<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Cache\CacheFactory;
use AppZap\PHPFramework\Mvc\Routing\Router;
use AppZap\PHPFramework\SignalSlot\Dispatcher as SignalSlotDispatcher;

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
  public function get_request_method() {
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
      $output = $this->dispatch_uncached($uri);
    };
    SignalSlotDispatcher::emitSignal(self::SIGNAL_OUTPUT_READY, $output, $uri, $this->request_method);
    return $output;
  }

  /**
   * @param $uri
   * @return string
   */
  protected function dispatch_uncached($uri) {
    $router = $this->getRouter($uri);
    $request = new Request($this->request_method, $router->get_parameters());
    return $router->get_responder()->dispatch($request);
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
   * @param $uri
   * @return Router
   */
  protected function getRouter($uri) {
    $router = $this->cache->load('router_' . $uri . '_' . $this->request_method, function () use ($uri) {
      return new Router($uri);
    });
    return $router;
  }

}
