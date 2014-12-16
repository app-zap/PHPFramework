<?php
namespace AppZap\PHPFramework\Authentication;

use AppZap\PHPFramework\Configuration\Configuration;

abstract class AuthenticationService {

  /**
   * @var string
   */
  protected $default_session_class_namespace = 'AppZap\PHPFramework\Authentication';

  /**
   * @var BaseSessionInterface
   */
  protected $session;

  /**
   *
   */
  public function __construct() {
    $sessionClass = Configuration::get('phpframework', 'authentication.sessionclass', 'BasePHPSession');
    if (!class_exists($sessionClass)) {
      $sessionClass = $this->default_session_class_namespace . '\\' . $sessionClass;
    }
    if(class_exists($sessionClass)) {
      $this->session = new $sessionClass();
      if(!($this->session instanceof BaseSessionInterface)) {
        $this->session = null;
        throw new BaseSessionException($sessionClass . ' is not an instance of AppZap\PHPFramework\Authentication\BaseSessionInterface', 1409732473);
      }
    } else {
      throw new BaseSessionException('Session class ' . $sessionClass . ' not found', 1409732479);
    }
  }

}
