<?php
namespace AppZap\PHPFramework;

use AppZap\PHPFramework\Configuration\Configuration;

class ExceptionLogger {

  /**
   * @var \Airbrake\Client
   */
  protected static $client;

  /**
   *
   */
  public static function initialize() {
    $airbrake_ini = Configuration::getSection('phpframework', 'airbrake');
    if (!is_null($airbrake_ini) && Configuration::get('phpframework', 'airbrake.enable', FALSE)) {
      $apiKey = $airbrake_ini['api_key'];
      $airbrake_environment = isset($airbrake_ini['environment']) ? $airbrake_ini['environment'] : 'NO_ENVIRONMENT_SET';
      $options = [
          'secure' => TRUE,
          'host' => $airbrake_ini['host'],
          'resource' => $airbrake_ini['resource'],
          'timeout' => 10,
          'environmentName' => Configuration::get('application', 'application') . ' / ' . $airbrake_environment,
      ];

      \Airbrake\EventHandler::start($apiKey, FALSE, $options);
      $config = new \Airbrake\Configuration($apiKey, $options);
      self::$client = new \Airbrake\Client($config);
    }
  }

  /**
   * @param \Exception $exception
   */
  public static function log_exception(\Exception $exception) {
    if (self::$client instanceof \Airbrake\Client) {
      self::$client->notifyOnException($exception);
    }
  }

}
