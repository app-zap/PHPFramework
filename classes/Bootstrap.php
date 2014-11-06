<?php
namespace AppZap\PHPFramework;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Configuration\DefaultConfiguration;
use AppZap\PHPFramework\Configuration\Parser\IniParser;
use AppZap\PHPFramework\Mvc\ApplicationPartMissingException;
use AppZap\PHPFramework\Mvc\Dispatcher;
use AppZap\PHPFramework\SignalSlot\Dispatcher as SignalSlotDispatcher;

class Bootstrap {

  /**
   * @param string $application
   * @return string
   * @throws ApplicationPartMissingException
   * @throws \Exception
   */
  public static function bootstrap($application) {
    self::initializeConfiguration($application);
    self::loadPlugins();
    self::registerCoreSlots();
    self::checkForRequiredApplicationParts();
    self::setErrorReporting();
    self::initializeExceptionLogging();
    return self::invokeDispatcher();
  }

  /**
   * @param string $application
   * @throws \Exception
   */
  protected static function initializeConfiguration($application) {
    Configuration::reset();
    DefaultConfiguration::initialize($application);
    IniParser::initialize();
  }

  /**
   *
   */
  protected static function loadPlugins() {
    $plugins = Configuration::getSection('phpframework', 'plugins');
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
   *
   */
  protected static function registerCoreSlots() {
    SignalSlotDispatcher::registerSlot(Dispatcher::SIGNAL_CONSTRUCT, ['AppZap\PHPFramework\SignalSlot\CoreSlots', 'invokeDatabaseMigrator']);
    SignalSlotDispatcher::registerSlot(Dispatcher::SIGNAL_OUTPUT_READY, ['AppZap\PHPFramework\SignalSlot\CoreSlots', 'addFrameworkSignatureToOutput']);
  }

  /**
   * @throws ApplicationPartMissingException
   */
  protected static function checkForRequiredApplicationParts() {
    if (!is_dir(Configuration::get('application', 'templates_directory'))) {
      throw new ApplicationPartMissingException('Template directory "' . Configuration::get('application', 'templates_directory') . '" does not exist.');
    }
  }

  /**
   *
   */
  protected static function setErrorReporting() {
    if (Configuration::get('phpframework', 'debug_mode')) {
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
  protected static function invokeDispatcher() {
    $dispatcher = new Dispatcher();
    if ($dispatcher->get_request_method() === 'cli') {
      $cli_arguments = $_SERVER['argv'];
      array_shift($cli_arguments);
      $resource = '/' . join('/', $cli_arguments);
    } else {
      $resource = $_SERVER['REQUEST_URI'];
      $uri_path_prefix = '/' . trim(Configuration::get('phpframework', 'uri_path_prefix'), '/');
      if ($uri_path_prefix && strpos($resource, $uri_path_prefix) === 0) {
        $resource = substr($resource, strlen($uri_path_prefix));
      }
    }
    return $dispatcher->dispatch($resource);
  }

}
