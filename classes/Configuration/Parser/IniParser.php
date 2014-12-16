<?php
namespace AppZap\PHPFramework\Configuration\Parser;

use AppZap\PHPFramework\Configuration\Configuration;

class IniParser {

  /**
   * @throws \Exception
   */
  static public function initialize() {
    $applicationDirectory = Configuration::get('application', 'application_directory');
    $configFilePath = $applicationDirectory . 'settings.ini';
    $overwriteFilePath = $applicationDirectory . 'settings_local.ini';
    self::parse($configFilePath, $overwriteFilePath);
  }

  /**
   * @param string $configFile
   * @param string $overwriteFile
   */
  protected static function parse($configFile, $overwriteFile = NULL) {
    if (is_readable($configFile)) {
      self::parseFile($configFile);
    }
    if (is_readable($overwriteFile)) {
      self::parseFile($overwriteFile);
    }
  }

  /**
   * @param string $file
   */
  protected static function parseFile($file) {
    $config = parse_ini_file($file, TRUE);
    foreach ($config as $section => $sectionConfiguration) {
      foreach ($sectionConfiguration as $key => $value) {
        Configuration::set($section, $key, $value);
      }
    }
  }

}
