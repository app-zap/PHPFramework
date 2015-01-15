<?php
namespace AppZap\PHPFramework\Tests\Unit\Authentication;

use AppZap\PHPFramework\Authentication\BaseCryptCookieSession;

class BaseCryptCookieSessionTest extends \PHPUnit_Framework_TestCase {

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Authentication\BaseCryptCookieSessionException
   * @expectedExceptionCode 1415264244
   */
  public function contructorWithoutSettingEncryptKey() {
    new BaseCryptCookieSession();
  }

}