<?php
namespace AppZap\PHPFramework\Tests\Unit\Authentication;

use AppZap\PHPFramework\Authentication\BaseCryptCookieSession;
use AppZap\PHPFramework\Configuration\Configuration;

class BaseCryptCookieSessionTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    Configuration::set('phpframework', 'authentication.cookie.encrypt_key', '0123467890123456');
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Authentication\BaseCryptCookieSessionException
   * @expectedExceptionCode 1415264244
   */
  public function constructWithoutSettingEncryptKey() {
    Configuration::set('phpframework', 'authentication.cookie.encrypt_key', NULL);
    new BaseCryptCookieSession();
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Authentication\BaseCryptCookieSessionException
   * @expectedExceptionCode 1421849111
   */
  public function constructWithWrongKeySize() {
    Configuration::set('phpframework', 'authentication.cookie.encrypt_key', '1234');
    new BaseCryptCookieSession();
  }

  /**
   * @test
   */
  public function roundTripValue() {
    $cookieSession = new BaseCryptCookieSession();
    $cookieSession->injectSetCookieFunction(function(){});
    $cookieSession->set('foo', 'bar');
    $this->assertEquals('bar', $cookieSession->get('foo'));
  }

  /**
   * @test
   */
  public function keyExists() {
    $cookieSession = new BaseCryptCookieSession();
    $cookieSession->injectSetCookieFunction(function(){});
    $this->assertFalse($cookieSession->exist('foo'));
    $cookieSession->set('foo', 'bar');
    $this->assertTrue($cookieSession->exist('foo'));
  }

  /**
   * @test
   */
  public function clear() {
    $cookieSession = new BaseCryptCookieSession();
    $cookieSession->injectSetCookieFunction(function(){});
    $cookieSession->set('foo', 'bar');
    $cookieSession->set('foo2', 'bar2');
    $this->assertTrue($cookieSession->exist('foo'));
    $cookieSession->clear('foo');
    $this->assertFalse($cookieSession->exist('foo'));
    $this->assertTrue($cookieSession->exist('foo2'));
    $cookieSession->clearAll();
    $this->assertFalse($cookieSession->exist('foo2'));
  }

}
