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
  public static function get($section, $key, $defaultValue = NULL) {
    if (isset(self::$configuration[$section]) && isset(self::$configuration[$section][$key])) {
      return self::$configuration[$section][$key];
    } else {
      return $defaultValue;
    }
  }

  /**
   * @param string $section
   * @param string $namespace
   * @param array $defaultValues
   * @return array
   */
  public static function getSection($section, $namespace = NULL, $defaultValues = []) {
    if (isset(self::$configuration[$section])) {
      if ($namespace) {
        $configuration = [];
        foreach (self::$configuration[$section] as $key => $value) {
          if (strpos($key, $namespace . '.') === 0) {
            $configuration[substr($key, strlen($namespace) + 1)] = $value;
          }
        }
      } else {
        $configuration = self::$configuration[$section];
      }
      return array_merge($defaultValues, $configuration);
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
  public static function remove($section, $key) {
    if (isset(self::$configuration[$section]) && isset(self::$configuration[$section][$key])) {
      unset(self::$configuration[$section][$key]);
    }
  }

  /**
   * @param string $section
   */
  public static function removeSection($section) {
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

  /**
   * @param string $section
   * @param string $key
   * @deprecated Since: 1.4, Removal: 1.5, Reason: Use ->remove() instead
   */
  public static function remove_key($section, $key) {
    self::remove($section, $key);
  }

  /**
   * @param string $section
   * @deprecated Since: 1.4, Removal: 1.5, Reason: Use ->removeSection() instead
   */
  public static function remove_section($section) {
    self::removeSection($section);
  }

}
