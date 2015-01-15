<?php
namespace AppZap\PHPFramework\Authentication;

class BaseSessionUndefinedIndexException extends \Exception {

  /**
   * @param string $index
   */
  public function __construct($index) {
    parent::__construct('Undefined session index ' . $index);
  }
}
