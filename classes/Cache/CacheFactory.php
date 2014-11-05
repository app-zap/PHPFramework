<?php
namespace AppZap\PHPFramework\Cache;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mvc\ApplicationPartMissingException;
use Nette\Caching\Storages\DevNullStorage;
use Nette\Caching\Storages\FileJournal;
use Nette\Caching\Storages\FileStorage;

/**
 * This factory instanciates and configures a Nette Cache
 */
class CacheFactory {

  /**
   * @var Cache
   */
  protected static $cache;

  /**
   * @return Cache
   * @throws ApplicationPartMissingException
   */
  public static function getCache() {
    if (!self::$cache instanceof Cache) {
      if (Configuration::get('phpframework', 'cache.enable')) {
        $cache_folder = Configuration::get('phpframework', 'cache.folder', './cache');
        $cache_folder_path = realpath($cache_folder);
        if (!is_dir($cache_folder_path)) {
          if (!@mkdir($cache_folder, 0777, TRUE)) {
            throw new ApplicationPartMissingException('Cache folder "' . $cache_folder . '" is missing and could not be created.', 1410537983);
          }
          $cache_folder_path = realpath($cache_folder);
        }
        $testfile = $cache_folder_path . '/L7NxnrqsICAtxg0qxDWPUSA';
        @touch($testfile);
        if (file_exists($testfile)) {
          unlink($testfile);
        } else {
          throw new ApplicationPartMissingException('Cache folder "' . $cache_folder . '" is not writable', 1410537933);
        }
        $storage = new FileStorage($cache_folder_path, new FileJournal($cache_folder_path));
      } else {
        $storage = new DevNullStorage();
      }
      self::$cache = new Cache($storage, Configuration::get('application', 'application'));
    }
    return self::$cache;
  }

  /**
   *
   */
  public static function reset() {
    self::$cache = NULL;
  }

}
