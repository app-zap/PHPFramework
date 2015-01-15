<?php
namespace AppZap\PHPFramework\Tests\Unit\Configuration\Parser;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Configuration\DefaultConfiguration;
use AppZap\PHPFramework\Configuration\Parser\IniParser;

class IniParserTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    $_ENV['AppZap\PHPFramework\ProjectRoot'] = __DIR__;
    Configuration::reset();
  }

  /**
   * @test
   */
  public function applicationFolderWithoutIniFiles() {
    DefaultConfiguration::initialize('_nofiles');
    IniParser::initialize();
  }

  /**
   * @test
   */
  public function applicationFolderOnlyGlobalFile() {
    DefaultConfiguration::initialize('_onlyglobal');
    IniParser::initialize();
    $this->assertSame('bar', Configuration::get('unittest', 'foo'));
  }

  /**
   * @test
   */
  public function applicationFolderOnlyLocalFile() {
    DefaultConfiguration::initialize('_onlyglobal');
    IniParser::initialize();
    $this->assertSame('bar', Configuration::get('unittest', 'foo'));
  }

  /**
   * @test
   */
  public function applicationFolderBothFiles() {
    DefaultConfiguration::initialize('_both');
    IniParser::initialize();
    $this->assertSame('b', Configuration::get('unittest', 'foo'));
    $this->assertSame('42', Configuration::get('unittest', 'bar'));
    $this->assertNull(Configuration::get('unittest', 'baz'));
  }


}