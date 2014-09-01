<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Cache\CacheFactory;
use AppZap\PHPFramework\Configuration\Configuration;
use Nette\Caching\Cache;

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
  protected $routefile;

  /**
   * @throws ApplicationPartMissingException
   */
  public function __construct() {
    $this->cache = CacheFactory::getCache();
    $application_configuration = Configuration::getSection('application');

    if (!is_dir($application_configuration['application_directory'])) {
      throw new ApplicationPartMissingException('Application directory "' . $application_configuration['application_directory'] . '" does not exist.');
    }

    if (!is_dir($application_configuration['templates_directory'])) {
      throw new ApplicationPartMissingException('Template directory "' . $application_configuration['templates_directory'] . '" does not exist.');
    }
  }

  public function dispatch($uri) {

    $request_method = $this->determineRequestMethod();
    $output = NULL;
    if ($request_method === 'get') {
      $output = $this->cache->load('output_' . $uri);
    }

    if (is_null($output)) {
      $router = $this->getRouter($uri);
      $responder_class = $router->get_responder_class();
      $parameters = $router->get_parameters();

      $request = new BaseHttpRequest($request_method);
      $response = new BaseHttpResponse();

      $default_template_name = $this->determineDefaultTemplateName($responder_class);
      if ($default_template_name) {
        $response->set_template_name($default_template_name);
      }

      /** @var BaseHttpHandler $request_handler */
      $request_handler = new $responder_class($request, $response);

      try {
        if (!method_exists($request_handler, $request_method)) {
          throw new MethodNotSupportedException('Method ' . $request_method . ' is not valid for ' . $responder_class);
        }
        $request_handler->initialize($parameters);
        $output = $request_handler->$request_method($parameters);
        if (is_null($output)) {
          $output = $response->render();
        }
      } catch(MethodNotImplementedException $e) {
        header("HTTP/1.0 405 Method Not Allowed");
        die();
      }
    };

    if (Configuration::get('cache', 'full_output_cache', FALSE) && $request_method === 'get') {
      $this->cache->save('output_' . $uri, $output, [
        Cache::EXPIRE => Configuration::get('cache', 'full_output_expiration', '20 Minutes'),
      ]);
    }

    echo $output;
  }

  /**
   * @return string
   */
  protected function determineRequestMethod() {
    if (php_sapi_name() == 'cli') {
      $method = 'cli';
      return $method;
    } else {
      $method = strtolower($_SERVER['REQUEST_METHOD']);
      return $method;
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
    $request_method = $this->determineRequestMethod();
    $router = $this->cache->load('router_' . $uri . '_' . $request_method, function () use ($uri) {
      return new Router($uri);
    });
    return $router;
  }

}

class ApplicationPartMissingException extends \Exception {
}

class InvalidHttpResponderException extends \Exception {
}
