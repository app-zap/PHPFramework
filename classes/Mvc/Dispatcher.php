<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Cache\CacheFactory;
use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Cache\Cache;

/**
 * Main entrance class for the framework / application
 *
 * @author Knut Ahlers
 */
class Dispatcher {

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
      $response = new BaseHttpResponse();

      $default_template_name = $this->determineDefaultTemplateName($responder_class);
      if ($default_template_name) {
        $response->set_template_name($default_template_name);
      }

      /** @var BaseHttpHandler $request_handler */
      $request_handler = new $responder_class($request, $response);
      if (!method_exists($request_handler, $this->request_method)) {
        // Send HTTP 405 response
        $request_handler->handle_not_supported_method($this->request_method);
      }
      $request_handler->initialize($parameters);
      $output = $request_handler->{$this->request_method}($parameters);
      if (is_null($output)) {
        $output = $response->render();
      }

    };

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
    if (preg_match('|\\\\([a-zA-Z0-9]{2,50})Handler$|', $responder_class, $matches)) {
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
