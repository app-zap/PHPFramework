<?php
namespace AppZap\PHPFramework\Persistence;

use AppZap\PHPFramework\Configuration\Configuration;

class SimpleMigrator {

  /**
   * @var DatabaseConnection
   */
  protected $db;

  /**
   * @var string
   */
  protected $migration_directory;

  /**
   * @throws SimpleMigratorException
   */
  public function __construct() {
    $migration_directory = Configuration::get('phpframework', 'db.migrator.directory');

    $this->db = StaticDatabaseConnection::getInstance();
    $this->migration_directory = $migration_directory;

    if(!is_dir($migration_directory)) {
      throw new SimpleMigratorException('Migration directory does not exist or is not a directory.');
    }
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
      $this->db->update('migration_ver', $data, ['1' => '1']);
    }
  }

  /**
   * @param string $filename
   * @throws SimpleMigratorException when any command of the file is not executable
   */
  protected function execute_statement_file($filename) {
    $this->db->execute('SET autocommit = 0;');
    $this->db->execute('START TRANSACTION;');

    $f = @fopen($filename, "r");
    if($f === false) {
      throw new SimpleMigratorException('Unable to open file "' . $filename . '"');
    }
    $sqlFile = fread($f, filesize($filename));
    $sqlArray = explode(';', $sqlFile);
    foreach($sqlArray as $stmt) {
      if(strlen($stmt) > 3 && substr(ltrim($stmt), 0, 2) != '/*') {
        try {
          $this->db->execute($stmt);
        } catch(DBQueryException $ex) {
          $this->db->execute('ROLLBACK;');
          throw new SimpleMigratorException('An error occured while executing query of "' . $filename . '": "' . $stmt . '"');
        }
      }
    }

    $this->db->execute('COMMIT;');
    $this->db->execute('SET autocommit = 1;');
  }

  /**
   * @throws DBConnectionException
   * @throws SimpleMigratorException
   */
  public function migrate() {
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

}

class SimpleMigratorException extends \Exception {}