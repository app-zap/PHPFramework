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
  protected $params;

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
   */
  public function setRequest(Request $request) {
    SignalSlotDispatcher::emitSignal(self::SIGNAL_INIT_REQUEST, $request);
    $this->request = $request;
  }

  /**
   * @param AbstractView $response
   */
  public function setResponse(AbstractView $response) {
    SignalSlotDispatcher::emitSignal(self::SIGNAL_INIT_RESPONSE, $response);
    $this->response = $response;
  }

  /**
   * @param array $params
   */
  public function initialize($params) {
    $this->params = $params;
    if ($this->require_http_authentication) {
      $base_http_authentication = new BaseHttpAuthentication();
      $base_http_authentication->check_authentication();
    }
  }

  /**
   * @throws \Exception
   */
  public function handle_not_supported_method() {
    HttpStatus::set_status(HttpStatus::STATUS_405_METHOD_NOT_ALLOWED, [
        HttpStatus::HEADER_FIELD_ALLOW => join(', ', $this->get_implemented_methods())
    ]);
    HttpStatus::send_headers();
    throw new DispatchingInterruptedException('Request method not allowed', 1415268266);
  }

  /**
   * @return array
   */
  protected function get_implemented_methods() {
    $methods = ['options', 'get', 'head', 'post', 'put', 'delete'];
    $implemented_methods = [];
    foreach($methods as $method) {
      if (method_exists($this, $method)) {
        $implemented_methods[] = $method;
      }
    }
    return $implemented_methods;
  }

}
