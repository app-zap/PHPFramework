<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Cache\CacheFactory;
use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Cache\Cache;
use AppZap\PHPFramework\Mvc\View\TwigView;
use AppZap\PHPFramework\SignalSlot\Dispatcher as SignalSlotDispatcher;

/**
 * Main entrance class for the framework / application
 *
 * @author Knut Ahlers
 */
class Dispatcher {

  const SIGNAL_OUTPUT_READY = 1413366871;

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
    $this->cache = CacheFactory::getCache();
    $this->determineRequestMethod();
  }

  /**
   * @return string
   */
  public function get_request_method() {
    return $this->request_method;
  }

  /**
   * @param string $uri
   */
  public function dispatch($uri) {

    $output = NULL;
    if ($this->request_method === 'get') {
      $output = $this->cache->load('output_' . $uri);
    }

    if (is_null($output)) {
      $router = $this->getRouter($uri);
      $responder_class = $router->get_responder_class();
      $parameters = $router->get_parameters();

      $request = new BaseHttpRequest($this->request_method);
      $response = new TwigView();

      $default_template_name = $this->determineDefaultTemplateName($responder_class);
      if ($default_template_name) {
        $response->set_template_name($default_template_name);
      }

      /** @var AbstractController $contoller */
      $contoller = new $responder_class($request, $response);
      if (!method_exists($contoller, $this->request_method)) {
        // Send HTTP 405 response
        $contoller->handle_not_supported_method($this->request_method);
      }
      $contoller->initialize($parameters);
      $output = $contoller->{$this->request_method}($parameters);
      if (is_null($output)) {
        $output = $response->render();
      }

    };

    SignalSlotDispatcher::emitSignal(self::SIGNAL_OUTPUT_READY, $output);

    if (Configuration::get('cache', 'full_output_cache', FALSE) && $this->request_method === 'get') {
      $this->cache->save('output_' . $uri, $output, [
        Cache::EXPIRE => Configuration::get('cache', 'full_output_expiration', '20 Minutes'),
      ]);
    }

    echo $output;
    return $output;
  }

  /**
   *
   */
  protected function determineRequestMethod() {
    if (isset($_ENV['AppZap\PHPFramework\RequestMethod'])) {
      $this->request_method = $_ENV['AppZap\PHPFramework\RequestMethod'];
    }
    elseif (php_sapi_name() === 'cli') {
      $this->request_method = 'cli';
    } else {
      $this->request_method = strtolower($_SERVER['REQUEST_METHOD']);
    }
  }

  /**
   * @param $responder_class
   * @return string
   */
  protected function determineDefaultTemplateName($responder_class) {
    if (preg_match('|\\\\([a-zA-Z0-9]{2,50})Controller$|', $responder_class, $matches)) {
      return $matches[1];
    }
    return NULL;
  }

  /**
   * @param $uri
   * @return mixed|NULL
   */
  protected function getRouter($uri) {
    $router = $this->cache->load('router_' . $uri . '_' . $this->request_method, function () use ($uri) {
      return new Router($uri);
    });
    return $router;
  }

}

class InvalidHttpResponderException extends \Exception {
}
