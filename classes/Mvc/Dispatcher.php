<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Cache\CacheFactory;
use AppZap\PHPFramework\Mvc\View\TwigView;
use AppZap\PHPFramework\SignalSlot\Dispatcher as SignalSlotDispatcher;

/**
 * Main entrance class for the framework / application
 *
 * @author Knut Ahlers
 */
class Dispatcher {

  const SIGNAL_CONSTRUCT = 1415092297;
  const SIGNAL_OUTPUT_READY = 1413366871;
  const SIGNAL_START_DISPATCHING = 1415798962;

  /**
   * @var \Nette\Caching\Cache
   */
  protected $cache;

  /**
   * @var string
   */
  protected $request_method;

  /**
   * @var string
   */
  protected $routefile;

  /**
   * @throws ApplicationPartMissingException
   */
  public function __construct() {
    SignalSlotDispatcher::emitSignal(self::SIGNAL_CONSTRUCT);
    $this->cache = CacheFactory::getCache();
    $this->determineRequestMethod();
  }

  /**
   * @return string
   */
  public function getRequestMethod() {
    return $this->request_method;
  }

  /**
   * @param $uri
   * @return string
   */
  public function dispatch($uri) {
    $output = NULL;
    SignalSlotDispatcher::emitSignal(self::SIGNAL_START_DISPATCHING, $output, $uri, $this->request_method);
    if (is_null($output)) {
      $output = $this->dispatchUncached($uri);
    };
    SignalSlotDispatcher::emitSignal(self::SIGNAL_OUTPUT_READY, $output, $uri, $this->request_method);
    return $output;
  }

  /**
   *
   */
  protected function determineRequestMethod() {
    if (isset($_ENV['AppZap\PHPFramework\RequestMethod'])) {
      $this->request_method = $_ENV['AppZap\PHPFramework\RequestMethod'];
    } elseif (php_sapi_name() === 'cli') {
      $this->request_method = 'cli';
    } else {
      $this->request_method = strtolower($_SERVER['REQUEST_METHOD']);
    }
  }

  /**
   * @param AbstractController $controller
   * @return string
   */
  protected function determineDefaultTemplateName(AbstractController $controller) {
    if (preg_match('|\\\\([a-zA-Z0-9]{2,50})Controller$|', get_class($controller), $matches)) {
      return $controller->getTemplateName($matches[1]);
    }
    return NULL;
  }

  /**
   * @param $uri
   * @return Router
   */
  protected function getRouter($uri) {
    $router = $this->cache->load('router_' . $uri . '_' . $this->request_method, function () use ($uri) {
      return new Router($uri);
    });
    return $router;
  }

  /**
   * @param $uri
   * @return string
   */
  protected function dispatchUncached($uri) {
    $router = $this->getRouter($uri);
    if (is_callable($router->get_responder())) {
      $output = $this->dispatchCallable($router);
    } else {
      $output = $this->dispatchController($router);
    }
    return $output;
  }

  /**
   * @param Router $router
   * @return string
   */
  protected function dispatchCallable(Router $router) {
    return call_user_func($router->get_responder(), $router->get_parameters());
  }

  /**
   * @param Router $router
   * @return string
   */
  protected function dispatchController(Router $router) {
    $responder = $router->get_responder();
    $parameters = $router->get_parameters();
    $request = new Request($this->request_method);
    $response = new TwigView();

    try {
      /** @var AbstractController $controller */
      $controller = new $responder($request, $response);
      if (!method_exists($controller, $this->request_method)) {
        // Send HTTP 405 response
        $controller->handleNotSupportedMethod($this->request_method);
      }
      $default_template_name = $this->determineDefaultTemplateName($controller);
      if ($default_template_name) {
        $response->set_template_name($default_template_name);
      }
      $controller->setParameters($parameters);
      $controller->initialize();
      $output = $controller->{$this->request_method}($parameters);
      if (is_null($output)) {
        $output = $response->render();
      }
      return $output;
    } catch (DispatchingInterruptedException $e) {
      $output = '';
    }
    return $output;
  }

  /**
   * @return string
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->getRequestMethod() instead
   */
  public function get_request_method() {
    return $this->getRequestMethod();
  }

}
