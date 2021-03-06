<?php
namespace AppZap\PHPFramework\Authentication;

interface BaseSessionInterface {

  /**
   * @param string $key
   * @param mixed $value
   * @return BaseSessionInterface
   */
  public function set($key, $value);

  /**
   * @param string $key
   * @param mixed $default Default value to return when key is not found
   * @return mixed
   */
  public function get($key, $default = NULL);

  /**
   * @param string $key
   * @return boolean
   */
  public function exist($key);

  /**
   * @param string $key
   * @return BaseSessionInterface
   * @throws BaseSessionUndefinedIndexException
   */
  public function clear($key);

  /**
   * @return BaseSessionInterface
   */
  public function clearAll();

}
