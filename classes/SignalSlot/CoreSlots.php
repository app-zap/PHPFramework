<?php
namespace AppZap\PHPFramework\SignalSlot;

use AppZap\PHPFramework\Cache\Cache;
use AppZap\PHPFramework\Cache\CacheFactory;
use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Persistence\DatabaseMigrator;

class CoreSlots {

  /**
   * @param string $output
   */
  public static function addFrameworkSignatureToOutput(&$output) {
    if (Configuration::get('phpframework', 'powered_by', TRUE)) {
      $output = preg_replace("/<head( (.)*?|)>/u", "<head$1>\n\t<!-- Powered by PHPFramework " . Configuration::get('phpframework', 'version', '') . " (https://github.com/app-zap/PHPFramework) -->", $output);
    }
  }

  /**
   *
   */
  public static function invokeDatabaseMigrator() {
    if (Configuration::get('phpframework', 'db.migrator.enable')) {
      (new DatabaseMigrator())->migrate();
    }
  }

  /**
   * @param string $output
   * @param string $uri
   * @param string $requestMethod
   * @throws \AppZap\PHPFramework\Mvc\ApplicationPartMissingException
   */
  public static function readOutputFromCache(&$output, $uri, $requestMethod) {
    if (Configuration::get('phpframework', 'cache.full_output', FALSE) && $requestMethod === 'get') {
      $output = CacheFactory::getCache()->load('output_' . $uri);
    }
  }

  /**
   * @param string $output
   * @param string $uri
   * @param string $requestMethod
   * @throws \AppZap\PHPFramework\Mvc\ApplicationPartMissingException
   */
  public static function writeOutputToCache($output, $uri, $requestMethod) {
    if (Configuration::get('phpframework', 'cache.full_output', FALSE) && $requestMethod === 'get') {
      CacheFactory::getCache()->save('output_' . $uri, $output, [
          Cache::EXPIRE => Configuration::get('phpframework', 'cache.full_output_expiration', '20 Minutes'),
      ]);
    }
  }

  /**
   * @param string $output
   */
  public static function echoOutput($output) {
    if (Configuration::get('phpframework', 'echo_output', TRUE)) {
      echo $output;
    }
  }

}
