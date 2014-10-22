<?php
namespace AppZap\PHPFramework\Tests\Unit\Domain\Repositories;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Domain\Collection\GenericModelCollection;
use AppZap\PHPFramework\Domain\Model\AbstractModel;
use AppZap\PHPFramework\Domain\Repository\AbstractDomainRepository;

class Item extends AbstractModel {
  protected $title;
  public function get_title() {
    return $this->title;
  }
  public function set_title($title) {
    $this->title = $title;
  }
}

class ItemRepository extends AbstractDomainRepository {
  public function find_by_title($title) {
    return $this->query_one(['title' => $title]);
  }
}

class AbstractDomainRepositoryTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var ItemRepository
   */
  protected $repository;

  public function setUp() {
    $database = 'phpunit_tests';
    $host = '127.0.0.1';
    $password = '';
    $user = 'travis';
    Configuration::set('phpframework', 'db.mysql.database', $database);
    Configuration::set('phpframework', 'db.mysql.host', $host);
    Configuration::set('phpframework', 'db.mysql.password', $password);
    Configuration::set('phpframework', 'db.mysql.user', $user);
    $this->repository = ItemRepository::get_instance();
  }

  /**
   * @test
   */
  public function saveAndGetById() {
    $item = new Item();
    $item->set_title('test');
    $this->repository->save($item);
    $id = $item->get_id();
    /** @var Item $gotten_item */
    $gotten_item = $this->repository->find_by_id($id);
    $this->assertSame('test', $gotten_item->get_title());
    $gotten_item->set_title('test2');
    $this->repository->save($item);
    /** @var Item $gotten_item2 */
    $gotten_item2 = $this->repository->find_by_id($id);
    $this->assertSame('test2', $gotten_item2->get_title());
  }

  /**
   * @test
   */
  public function queryOne() {
    $item = new Item();
    $item->set_title('queryOneTest');
    $this->repository->save($item);
    $id = $item->get_id();
    /** @var Item $gotten_item */
    $gotten_item = $this->repository->find_by_title('queryOneTest');
    $this->assertSame($id, $gotten_item->get_id());
  }

  /**
   * @test
   */
  public function queryOneNotExisting() {
    $item = $this->repository->find_by_title('ekbqGZvyAUcT0aoayxRJNBIu');
    $this->assertNull($item);
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\SingletonException
   * @expectedExceptionCode 1412682006
   */
  public function cloneException() {
    return clone $this->repository;
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\SingletonException
   * @expectedExceptionCode 1412682032
   */
  public function wakeupException() {
    $this->repository->__wakeup();
  }

  /**
   * @test
   */
  public function findAll() {
    $items = $this->repository->find_all();
    $this->assertTrue($items instanceof GenericModelCollection);
  }

}