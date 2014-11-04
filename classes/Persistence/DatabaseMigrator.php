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
  protected $migration_directory;

  /**
   * @throws DatabaseMigratorException
   */
  public function __construct() {
    $this->db = StaticDatabaseConnection::getInstance();
    $this->migration_directory = Configuration::get('phpframework', 'db.migrator.directory');

    if (!$this->migration_directory) {
      throw new DatabaseMigratorException('Migration directory was not configured.', 1415085595);
    }

    if(!is_dir($this->migration_directory)) {
      throw new DatabaseMigratorException('Migration directory "' . $this->migration_directory . '" does not exist or is not a directory.', 1415085126);
    }

  }

  /**
   * @throws DBConnectionException
   * @throws DatabaseMigratorException
   */
  public function migrate() {
    $migration_files = $this->get_migration_files();
    do {
      $next_migration = $this->get_current_migration_version() + 1;
      if(!array_key_exists($next_migration, $migration_files)) {
        break;
      }
      $next_path = rtrim($this->migration_directory, '/') . '/' . $migration_files[$next_migration];
      if(file_exists($next_path) && is_file($next_path)) {
        $this->execute_statement_file($next_path);
        $this->set_current_migration_version($next_migration);
      }
    } while(file_exists($next_path));
  }

  /**
   * @return array
   */
  protected function get_migration_files() {
    $migration_files = [];
    $matches = [];
    if($handle = opendir($this->migration_directory)) {
      while($file = readdir($handle)) {
        if(preg_match('/^([0-9]+)_.*\.sql$/', $file, $matches) > 0) {
          $migration_files[(int)$matches[1]] = $file;
        }
        if(preg_match('/^([0-9]+)\.sql$/', $file, $matches) > 0) {
          $migration_files[(int)$matches[1]] = $file;
        }
      }
    }
    return $migration_files;
  }

  /**
   * @return int
   */
  protected function get_current_migration_version() {
    if(count($this->db->query("SHOW TABLES LIKE 'migration_ver'")) < 1) {
      return 0;
    }

    return $this->db->field('migration_ver', 'version');
  }

  /**
   * @param int $version
   */
  protected function set_current_migration_version($version) {
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
   * @throws DatabaseMigratorException when any command of the file is not executable
   */
  protected function execute_statement_file($filename) {
    $this->db->execute('SET autocommit = 0;');
    $this->db->execute('START TRANSACTION;');
    $statements = $this->get_statements_from_file($filename);
    foreach($statements as $statement) {
      try {
        $this->execute_statement($statement);
      } catch (DBQueryException $e) {
        throw new DatabaseMigratorException('An error occured while executing query of "' . $filename . '": "' . $statement . '"', 1415089456);
      }
    }
    $this->db->execute('COMMIT;');
    $this->db->execute('SET autocommit = 1;');
  }

  /**
   * @param $filename
   * @return array
   * @throws DatabaseMigratorException
   */
  protected function get_statements_from_file($filename) {
    $f = @fopen($filename, "r");
    if ($f === false) {
      throw new DatabaseMigratorException('Unable to open file "' . $filename . '"');
    }
    $sqlFile = fread($f, filesize($filename));
    return explode(';', $sqlFile);
  }

  /**
   * @param $statement
   * @throws DBQueryException
   * @throws DatabaseMigratorException
   */
  protected function execute_statement($statement) {
    if (strlen($statement) > 3 && substr(ltrim($statement), 0, 2) != '/*') {
      try {
        $this->db->execute($statement);
      } catch (DBQueryException $ex) {
        $this->db->execute('ROLLBACK;');
        throw $ex;
      }
    }
  }

}

class DatabaseMigratorException extends \Exception {}