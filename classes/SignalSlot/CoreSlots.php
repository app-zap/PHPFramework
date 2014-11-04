<?php
namespace AppZap\PHPFramework\SignalSlot;

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

}