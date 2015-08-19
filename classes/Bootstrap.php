<?php
namespace AppZap\PHPFramework;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Configuration\DefaultConfiguration;
use AppZap\PHPFramework\Configuration\Parser\IniParser;
use AppZap\PHPFramework\Mvc\AbstractController;
use AppZap\PHPFramework\Mvc\ApplicationPartMissingException;
use AppZap\PHPFramework\Mvc\Dispatcher;
use AppZap\PHPFramework\SignalSlot\Dispatcher as SignalSlotDispatcher;

class Bootstrap {

  const SIGNAL_PLUGINSLOADED = 1415790750;

  /**
   * @param string $application
   * @return string
   * @throws ApplicationPartMissingException
   * @throws \Exception
   */
  public static function bootstrap($application = 'app') {
    self::initializeConfiguration($application);
    self::loadPlugins();
    self::registerCoreSlots();
    self::setErrorReporting();
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
    /** @deprecated Since 1.5, Removal: 1.6, Reason: Don't activate your plugins via settings.ini anymore. Plugins should auto-initialize themselves via composer. (See https://github.com/app-zap/PHPFramework-EmptyPlugin) */
    $plugins = Configuration::getSection('phpframework', 'plugins');
    if (is_array($plugins)) {
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
    SignalSlotDispatcher::emitSignal(self::SIGNAL_PLUGINSLOADED);
  }

  /**
   *
   */
  protected static function registerCoreSlots() {
    SignalSlotDispatcher::registerSlot(AbstractController::SIGNAL_INIT_RESPONSE, ['AppZap\PHPFramework\SignalSlot\CoreSlots', 'contentTypeHeader']);
    SignalSlotDispatcher::registerSlot(Dispatcher::SIGNAL_CONSTRUCT, ['AppZap\PHPFramework\SignalSlot\CoreSlots', 'invokeDatabaseMigrator']);
    SignalSlotDispatcher::registerSlot(Dispatcher::SIGNAL_OUTPUT_READY, ['AppZap\PHPFramework\SignalSlot\CoreSlots', 'writeOutputToCache']);
    SignalSlotDispatcher::registerSlot(Dispatcher::SIGNAL_OUTPUT_READY, ['AppZap\PHPFramework\SignalSlot\CoreSlots', 'addFrameworkSignatureToOutput']);
    SignalSlotDispatcher::registerSlot(Dispatcher::SIGNAL_OUTPUT_READY, ['AppZap\PHPFramework\SignalSlot\CoreSlots', 'echoOutput']);
    SignalSlotDispatcher::registerSlot(Dispatcher::SIGNAL_START_DISPATCHING, ['AppZap\PHPFramework\SignalSlot\CoreSlots', 'readOutputFromCache']);
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
  protected static function invokeDispatcher() {
    $dispatcher = new Dispatcher();
    if ($dispatcher->getRequestMethod() === 'cli') {
      $cliArguments = $_SERVER['argv'];
      array_shift($cliArguments);
      $resource = '/' . join('/', $cliArguments);
    } else {
      $resource = $_SERVER['REQUEST_URI'];
      $uriPathPrefix = '/' . trim(Configuration::get('phpframework', 'uri_path_prefix'), '/');
      if ($uriPathPrefix && strpos($resource, $uriPathPrefix) === 0) {
        $resource = substr($resource, strlen($uriPathPrefix));
      }
    }
    return $dispatcher->dispatch($resource);
  }

}
