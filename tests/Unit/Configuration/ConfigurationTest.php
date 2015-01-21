<?php
namespace AppZap\PHPFramework\Tests\Unit\Configuration;

use AppZap\PHPFramework\Configuration\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase {

  /**
   * @test
   */
  public function roundtripSingleValue() {
    Configuration::set('test', 'foo', 'bar');
    $this->assertSame('bar', Configuration::get('test', 'foo'));
  }

  /**
   * @test
   */
  public function getDefaultValue() {
    Configuration::reset();
    $this->assertSame('bar', Configuration::get('test', 'foo', 'bar'));
  }

  /**
   * @test
   */
  public function resetWorks() {
    Configuration::set('test', 'reset_works', TRUE);
    $this->assertTrue(Configuration::get('test', 'reset_works'));
    Configuration::reset();
    $this->assertNull(Configuration::get('test', 'reset_works'));
  }

  /**
   * @test
   */
  public function removeKeyWorks() {
    Configuration::set('test', 'key1', TRUE);
    Configuration::set('test', 'key2', TRUE);
    $this->assertTrue(Configuration::get('test', 'key1'));
    $this->assertTrue(Configuration::get('test', 'key2'));
    Configuration::remove('test', 'key1');
    $this->assertNull(Configuration::get('test', 'key1'));
    $this->assertTrue(Configuration::get('test', 'key2'));
  }

  /**
   * @test
   */
  public function getSection() {
    Configuration::reset();
    Configuration::set('test', 'key1', TRUE);
    Configuration::set('test', 'key2', TRUE);
    Configuration::set('othersection', 'key3', TRUE);
    $testSection = Configuration::getSection('test');
    $this->assertSame(2, count($testSection));
    $this->assertArrayNotHasKey('key3', $testSection);
  }

  /**
   * @test
   */
  public function getSectionNamespace() {
    Configuration::reset();
    Configuration::set('test', 'key1', FALSE);
    Configuration::set('test', 'ns1.key1', TRUE);
    Configuration::set('test', 'ns1.key2', FALSE);
    Configuration::set('test', 'ns2.key1', FALSE);
    Configuration::set('test', 'ns2.key2', FALSE);
    Configuration::set('test', 'ns3.subspace1.key1', TRUE);
    Configuration::set('test', 'ns3.key1', FALSE);
    Configuration::set('othersection', 'key1', FALSE);
    Configuration::set('othersection', 'ns1.key1', FALSE);
    Configuration::set('othersection', 'ns1.key2', FALSE);
    $testSection = Configuration::getSection('test', 'ns1');
    $this->assertTrue($testSection['key1']);
    $this->assertFalse($testSection['key2']);
    $testSection = Configuration::getSection('test', 'ns3.subspace1');
    $this->assertTrue($testSection['key1']);
  }

  /**
   * @test
   */
  public function getNonExistingSection() {
    Configuration::reset();
    $this->assertNull(Configuration::getSection('not_existing'));
  }

  /**
   * @test
   */
  public function removeSection() {
    Configuration::reset();
    Configuration::set('test', 'key1', TRUE);
    Configuration::set('test', 'key2', TRUE);
    Configuration::set('othersection', 'key3', TRUE);
    $testSection = Configuration::getSection('test');
    $this->assertSame(2, count($testSection));
    Configuration::removeSection('test');
    $this->assertNull(Configuration::getSection('test'));
    $this->assertSame(1, count(Configuration::getSection('othersection')));
  }

  /**
   * @test
   */
  public function getSectionDefaultValues() {
    Configuration::reset();
    Configuration::set('test', 'key1', 1);
    Configuration::set('test', 'key3', 3);
    $configuration = Configuration::getSection('test', '', ['key1' => FALSE, 'key2' => 2]);
    $this->assertSame(1, $configuration['key1']);
    $this->assertSame(2, $configuration['key2']);
    $this->assertSame(3, $configuration['key3']);
  }

  /**
   * @test
   */
  public function getSectionDefaultValuesNamespace() {
    Configuration::reset();
    Configuration::set('test', 'ns1.key1', 1);
    Configuration::set('test', 'ns2.key2', FALSE);
    Configuration::set('test', 'ns1.key3', 3);
    $configuration = Configuration::getSection('test', 'ns1', ['key1' => FALSE, 'key2' => 2]);
    $this->assertSame(1, $configuration['key1']);
    $this->assertSame(2, $configuration['key2']);
    $this->assertSame(3, $configuration['key3']);
  }

}
