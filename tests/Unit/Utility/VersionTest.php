<?php
namespace AppZap\PHPFramework\Tests\Unit\Utility;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Utility\Version;

class VersionTest extends \PHPUnit_Framework_TestCase {

  /**
   * @test
   */
  public function minimum_clean() {
    Configuration::set('phpframework', 'version', '2.0');
    $this->assertTrue(Version::minimum(0, 0));
    $this->assertTrue(Version::minimum(1, 0));
    $this->assertTrue(Version::minimum(1, 5));
    $this->assertTrue(Version::minimum(2, 0));
    $this->assertFalse(Version::minimum(2, 1));
    $this->assertFalse(Version::minimum(3, 0));
    Configuration::set('phpframework', 'version', '12.5');
    $this->assertTrue(Version::minimum(0, 0));
    $this->assertTrue(Version::minimum(10, 5));
    $this->assertTrue(Version::minimum(12, 0));
    $this->assertTrue(Version::minimum(12, 4));
    $this->assertTrue(Version::minimum(12, 5));
    $this->assertFalse(Version::minimum(12, 6));
    $this->assertFalse(Version::minimum(13, 0));
  }

  /**
   * @test
   */
  public function minimum_dev() {
    Configuration::set('phpframework', 'version', '2.0-dev');
    $this->assertTrue(Version::minimum(1, 5));
    $this->assertTrue(Version::minimum(2, 0));
    $this->assertFalse(Version::minimum(2, 1));
    $this->assertFalse(Version::minimum(3, 0));
    Configuration::set('phpframework', 'version', '12.5-dev');
    $this->assertTrue(Version::minimum(10, 5));
    $this->assertTrue(Version::minimum(12, 5));
    $this->assertFalse(Version::minimum(12, 6));
    $this->assertFalse(Version::minimum(13, 0));
  }

  /**
   * @test
   */
  public function maximum_clean() {
    Configuration::set('phpframework', 'version', '2.0');
    $this->assertTrue(Version::maximum(2, 0));
    $this->assertTrue(Version::maximum(2, 5));
    $this->assertTrue(Version::maximum(3, 0));
    $this->assertFalse(Version::maximum(1, 99));
    $this->assertFalse(Version::maximum(0, 5));
    Configuration::set('phpframework', 'version', '12.5');
    $this->assertTrue(Version::maximum(12, 5));
    $this->assertTrue(Version::maximum(12, 99));
    $this->assertTrue(Version::maximum(13, 0));
    $this->assertFalse(Version::maximum(1, 99));
    $this->assertFalse(Version::maximum(12, 0));
  }

  /**
   * @test
   */
  public function maximum_dev() {
    Configuration::set('phpframework', 'version', '2.0-dev');
    $this->assertTrue(Version::maximum(2, 0));
    $this->assertTrue(Version::maximum(2, 5));
    $this->assertFalse(Version::maximum(1, 99));
    Configuration::set('phpframework', 'version', '12.5-dev');
    $this->assertTrue(Version::maximum(12, 5));
    $this->assertTrue(Version::maximum(12, 99));
    $this->assertFalse(Version::maximum(12, 0));
  }

}