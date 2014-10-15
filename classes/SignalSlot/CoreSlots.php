<?php
namespace AppZap\PHPFramework\SignalSlot;

class CoreSlots {

  public function addFrameworkSignatureToOutput(&$output) {
    $output = preg_replace("/<head( (.)*?|)>/u", "<head$1>\n\t<!-- Powered by PHPFramework (https://github.com/app-zap/PHPFramework) -->", $output);
  }

}