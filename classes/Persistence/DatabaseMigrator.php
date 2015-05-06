<?php
namespace AppZap\PHPFramework\Persistence;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\SignalSlot\Dispatcher as SignalSlotDispatcher;

class DatabaseMigrator {

  const SIGNAL_MIGRATION_DIRECTORIES = 1430863672;

  /**
   * @var DatabaseConnection
   */
  protected $db;

  /**
   * @var array
   */
  protected $migrationContexts;

  /**
   * @throws DatabaseMigratorException
   */
  public function __construct() {
    $this->db = StaticDatabaseConnection::getInstance();
    $applicationMigrationDirectory = Configuration::get('phpframework', 'db.migrator.directory');
    if (!$applicationMigrationDirectory) {
      throw new DatabaseMigratorException('Migration directory was not configured.', 1415085595);
    }
    $this->migrationContexts = [
      new DatabaseMigrationContext('application', $applicationMigrationDirectory),
    ];

    SignalSlotDispatcher::emitSignal(self::SIGNAL_MIGRATION_DIRECTORIES, $this->migrationContexts);

    foreach ($this->migrationContexts as $context) {
      /** @var DatabaseMigrationContext $context */
      if(!is_dir($context->getDirectory())) {
        throw new DatabaseMigratorException('Migration directory "' . $context->getDirectory() . '" (' . $context->getName() . ') does not exist or is not a directory.', 1415085126);
      }
      $context->setLastExecutedVersion($this->getLastExecutedVersion($context));
    }

  }

  /**
   * @throws DatabaseMigratorException
   */
  public function migrate() {
    foreach ($this->migrationContexts as $context) {
      /** @var DatabaseMigrationContext $context */
      foreach ($this->getMigrationFiles($context) as $version => $file) {
        if ($version > $context->getLastExecutedVersion()) {
          $this->migrateFile($context->getDirectory() . $file);
          $context->setLastExecutedVersion($version);
          $this->saveCurrentMigrationVersion($context);
        }
      }
    }
  }

  /**
   * @param DatabaseMigrationContext $context
   * @return int
   */
  protected function getLastExecutedVersion($context) {
    if(count($this->db->query('SHOW TABLES LIKE `migration_ver`')) < 1) {
      return 0;
    }
    return (int)$this->db->field('migration_ver', 'version', ['context' => $context->getName()]);
  }

  /**
   * @param DatabaseMigrationContext $context
   * @return array
   */
  protected function getMigrationFiles($context) {
    $migrationFiles = [];
    $matches = [];
    if($handle = opendir($context->getDirectory())) {
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
   * @param DatabaseMigrationContext $context
   */
  protected function saveCurrentMigrationVersion($context) {
    if(count($this->db->query("SHOW TABLES LIKE 'migration_ver'")) < 1) {
      $sql = 'CREATE TABLE IF NOT EXISTS `migration_ver` (`context` varchar(255) NOT NULL, `version` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;';
      $this->db->execute($sql);
    }

    $data = [
      'context' => $context->getName(),
      'version' => $context->getLastExecutedVersion(),
    ];
    if($this->db->count('migration_ver', ['context' => $context->getName()]) < 1) {
      $this->db->insert('migration_ver', $data);
    } else {
      $this->db->update('migration_ver', $data, ['context' => $context->getName()]);
    }
  }

  /**
   * @param string $file
   * @return array
   * @throws DatabaseMigratorException
   */
  protected function getStatementsFromFile($file) {
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
