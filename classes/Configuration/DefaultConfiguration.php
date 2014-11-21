<?php

namespace AppZap\PHPFramework\Configuration;

use AppZap\PHPFramework\Mvc\ApplicationPartMissingException;

class DefaultConfiguration {

  /**
   * @param $application
   * @throws ApplicationPartMissingException
   */
  public static function initialize($application) {
    $project_root = isset($_ENV['AppZap\PHPFramework\ProjectRoot']) ? $_ENV['AppZap\PHPFramework\ProjectRoot'] : getcwd();
    $application_directory_path = $project_root . '/' . $application;
    $application_directory = realpath($application_directory_path);
    if (!is_dir($application_directory)) {
      throw new ApplicationPartMissingException('Application folder ' . htmlspecialchars($application_directory_path) . ' not found', 1410538265);
    }
    $application_directory .= '/';
    Configuration::set('phpframework', 'project_root', $project_root);
    Configuration::set('phpframework', 'db.migrator.directory', $application_directory . '_sql/');
    Configuration::set('application', 'application', $application);
    Configuration::set('application', 'application_directory', $application_directory);
    Configuration::set('application', 'routes_file', $application_directory . 'routes.php');
    Configuration::set('application', 'templates_directory', $application_directory . 'templates/');
    Configuration::set('phpframework', 'version', '1.4-dev');
  }

}
