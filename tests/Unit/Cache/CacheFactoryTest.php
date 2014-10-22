<?php
namespace AppZap\PHPFramework\Tests\Unit\Cache;

use AppZap\PHPFramework\Cache\CacheFactory;
use AppZap\PHPFramework\Configuration\Configuration;
use Nette\Caching\Storages\DevNullStorage;
use Nette\Caching\Storages\FileStorage;

class CacheFactoryTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    CacheFactory::reset();
  }

  /**
   * @test
   */
  public function returnDevNullStorage() {
    Configuration::reset();
    $cache = CacheFactory::getCache();
    $this->assertTrue($cache->getStorage() instanceof DevNullStorage);
  }

  /**
   * @test
   */
  public function returnFileStorage() {
    Configuration::reset();
    Configuration::set('phpframework', 'cache.enable', TRUE);
    Configuration::set('phpframework', 'cache.folder', './tests/Unit/Cache/_cachefolder');
    $cache = CacheFactory::getCache();
    $this->assertTrue($cache->getStorage() instanceof FileStorage);
  }

  /**
   * @test
   */
  public function writeCacheFolder() {
    Configuration::reset();
    Configuration::set('phpframework', 'cache.enable', TRUE);
    Configuration::set('phpframework', 'cache.folder', './tests/Unit/Cache/_cachefolder/tempfolder');
    $cache = CacheFactory::getCache();
    $this->assertTrue($cache->getStorage() instanceof FileStorage);
    rmdir('./tests/Unit/Cache/_cachefolder/tempfolder');
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\ApplicationPartMissingException
   * @expectedExceptionCode 1410537983
   */
  public function cacheFolderCanNotBeWritten() {
    Configuration::reset();
    Configuration::set('phpframework', 'cache.enable', TRUE);
    Configuration::set('phpframework', 'cache.folder', '/_cachefolder');
    $cache = CacheFactory::getCache();
    $this->assertTrue($cache->getStorage() instanceof FileStorage);
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\ApplicationPartMissingException
   * @expectedExceptionCode 1410537933
   */
  public function cacheFolderNotWritable() {
    Configuration::reset();
    Configuration::set('phpframework', 'cache.enable', TRUE);
    Configuration::set('phpframework', 'cache.folder', '/');
    $cache = CacheFactory::getCache();
    $this->assertTrue($cache->getStorage() instanceof FileStorage);
  }

}