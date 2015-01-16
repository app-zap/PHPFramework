<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Configuration\Configuration;

class Router {

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
   * @throws ApplicationPartMissingException
   * @throws InvalidHttpResponderException
   */
  public function __construct($resource) {
    $routes = $this->collectRoutesDefinitions();
    $this->route($routes, $resource);

    // check if the responder is valid
    if (is_string($this->responder)) {
      if (!class_exists($this->responder)) {
        throw new InvalidHttpResponderException('Controller ' . $this->responder . ' for uri "' . $resource . '" not found!', 1415129223);
      }
    } elseif(!isset($this->responder)) {
      throw new InvalidHttpResponderException('Route ' . $resource . ' could not be routed.', 1415136995);
    } elseif (!is_callable($this->responder)) {
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
    $applicationRoutesFile = Configuration::get('application', 'routes_file');
    if (is_readable($applicationRoutesFile)) {
      $applicationRoutes = include($applicationRoutesFile);
      if (!is_array($applicationRoutes)) {
        throw new InvalidHttpResponderException('The routes file did not return an array with routes', 1415135585);
      }
      $routes = array_merge($routes, $applicationRoutes);
    }
    return $routes;
  }

  /**
   * @param array $routes
   * @param string $resource
   */
  protected function route($routes, $resource) {
    $resource = ltrim($resource, '/');
    foreach ($routes as $regex => $regexResponder) {
      $regex = $this->enhanceRegex($regex);
      if ($regexResponder !== FALSE && preg_match($regex, $resource, $matches)) {
        $matchesCount = count($matches);
        for ($i = 1; $i < $matchesCount; $i++) {
          $this->parameters[] = $matches[$i];
        }
        if (is_array($regexResponder)) {
          $this->route($regexResponder, preg_replace($regex, '', $resource));
        } else {
          $this->responder = $regexResponder;
        }
        break;
      }
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
