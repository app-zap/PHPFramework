<?php
namespace AppZap\PHPFramework\Tests\Authentication;

use AppZap\PHPFramework\Authentication\BaseSessionInterface;
use AppZap\PHPFramework\Authentication\BaseSessionUndefinedIndexException;
use AppZap\PHPFramework\Configuration\Configuration;

class AuthenticationService extends \AppZap\PHPFramework\Authentication\AuthenticationService {
}

class NullSession implements BaseSessionInterface {
  public function __construct() {
  }

  public function set($key, $value) {
  }

  public function get($key, $default = null) {
    return NULL;
  }

  public function exist($key) {
    return FALSE;
  }

  public function clear($key) {
  }

  public function clear_all() {
  }
}

class SessionNotImplementingTheInterface {
}

class AuthenticationServiceTest extends \PHPUnit_Framework_TestCase{

  /**
   * @test
   */
  public function construct() {
    Configuration::set('application', 'session.class', '\\AppZap\\PHPFramework\\Tests\\Authentication\\NullSession');
    new AuthenticationService();
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Authentication\BaseSessionException
   * @expectedExceptionCode 1409732473
   */
  public function construct_with_session_class_not_implementing_the_interface() {
    Configuration::set('application', 'session.class', '\\AppZap\\PHPFramework\\Tests\\Authentication\\SessionNotImplementingTheInterface');
    new AuthenticationService();
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Authentication\BaseSessionException
   * @expectedExceptionCode 1409732479
   */
  public function construct_with_not_existing_session_class() {
    Configuration::set('application', 'session.class', '\\AppZap\\PHPFramework\\Tests\\Authentication\\NotExisting');
    new AuthenticationService();
  }

}