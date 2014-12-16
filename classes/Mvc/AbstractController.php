<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Authentication\BaseHttpAuthentication;
use AppZap\PHPFramework\Mvc\View\AbstractView;
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
   * @var AbstractView
   */
  protected $response;

  /**
   * @var bool
   */
  protected $require_http_authentication = FALSE;

  /**
   * @param Request $request
   * @param AbstractView $response
   */
  public function __construct(Request $request, AbstractView $response) {
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
    if ($this->require_http_authentication) {
      $base_http_authentication = new BaseHttpAuthentication();
      $base_http_authentication->checkAuthentication();
    }
  }

  /**
   * @throws \Exception
   */
  public function handleNotSupportedMethod() {
    HttpStatus::set_status(HttpStatus::STATUS_405_METHOD_NOT_ALLOWED, [
        HttpStatus::HEADER_FIELD_ALLOW => join(', ', $this->getImplementedMethods())
    ]);
    HttpStatus::send_headers();
    throw new DispatchingInterruptedException('Request method not allowed', 1415268266);
  }

  /**
   * @return array
   */
  protected function getImplementedMethods() {
    $methods = ['options', 'get', 'head', 'post', 'put', 'delete'];
    $implemented_methods = [];
    foreach($methods as $method) {
      if (method_exists($this, $method)) {
        $implemented_methods[] = $method;
      }
    }
    return $implemented_methods;
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

  /**
   * @throws \Exception
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->handleNotSupportedMethod() instead
   */
  public function handle_not_supported_method() {
    $this->handleNotSupportedMethod();
  }

}
