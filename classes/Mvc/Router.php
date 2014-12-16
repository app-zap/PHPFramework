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
    $routesFile = Configuration::get('application', 'routes_file');
    if (!is_readable($routesFile)) {
      throw new ApplicationPartMissingException('Routes file "' . $routesFile . '" does not exist.', 1415134009);
    }
    $routes = include($routesFile);
    if (!is_array($routes)) {
      throw new InvalidHttpResponderException('The routes file did not return an array with routes', 1415135585);
    }
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
   * @param $resource
   * @param $routes
   */
  protected function route($routes, $resource) {
    $resource = ltrim($resource, '/');
    foreach ($routes as $regex => $regex_responder) {
      $regex = $this->enhanceRegex($regex);
      if (preg_match($regex, $resource, $matches)) {
        $matches_count = count($matches);
        for ($i = 1; $i < $matches_count; $i++) {
          $this->parameters[] = $matches[$i];
        }
        if (is_array($regex_responder)) {
          $this->route($regex_responder, preg_replace($regex, '', $resource));
        } else {
          $this->responder = $regex_responder;
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
      $regex = str_replace('?', '([a-z0-9]*)', $regex);
      $regex = '|' . $regex . '|';
    }
    return $regex;
  }

  /**
   * @return array
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->getParameters() instead
   */
  public function get_parameters() {
    return $this->getParameters();
  }

  /**
   * @return mixed
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->getResponder() instead
   */
  public function get_responder() {
    return $this->getResponder();
  }

}
