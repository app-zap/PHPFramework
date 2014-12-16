<?php
namespace AppZap\PHPFramework;

/**
 * Trait Singleton
 *
 * This trait can be used to implement a singleton class. Singletons are classes which are only
 * instanciated once. This instance should be reused through the whole code cycle.
 *
 * Usage example:
 *
 * class MySingletonClass {
*  use Singleton;
 *  // here go the class members..
 * }
 *
 * $my_singleton_object = MySingletonClass::get_instance();
 */
trait Singleton {

  /**
   * @return object
   */
  public static function getInstance() {
    static $instance = NULL;
    $class = get_called_class();
    return $instance ?: $instance = new $class;
  }

  public function __clone() {
    throw new SingletonException('Cloning ' . __CLASS__ . ' is not allowed.', 1412682006);
  }

  public function __wakeup() {
    throw new SingletonException('Unserializing ' . __CLASS__ . ' is not allowed.', 1412682032);
  }

  /**
   * @return object
   * @deprecated Since: 1.4, Removal: 1.5, Reason: Use ->getInstance() instead
   */
  public static function get_instance() {
    return self::getInstance();
  }

}
