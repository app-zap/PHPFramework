<?php
namespace AppZap\PHPFramework;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Configuration\Parser\IniParser;
use AppZap\PHPFramework\Mvc\ApplicationPartMissingException;
use AppZap\PHPFramework\Mvc\Dispatcher;
use AppZap\PHPFramework\Persistence\SimpleMigrator;

class Bootstrap {

  /**
   * @param $application
   * @throws \Exception
   */
  public static function bootstrap($application) {
    self::initializeConfiguration($application);
    self::checkForRequiredApplicationParts();
    self::setErrorReporting();
    self::initializeExceptionLogging();
    self::invokeDatabaseMigrator();
    self::invokeDispatcher();
  }

  /**
   * @param string $application
   * @throws \Exception
   */
  protected static function initializeConfiguration($application) {
    IniParser::init($application);
  }

  /**
   * @throws ApplicationPartMissingException
   */
  protected static function checkForRequiredApplicationParts() {
    if (!is_dir(Configuration::get('application', 'templates_directory'))) {
      throw new ApplicationPartMissingException('Template directory "' . Configuration::get('application', 'templates_directory') . '" does not exist.');
    }
    if (!is_readable(Configuration::get('application', 'routes_file'))) {
      throw new ApplicationPartMissingException('Routes file "' . Configuration::get('application', 'routes_file') . '" does not exist.');
    }
  }

  /**
   *
   */
  protected static function setErrorReporting() {
    if (Configuration::get('debug', 'debug_mode')) {
      error_reporting(E_ALL);
    }
  }

  /**
   *
   */
  protected static function initializeExceptionLogging() {
    $airbrake_configuration = Configuration::getSection('airbrake');
    if (!is_null($airbrake_configuration)) {
      \Airbrake\EventHandler::start($airbrake_configuration['api_key'], FALSE, [
              'secure' => TRUE,
              'host' => $airbrake_configuration['host'],
              'resource' => $airbrake_configuration['resource'],
              'timeout' => 10,
          ]
      );
    }
  }

  /**
   *
   */
  protected static function invokeDatabaseMigrator() {
    if (Configuration::get('db', 'enable_migrator')) {
      (new SimpleMigrator())->migrate();
    }
  }

  /**
   *
   */
  protected static function invokeDispatcher() {
    global $argv;
    $dispatcher = new Dispatcher();
    if (php_sapi_name() === 'cli') {
      array_shift($argv);
      $dispatcher->dispatch('/' . join('/', $argv));
    } else {
      $dispatcher->dispatch($_SERVER['REQUEST_URI']);
    }
  }

}