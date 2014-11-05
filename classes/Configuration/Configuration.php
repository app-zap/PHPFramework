<?php
namespace AppZap\PHPFramework\Configuration;

class Configuration {

  /**
   * @var array
   */
  protected static $configuration = [];

  /**
   * @param string $section
   * @param string $key
   * @param mixed $defaultValue
   * @return mixed
   */
  public static function get($section, $key, $default_value = NULL) {
    if (isset(self::$configuration[$section]) && isset(self::$configuration[$section][$key])) {
      return self::$configuration[$section][$key];
    } else {
      return $default_value;
    }
  }

  /**
   * @param string $section
   * @param string $namespace
   * @return array
   */
  public static function getSection($section, $namespace = NULL) {
    if (isset(self::$configuration[$section])) {
      if ($namespace) {
        $filtered = [];
        foreach (self::$configuration[$section] as $key => $value) {
          if (strpos($key, $namespace . '.') === 0) {
            $filtered[substr($key, strlen($namespace) + 1)] = $value;
          }
        }
        return $filtered;
      } else {
        return self::$configuration[$section];
      }
    } else {
      return NULL;
    }
  }

  /**
   * @param string $section
   * @param string $key
   * @param mixed $value
   */
  public static function set($section, $key, $value = NULL) {
    if (!isset(self::$configuration[$section])) {
      self::$configuration[$section] = [];
    }
    self::$configuration[$section][$key] = $value;
  }

  /**
   * @param string $section
   * @param string $key
   */
  public static function remove_key($section, $key) {
    if (isset(self::$configuration[$section]) && isset(self::$configuration[$section][$key])) {
      unset(self::$configuration[$section][$key]);
    }
  }

  /**
   * @param string $section
   */
  public static function remove_section($section) {
    if (isset(self::$configuration[$section])) {
      unset(self::$configuration[$section]);
    }
  }

  /**
   *
   */
  public static function reset() {
    self::$configuration = [];
  }

}
