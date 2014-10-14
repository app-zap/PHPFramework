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
    self::loadPlugins();
    self::checkForRequiredApplicationParts();
    self::setErrorReporting();
    self::initializeExceptionLogging();
    self::invokeDatabaseMigrator();
    return self::invokeDispatcher();
  }

  /**
   * @param string $application
   * @throws \Exception
   */
  protected static function initializeConfiguration($application) {
    IniParser::init($application);
  }

  /**
   *
   */
  protected static function loadPlugins() {
    $plugins = Configuration::getSection('phpframework_plugins');
    if ($plugins) {
      foreach ($plugins as $namespace => $enabled) {
        if ($enabled) {
          $pluginLoaderClassname = $namespace . '\PluginLoader';
          if (!class_exists($pluginLoaderClassname)) {
            throw new \Exception('Plugin ' . $namespace . ' could not be loaded. Class ' . $pluginLoaderClassname . ' was not found.', 1413322791);
          }
          new $pluginLoaderClassname();
        }
      }
    }
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
    ExceptionLogger::initialize();
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
    if ($dispatcher->get_request_method() === 'cli') {
      array_shift($argv);
      $resource = '/' . join('/', $argv);
    } else {
      $resource = $_SERVER['REQUEST_URI'];
    }
    return $dispatcher->dispatch($resource);
  }

}