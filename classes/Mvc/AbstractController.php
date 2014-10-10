<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Authentication\BaseHttpAuthentication;
use AppZap\PHPFramework\Mvc\View\AbstractView;

abstract class AbstractController {

  /**
   * @var BaseHttpRequest
   */
  protected $request = null;

  /**
   * @var AbstractView
   */
  protected $response = null;

  /**
   * @var bool
   */
  protected $require_http_authentication = FALSE;

  /**
   * @param BaseHttpRequest $request
   * @param AbstractView $response
   */
  public function __construct(BaseHttpRequest $request, AbstractView $response) {
    $this->request = $request;
    $this->response = $response;
  }

  /**
   * @param array $params
   */
  public function initialize($params) {
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
    die();
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
