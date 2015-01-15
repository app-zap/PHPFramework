<?php

namespace AppZap\PHPFramework\Configuration;

use AppZap\PHPFramework\Mvc\ApplicationPartMissingException;

class DefaultConfiguration {

  /**
   * @param $application
   * @throws ApplicationPartMissingException
   */
  public static function initialize($application) {
    $projectRoot = self::getProjectRoot();
    $applicationDirectoryPath = $projectRoot . $application;
    $applicationDirectory = realpath($applicationDirectoryPath);
    if (!is_dir($applicationDirectory)) {
      throw new ApplicationPartMissingException('Application folder ' . htmlspecialchars($applicationDirectoryPath) . ' not found', 1410538265);
    }
    $applicationDirectory .= '/';
    Configuration::set('phpframework', 'db.migrator.directory', $applicationDirectory . '_sql/');
    Configuration::set('phpframework', 'project_root', $projectRoot);
    Configuration::set('application', 'application', $application);
    Configuration::set('application', 'application_directory', $applicationDirectory);
    Configuration::set('application', 'routes_file', $applicationDirectory . 'routes.php');
    Configuration::set('application', 'templates_directory', $applicationDirectory . 'templates/');
    Configuration::set('phpframework', 'version', '1.5-dev');
  }

  /**
   * @return string
   */
  protected static function getProjectRoot() {
    if (isset($_ENV['AppZap\PHPFramework\ProjectRoot'])) {
      $projectRoot = $_ENV['AppZap\PHPFramework\ProjectRoot'];
    } else {
      $projectRoot = getcwd();
    }
    $projectRoot = rtrim($projectRoot, '/') . '/';
    return $projectRoot;
  }

}
