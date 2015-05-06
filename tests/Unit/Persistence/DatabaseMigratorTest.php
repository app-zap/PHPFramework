<?php

namespace AppZap\PHPFramework\Tests\Unit\Persistence;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Persistence\DatabaseConnection;
use AppZap\PHPFramework\Persistence\DatabaseMigrator;
use AppZap\PHPFramework\Persistence\StaticDatabaseConnection;

class DatabaseMigratorTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var string
   */
  protected $basePath;

  /**
   * @var DatabaseConnection
   */
  protected $db;

  public function setUp() {
    Configuration::reset();
    $database = 'phpunit_tests';
    $host = '127.0.0.1';
    $password = '';
    $user = 'travis';
    Configuration::set('phpframework', 'db.mysql.database', $database);
    Configuration::set('phpframework', 'db.mysql.host', $host);
    Configuration::set('phpframework', 'db.mysql.password', $password);
    Configuration::set('phpframework', 'db.mysql.user', $user);
    $this->basePath = dirname(__FILE__);
    $this->db = StaticDatabaseConnection::getInstance();
    try {
      $this->db->connect();
    } catch (\PDOException $e) {
      $this->markTestSkipped('no database connection');
    }
    $this->db->query('DROP TABLE IF EXISTS `migration_ver`');
    $this->db->query('DROP TABLE IF EXISTS `migrator_test_1`');
    $this->db->query('DROP TABLE IF EXISTS `migrator_test_2`');
    $this->db->query('DROP TABLE IF EXISTS `migrator_test_error`');
    $this->db->query('DROP TABLE IF EXISTS `migrator_test_timestamps`');
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Persistence\DatabaseMigratorException
   * @expectedExceptionCode 1415085595
   */
  public function failsIfNoDirectoryConfigured() {
    new DatabaseMigrator();
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Persistence\DatabaseMigratorException
   * @expectedExceptionCode 1415085126
   */
  public function failsIfDirectoryDoesntExist() {
    Configuration::set('phpframework', 'db.migrator.directory', $this->basePath . '/this/doesnt/exist/');
    new DatabaseMigrator();
  }

  /**
   * @test
   */
  public function noErrorOccursIfDirectoryIsEmpty() {
    Configuration::set('phpframework', 'db.migrator.directory', $this->basePath . '/_migrator/_empty/');
    (new DatabaseMigrator())->migrate();
  }

  /**
   * @test
   */
  public function noErrorOccursIfDirectoryHasNoValidFiles() {
    Configuration::set('phpframework', 'db.migrator.directory', $this->basePath . '/_migrator/_invalid_filenames/');
    (new DatabaseMigrator())->migrate();
  }

  /**
   * @test
   */
  public function migrationsAreExecuted() {
    Configuration::set('phpframework', 'db.migrator.directory', $this->basePath . '/_migrator/_1/');
    (new DatabaseMigrator())->migrate();
    $this->assertSame(1, count($this->db->query("SHOW TABLES LIKE 'migrator_test_1'")));
    $this->assertSame(1, $this->db->count('migrator_test_1', ['title' => 'test2']));
    $this->assertSame(2, $this->db->count('migrator_test_1'));
  }

  /**
   * @test
   */
  public function migrationsAreExecutedFilesStartAt2() {
    Configuration::set('phpframework', 'db.migrator.directory', $this->basePath . '/_migrator/_2/');
    (new DatabaseMigrator())->migrate();
    $this->assertSame(1, count($this->db->query("SHOW TABLES LIKE 'migrator_test_2'")));
    $this->assertSame(1, $this->db->count('migrator_test_2', ['title' => 'test2']));
    $this->assertSame(2, $this->db->count('migrator_test_2'));
  }

  /**
   * @test
   */
  public function migrationsAreExecutedFilesUseTimestamps() {
    Configuration::set('phpframework', 'db.migrator.directory', $this->basePath . '/_migrator/_timestamps/');
    (new DatabaseMigrator())->migrate();
    $this->assertSame(1, count($this->db->query("SHOW TABLES LIKE 'migrator_test_timestamps'")));
    $this->assertSame(1, $this->db->count('migrator_test_timestamps', ['title' => 'test2']));
    $this->assertSame(2, $this->db->count('migrator_test_timestamps'));
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Persistence\DatabaseMigratorException
   * @expectedExceptionCode 1415089456
   */
  public function rollbackOnError() {
    Configuration::set('phpframework', 'db.migrator.directory', $this->basePath . '/_migrator/_error/');
    try {
      (new DatabaseMigrator())->migrate();
    } catch (\Exception $e) {
      $this->assertSame(1, count($this->db->query("SHOW TABLES LIKE 'migrator_test_error'")));
      $this->assertSame(1, $this->db->count('migrator_test_error', ['title' => 'test1']));
      $this->assertSame(1, $this->db->count('migrator_test_error'));
      throw $e;
    }
  }

}