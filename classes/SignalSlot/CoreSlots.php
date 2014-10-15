<?php
namespace AppZap\PHPFramework\SignalSlot;

use AppZap\PHPFramework\Configuration\Configuration;

class CoreSlots {

  public static function addFrameworkSignatureToOutput(&$output) {
    if (Configuration::get('phpframework', 'powered_by', TRUE)) {
      $output = preg_replace("/<head( (.)*?|)>/u", "<head$1>\n\t<!-- Powered by PHPFramework (https://github.com/app-zap/PHPFramework) -->", $output);
    }
  }

}