<?php
namespace AppZap\PHPFramework\Authentication;

class BasePHPSession implements BaseSessionInterface {

  /**
   *
   */
  public function __construct() {
      session_start();
  }

  /**
   * @param string $key
   * @param mixed $value
   * @return BasePHPSession
   */
  public function set($key, $value) {
    $key = (string)$key;
    $_SESSION[$key] = $value;

    return $this;
  }

  /**
   * @param string $key
   * @param mixed $default Default value to return when key is not found
   * @return mixed
   * @throws BaseSessionUndefinedIndexException if $key not in $_SESSION
   */
  public function get($key, $default = NULL) {
    $key = (string)$key;

    if($this->exist($key)) {
      return $_SESSION[$key];
    }

    return $default;
  }

  /**
   * @param string $key
   * @return boolean
   */
  public function exist($key) {
    $key = (string)$key;
    return isset($_SESSION[$key]);
  }

  /**
   * @param string $key
   * @return BasePHPSession
   * @throws BaseSessionUndefinedIndexException if $key not in $_SESSION
   */
  public function clear($key) {
    $key = (string)$key;

    if($this->exist($key)) {
      unset($_SESSION[$key]);
      return $this;
    }

    throw new BaseSessionUndefinedIndexException($key);
  }

  /**
   * @return BasePHPSession
   */
  public function clearAll() {
    session_unset();
    return $this;
  }

}
