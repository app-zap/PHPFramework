<?php

namespace AppZap\PHPFramework\Tests\Unit\Persistence;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Persistence\DatabaseConnection;
use AppZap\PHPFramework\Persistence\DatabaseMigrator;
use AppZap\PHPFramework\Persistence\StaticDatabaseConnection;

class DatabaseMigratorMock extends DatabaseMigrator {
  public function _get_current_migration_version() {
    return $this->get_current_migration_version();
  }
}

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
    $this->basePath = dirname(__FILE__);
    $this->db = StaticDatabaseConnection::getInstance();
    $this->db->query('DROP TABLE IF EXISTS `migration_ver`');
    $this->db->query('DROP TABLE IF EXISTS `migrator_test_1`');
    $this->db->query('DROP TABLE IF EXISTS `migrator_test_2`');
    $this->db->query('DROP TABLE IF EXISTS `migrator_test_error`');
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
  public function currentMigrationVersionIs0IfTableDoesntExist() {
    Configuration::set('phpframework', 'db.migrator.directory', $this->basePath . '/_migrator/_1/');
    $migrator = new DatabaseMigratorMock();
    $this->assertSame(0, $migrator->_get_current_migration_version());
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
    $this->markTestIncomplete('Skipping numbers seems not supported yet.');
    Configuration::set('phpframework', 'db.migrator.directory', $this->basePath . '/_migrator/_2/');
    (new DatabaseMigrator())->migrate();
    $this->assertSame(1, count($this->db->query("SHOW TABLES LIKE 'migrator_test_2'")));
    $this->assertSame(1, $this->db->count('migrator_test_2', ['title' => 'test2']));
    $this->assertSame(2, $this->db->count('migrator_test_2'));
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