<?php
namespace AppZap\PHPFramework\Mvc\Routing;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mvc\ApplicationPartMissingException;
use AppZap\PHPFramework\Mvc\InvalidHttpResponderException;
use AppZap\PHPFramework\Mvc\Responder\AbstractResponder;
use AppZap\PHPFramework\Mvc\Responder\ResponderFactory;

class Router {

  /**
   * @var array
   */
  protected $parameters = [];

  /**
   * @var AbstractResponder
   */
  protected $responder;

  /**
   * @return array
   */
  public function get_parameters() {
    return $this->parameters;
  }

  /**
   * @return AbstractResponder
   */
  public function get_responder() {
    return $this->responder;
  }

  /**
   * @param string $resource
   * @throws ApplicationPartMissingException
   * @throws InvalidHttpResponderException
   */
  public function __construct($resource) {
    $routes_file = Configuration::get('application', 'routes_file');
    if (!is_readable($routes_file)) {
      throw new ApplicationPartMissingException('Routes file "' . $routes_file . '" does not exist.', 1415134009);
    }
    $routes = include($routes_file);
    if (!is_array($routes)) {
      throw new InvalidHttpResponderException('The routes file did not return an array with routes', 1415135585);
    }
    $this->responder = $this->route($routes, $resource);
  }

  /**
   * @param $resource
   * @param $routes
   */
  protected function route($routes, $resource) {
    $resource = ltrim($resource, '/');
    foreach ($routes as $regex => $responderDefinition) {
      $parameters = EnhancedRegularExpressionMatcher::match($regex, $resource);
      if (is_array($parameters)) {
        $this->parameters = $parameters;
        $responder = ResponderFactory::createResponderFromDefinition($responderDefinition);
        $responder->setMatchedRegularExpression($regex);
        $responder->setResource($resource);
        return $responder;
      }
    }
    throw new InvalidHttpResponderException('Route ' . $resource . ' could not be routed.', 1415136995);
  }

}
