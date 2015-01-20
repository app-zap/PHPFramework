<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Authentication\BaseHttpAuthentication;
use AppZap\PHPFramework\Mvc\View\ViewInterface;
use AppZap\PHPFramework\SignalSlot\Dispatcher as SignalSlotDispatcher;

abstract class AbstractController {

  const SIGNAL_INIT_REQUEST = 1413325732;
  const SIGNAL_INIT_RESPONSE = 1413325748;

  /**
   * @var array
   */
  protected $parameters;

  /**
   * @var Request
   */
  protected $request;

  /**
   * @var ViewInterface
   */
  protected $response;

  /**
   * @var bool
   */
  protected $requireHttpAuthentication = FALSE;

  /**
   * @param Request $request
   * @param ViewInterface $response
   */
  public function __construct(Request $request, ViewInterface $response) {
    SignalSlotDispatcher::emitSignal(self::SIGNAL_INIT_REQUEST, $request);
    SignalSlotDispatcher::emitSignal(self::SIGNAL_INIT_RESPONSE, $response);
    $this->request = $request;
    $this->response = $response;
  }

  /**
   * @param array $parameters
   */
  public function setParameters($parameters) {
    $this->parameters = $parameters;
  }

  /**
   * @throws \AppZap\PHPFramework\Authentication\HttpAuthenticationRequiredException
   */
  public function initialize() {
    if ($this->requireHttpAuthentication) {
      $baseHttpAuthentication = new BaseHttpAuthentication();
      $baseHttpAuthentication->checkAuthentication();
    }
  }

  /**
   * @throws \Exception
   */
  public function handleNotSupportedMethod() {
    HttpStatus::setStatus(HttpStatus::STATUS_405_METHOD_NOT_ALLOWED, [
        HttpStatus::HEADER_FIELD_ALLOW => join(', ', $this->getImplementedMethods())
    ]);
    HttpStatus::sendHeaders();
    throw new DispatchingInterruptedException('Request method not allowed', 1415268266);
  }

  /**
   * @return array
   */
  protected function getImplementedMethods() {
    $methods = ['options', 'get', 'head', 'post', 'put', 'delete'];
    $implementedMethods = [];
    foreach($methods as $method) {
      if (method_exists($this, $method)) {
        $implementedMethods[] = $method;
      }
    }
    return $implementedMethods;
  }

  /**
   * Can be used to alter/prefix the default template name (derived from the name of the controller)
   *
   * @param string $defaultTemplateName
   * @return string
   */
  public function getTemplateName($defaultTemplateName) {
    return $defaultTemplateName;
  }

}
