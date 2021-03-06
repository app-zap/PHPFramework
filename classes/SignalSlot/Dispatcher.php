<?php
namespace AppZap\PHPFramework\SignalSlot;

class Dispatcher {

  /**
   * @var array
   */
  protected static $slots;

  public static function emitSignal($signalName, &$reference = NULL) {
    $arguments = func_get_args();
    array_shift($arguments); // first argument is $signalName
    array_shift($arguments); // second argument is $reference
    if (isset(self::$slots[$signalName])) {
      foreach (self::$slots[$signalName] as $callable) {
        call_user_func_array($callable, array_merge([&$reference], $arguments));
      }
    }
  }

  /**
   * @param string $signalName
   * @param callable $callback
   */
  public static function registerSlot($signalName, $callback) {
    if (!isset(self::$slots[$signalName])) {
      self::$slots[$signalName] = [];
    }
    self::$slots[$signalName][] = $callback;
  }

}
