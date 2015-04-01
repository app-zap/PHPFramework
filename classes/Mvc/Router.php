<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Http\HttpErrorException;
use AppZap\PHPFramework\SignalSlot\Dispatcher as SignalSlotDispatcher;

class Router {

  const SIGNAL_ROUTE_DEFINITIONS = 1421442000;

  /**
   * @var array
   */
  protected $parameters = [];

  /**
   * @var mixed
   */
  protected $responder;

  /**
   * @param string $resource
   * @throws HttpErrorException
   * @throws InvalidHttpResponderException
   */
  public function __construct($resource) {
    $routes = $this->collectRoutesDefinitions();
    $this->route($routes, $resource);

    if (!isset($this->responder)) {
      HttpStatus::setStatus(HttpStatus::STATUS_404_NOT_FOUND);
      throw new HttpErrorException(sprintf('%d: Resource \'%s\' not routable', 404, $resource), 404);
    }

    if (is_string($this->responder) && !class_exists($this->responder)) {
      throw new InvalidHttpResponderException('Controller ' . $this->responder . ' for uri "' . $resource . '" not found!', 1415129223);
    }

    if (!is_string($this->responder) && !is_callable($this->responder)) {
      throw new InvalidHttpResponderException('The responder must either be a class string, a callable or an array of subpaths', 1415129333);
    }
  }

  /**
   * @return array
   * @throws InvalidHttpResponderException
   */
  protected function collectRoutesDefinitions() {
    $path = __DIR__ . '/../../core_routes.php';
    $path = realpath($path);
    $routes = include($path);
    SignalSlotDispatcher::emitSignal(self::SIGNAL_ROUTE_DEFINITIONS, $routes);
    $applicationRoutesFile = Configuration::get('application', 'routes_file');
    if (is_readable($applicationRoutesFile)) {
      $applicationRoutes = include($applicationRoutesFile);
      if (!is_array($applicationRoutes)) {
        throw new InvalidHttpResponderException('The routes file did not return an array with routes', 1415135585);
      }
      $routes = $applicationRoutes + $routes;
    }
    return $routes;
  }

  /**
   * @param array $routes
   * @param mixed $resource
   */
  protected function route($routes, $resource) {
    if (is_int($resource)) {
      $this->routeHttpStatusCode($routes, $resource);
      return;
    }
    $resource = ltrim($resource, '/');
    foreach ($routes as $regex => $responder) {
      if ($responder === FALSE) {
        continue;
      }
      $regex = $this->enhanceRegex($regex);
      if (preg_match($regex, $resource, $matches)) {
        $matchesCount = count($matches);
        for ($i = 1; $i < $matchesCount; $i++) {
          $this->parameters[] = $matches[$i];
        }
        if (is_array($responder)) {
          $this->route($responder, preg_replace($regex, '', $resource));
        } else {
          $this->responder = $responder;
        }
        break;
      }
    }
  }

  /**
   * @param array $routes
   * @param int $httpStatusCode
   */
  protected function routeHttpStatusCode($routes, $httpStatusCode) {
    if (isset($routes[$httpStatusCode])) {
      $this->responder = $routes[$httpStatusCode];
      return;
    }
    // convert e.g. 405 to 400
    $baseStatusCode = (int) floor($httpStatusCode / 100) * 100;
    if (isset($routes[$baseStatusCode])) {
      $this->responder = $routes[$baseStatusCode];
      return;
    }
  }

  /**
   * @return array
   */
  public function getParameters() {
    return $this->parameters;
  }

  /**
   * @return mixed
   */
  public function getResponder() {
    return $this->responder;
  }

  /**
   * @param string $regex
   * @return string
   */
  protected function enhanceRegex($regex) {
    set_error_handler(function() {}, E_WARNING);
    $isRegularExpression = preg_match($regex, "") !== FALSE;
    restore_error_handler();
    if (!$isRegularExpression) {
      // if $regex is no regular expression
      if ($regex === '.') {
        $regex = '';
      }
      if (!$regex || $regex{0} !== '%') {
        $regex = '^' . $regex;
      } else {
        $regex = substr($regex, 1);
      }
      if (!$regex || $regex{strlen($regex) - 1} !== '%') {
        $regex .= '$';
      } else {
        $regex = substr($regex, 0, strlen($regex) - 1);
      }
      $regex = str_replace('?', '([a-zA-Z0-9]*)', $regex);
      $regex = '|' . $regex . '|';
    }
    return $regex;
  }

}
