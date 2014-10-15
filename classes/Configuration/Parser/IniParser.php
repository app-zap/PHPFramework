<?php
namespace AppZap\PHPFramework\Configuration\Parser;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mvc\ApplicationPartMissingException;

class IniParser {

  /**
   * @param string $application
   * @throws \Exception
   */
  static public function init($application) {
    $project_root = isset($_ENV['AppZap\PHPFramework\ProjectRoot']) ? $_ENV['AppZap\PHPFramework\ProjectRoot'] : dirname($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']);
    $application_directory_path = $project_root . '/' . $application;
    $application_directory = realpath($project_root . '/' . $application);
    if (!is_dir($application_directory)) {
      throw new ApplicationPartMissingException('Application folder ' . htmlspecialchars($application_directory_path) . ' not found', 1410538265);
    }
    $application_directory .= '/';
    $config_file_path = $application_directory . 'settings.ini';
    $overwrite_file_path = $application_directory . 'settings_local.ini';
    Configuration::reset();
    Configuration::set('application', 'application', $application);
    Configuration::set('application', 'application_directory', $application_directory);
    Configuration::set('application', 'migration_directory', $application_directory . '_sql/');
    Configuration::set('application', 'routes_file', $application_directory . 'routes.php');
    Configuration::set('application', 'templates_directory', $application_directory . 'templates/');
    Configuration::set('phpframework', 'version', '1.3-dev');
    self::parse($config_file_path, $overwrite_file_path);
  }

  /**
   * @param string $config_file
   * @param string $overwrite_file
   */
  protected static function parse($config_file, $overwrite_file = NULL) {
    if (is_readable($config_file)) {
      self::parse_file($config_file);
    }
    if (is_readable($overwrite_file)) {
      self::parse_file($overwrite_file);
    }
  }

  /**
   * @param string $file
   */
  protected static function parse_file($file) {
    $config = parse_ini_file($file, TRUE);
    foreach ($config as $section => $sectionConfiguration) {
      foreach ($sectionConfiguration as $key => $value) {
        Configuration::set($section, $key, $value);
      }
    }
  }

}