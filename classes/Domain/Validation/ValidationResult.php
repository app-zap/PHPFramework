<?php
namespace AppZap\PHPFramework\Domain\Validation;

class ValidationResult {

  /**
   * @var array
   */
  protected $errors = [];

  /**
   * @param string $property
   * @param string $message
   */
  public function addError($property, $message) {
    $this->errors[$property] = $message;
  }

  /**
   * @return array
   */
  public function getErrors() {
    return $this->errors;
  }

  /**
   * @return bool
   */
  public function isValid() {
    return count($this->errors) === 0;
  }

}