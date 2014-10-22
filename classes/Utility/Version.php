<?php

namespace AppZap\PHPFramework\Utility;

use AppZap\PHPFramework\Configuration\Configuration;

class Version {

  /**
   * @param int $major
   * @param int $minor
   * @return bool
   */
  public static function minimum($major, $minor) {
    $version = Configuration::get('phpframework', 'version', '0.0');
    $versionParts = explode('.', $version, 2);
    $actualMajor = (int) $versionParts[0];
    $actualMinor = (int) $versionParts[1];
    if (($actualMajor > $major) || ($actualMajor === $major && $actualMinor >= $minor)) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @param int $major
   * @param int $minor
   * @return bool
   */
  public static function maximum($major, $minor) {
    $version = Configuration::get('phpframework', 'version', '0.0');
    $versionParts = explode('.', $version, 2);
    $actualMajor = (int) $versionParts[0];
    $actualMinor = (int) $versionParts[1];
    if (($actualMajor < $major) || ($actualMajor === $major && $actualMinor <= $minor)) {
      return true;
    } else {
      return false;
    }
  }

}