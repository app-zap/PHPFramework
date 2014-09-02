<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Configuration\Configuration;

class Router {

  /**
   * @var array
   */
  protected $parameters;

  /**
   * @var string
   */
  protected $responder_class;

  /**
   * @return array
   */
  public function get_parameters() {
    return $this->parameters;
  }

  /**
   * @return string
   */
  public function get_responder_class() {
    return $this->responder_class;
  }

  /**
   * @param $uri
   * @throws ApplicationPartMissingException
   * @throws InvalidHttpResponderException
   */
  public function __construct($uri) {
    $application_configuration = Configuration::getSection('application');
    if (!is_readable($application_configuration['routes_file'])) {
      throw new ApplicationPartMissingException('Routes file "' . $application_configuration['routes_file'] . '" does not exist.');
    }

    $routes = include($application_configuration['routes_file']);

    $uri = preg_replace('/\?.*$/', '', $uri);

    $responder_class = NULL;
    $parameters = [];
    foreach ($routes as $regex => $class) {
      if (preg_match($regex, $uri, $matches)) {
        $responder_class = $class;
        for ($i = 1; $i < count($matches); $i++) {
          $parameters[] = $matches[$i];
        }
        break;
      }
    }

    // If the class does not exist throw an exception
    if (!class_exists($responder_class, TRUE)) {
      throw new InvalidHttpResponderException('Handler class ' . $responder_class . ' for uri ' . $uri . ' not found!');
    }
    $this->responder_class = $responder_class;
    $this->parameters = $parameters;
  }
}