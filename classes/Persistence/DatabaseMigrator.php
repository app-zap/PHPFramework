<?php
namespace AppZap\PHPFramework\Persistence;

use AppZap\PHPFramework\Configuration\Configuration;

class DatabaseMigrator {

  /**
   * @var DatabaseConnection
   */
  protected $db;

  /**
   * @var string
   */
  protected $migrationDirectory;

  /**
   * @var int
   */
  protected $lastExecutedVersion = 0;

  /**
   * @throws DatabaseMigratorException
   */
  public function __construct() {
    $this->db = StaticDatabaseConnection::getInstance();
    $this->migrationDirectory = Configuration::get('phpframework', 'db.migrator.directory');

    if (!$this->migrationDirectory) {
      throw new DatabaseMigratorException('Migration directory was not configured.', 1415085595);
    }

    if(!is_dir($this->migrationDirectory)) {
      throw new DatabaseMigratorException('Migration directory "' . $this->migrationDirectory . '" does not exist or is not a directory.', 1415085126);
    }

    $this->lastExecutedVersion = $this->getLastExecutedVersion();

  }

  /**
   * @throws DatabaseMigratorException
   */
  public function migrate() {
    foreach ($this->getMigrationFiles() as $version => $file) {
      if ($version > $this->lastExecutedVersion) {
        try {
          $this->migrateFile($file);
          $this->lastExecutedVersion = $version;
          $this->setCurrentMigrationVersion($this->lastExecutedVersion);
        } catch (\Exception $e) {
          throw $e;
        }
      }
    }
  }

  /**
   * @return int
   */
  protected function getLastExecutedVersion() {
    if(count($this->db->query("SHOW TABLES LIKE 'migration_ver'")) < 1) {
      return 0;
    }
    return $this->db->field('migration_ver', 'version');
  }

  /**
   * @return array
   */
  protected function getMigrationFiles() {
    $migrationFiles = [];
    $matches = [];
    if($handle = opendir($this->migrationDirectory)) {
      while($file = readdir($handle)) {
        if(preg_match('/^([0-9]+)_.*\.sql$/', $file, $matches) > 0) {
          $migrationFiles[(int)$matches[1]] = $file;
        }
        if(preg_match('/^([0-9]+)\.sql$/', $file, $matches) > 0) {
          $migrationFiles[(int)$matches[1]] = $file;
        }
      }
    }
    ksort($migrationFiles, SORT_NUMERIC);
    return $migrationFiles;
  }

  /**
   * @param string $filename
   * @throws DatabaseMigratorException when any command of the file is not executable
   */
  protected function migrateFile($filename) {
    $this->db->execute('SET autocommit = 0;');
    $this->db->execute('START TRANSACTION;');
    $statements = $this->getStatementsFromFile($filename);
    foreach($statements as $statement) {
      try {
        $this->executeStatement($statement);
      } catch (DatabaseQueryException $e) {
        throw new DatabaseMigratorException($e->getMessage(), 1415089456);
      }
    }
    $this->db->execute('COMMIT;');
    $this->db->execute('SET autocommit = 1;');
  }

  /**
   * @param int $version
   */
  protected function setCurrentMigrationVersion($version) {
    if(count($this->db->query("SHOW TABLES LIKE 'migration_ver'")) < 1) {
      $sql = "CREATE TABLE IF NOT EXISTS `migration_ver` (`version` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
      $this->db->execute($sql);
    }

    $data = ['version' => $version];
    if($this->db->count('migration_ver') < 1) {
      $this->db->insert('migration_ver', $data);
    } else {
      $this->db->update('migration_ver', $data, ['version!' => '0']);
    }
  }

  /**
   * @param string $filename
   * @return array
   * @throws DatabaseMigratorException
   */
  protected function getStatementsFromFile($filename) {
    $file = rtrim($this->migrationDirectory, '/') . '/' . $filename;
    $f = @fopen($file, "r");
    if ($f === FALSE) {
      throw new DatabaseMigratorException('Unable to open file "' . $file . '"');
    }
    $sql = fread($f, filesize($file));
    return explode(';', $sql);
  }

  /**
   * @param string $statement
   * @throws DatabaseQueryException
   * @throws DatabaseMigratorException
   */
  protected function executeStatement($statement) {
    if (strlen($statement) > 3 && substr(ltrim($statement), 0, 2) != '/*') {
      try {
        $this->db->execute($statement);
      } catch (DatabaseQueryException $ex) {
        $this->db->execute('ROLLBACK;');
        throw $ex;
      }
    }
  }

}
