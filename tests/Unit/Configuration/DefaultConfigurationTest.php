<?php
namespace AppZap\PHPFramework\Tests\Unit\Configuration;


use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Configuration\DefaultConfiguration;

class DefaultConfigurationTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    $_ENV['AppZap\PHPFramework\ProjectRoot'] = __DIR__;
    Configuration::reset();
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\ApplicationPartMissingException
   * @expectedExceptionCode 1410538265
   */
  public function notExistingApplicationFolder() {
    DefaultConfiguration::initialize('not_existing');
  }

}