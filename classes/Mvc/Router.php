<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Configuration\Configuration;

class Router {

  /**
   * @var array
   */
  protected $parameters;

  /**
   * @var mixed
   */
  protected $responder;

  /**
   * @return array
   */
  public function get_parameters() {
    return $this->parameters;
  }

  /**
   * @return mixed
   */
  public function get_responder() {
    return $this->responder;
  }

  /**
   * @param $uri
   * @throws ApplicationPartMissingException
   * @throws InvalidHttpResponderException
   */
  public function __construct($uri) {
    $routes = include(Configuration::get('application', 'routes_file'));

    $uri = preg_replace('/\?.*$/', '', $uri);

    $responder = NULL;
    $parameters = [];
    foreach ($routes as $regex => $class) {
      if (preg_match($regex, $uri, $matches)) {
        $responder = $class;
        for ($i = 1; $i < count($matches); $i++) {
          $parameters[] = $matches[$i];
        }
        break;
      }
    }

    // If the class does not exist throw an exception
    if (is_string($responder) && !class_exists($responder, TRUE)) {
      throw new InvalidHttpResponderException('Controller ' . $responder . ' for uri ' . $uri . ' not found!');
    }
    $this->responder = $responder;
    $this->parameters = $parameters;
  }
}