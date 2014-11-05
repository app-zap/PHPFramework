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
    $session_class = Configuration::get('phpframework', 'authentication.sessionclass', 'BasePHPSession');
    if (!class_exists($session_class, TRUE)) {
      $session_class = $this->default_session_class_namespace . '\\' . $session_class;
    }
    if(class_exists($session_class, TRUE)) {
      $this->session = new $session_class();
      if(!($this->session instanceof BaseSessionInterface)) {
        $this->session = null;
        throw new BaseSessionException($session_class . ' is not an instance of AppZap\PHPFramework\Authentication\BaseSessionInterface', 1409732473);
      }
    } else {
      throw new BaseSessionException('Session class ' . $session_class . ' not found', 1409732479);
    }
  }

}
