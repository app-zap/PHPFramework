<?php
namespace AppZap\PHPFramework\Configuration\Parser;

use AppZap\PHPFramework\Configuration\Configuration;

class IniParser {

  /**
   * @param string $application
   * @throws \Exception
   */
  static public function initialize() {
    $application_directory = Configuration::get('application', 'application_directory');
    $config_file_path = $application_directory . 'settings.ini';
    $overwrite_file_path = $application_directory . 'settings_local.ini';
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