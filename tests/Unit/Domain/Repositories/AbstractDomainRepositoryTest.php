<?php
namespace AppZap\PHPFramework\Tests\Unit\Domain\Repositories;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Domain\Collection\GenericModelCollection;
use AppZap\PHPFramework\Domain\Model\AbstractModel;
use AppZap\PHPFramework\Domain\Repository\AbstractDomainRepository;

class Item extends AbstractModel {
  protected $title;
  public function getTitle() {
    return $this->title;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
}

class ItemRepository extends AbstractDomainRepository {
  public function findByTitle($title) {
    return $this->queryOne(['title' => $title]);
  }
}

class AbstractDomainRepositoryTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var \PHPUnit_Framework_MockObject_MockObject
   */
  protected $databaseConnectionMock;

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
    $this->databaseConnectionMock = $this->getMockBuilder('AppZap\PHPFramework\Persistence\DatabaseConnection')->getMock();
    $this->repository = new ItemRepository($this->databaseConnectionMock);
  }

  /**
   * @test
   */
  public function saveAndGetById() {
    $this->databaseConnectionMock->
      expects($this->once())->
      method('insert')->
      with($this->equalTo('item'), $this->isType('array'))->
      will($this->returnValue(42));
    $item = new Item();
    $item->setTitle('test');
    $this->repository->save($item);
    $id = $item->getId();
    $this->assertEquals(42, $id, 'Mock should have assignes id 42 to $item');
    /** @var Item $gottenItem */
    $gottenItem = $this->repository->findById($id);
    $this->assertSame('test', $gottenItem->getTitle());
    $item->setTitle('test2');
    $this->repository->save($item);
    /** @var Item $gottenItem2 */
    $gottenItem2 = $this->repository->findById($id);
    $this->assertSame('test2', $gottenItem2->getTitle());
  }

  /**
   * @test
   */
  public function queryOne() {
    $this->databaseConnectionMock->
      expects($this->once())->
      method('insert')->
      with($this->equalTo('item'), $this->isType('array'))->
      will($this->returnValue(42));
    $this->databaseConnectionMock->
      expects($this->once())->
      method('row')->
      with($this->equalTo('item'), $this->equalTo('*'), $this->isType('array'))->
      will($this->returnValue(['id' => '42', 'title' => 'queryOneTest']));
    $item = new Item();
    $item->setTitle('queryOneTest');
    $this->repository->save($item);
    $id = $item->getId();
    /** @var Item $gottenItem */
    $gottenItem = $this->repository->findByTitle('queryOneTest');
    $this->assertSame($id, $gottenItem->getId());
  }

  /**
   * @test
   */
  public function queryOneNotExisting() {
    $this->databaseConnectionMock->
      expects($this->once())->
      method('row')->
      with($this->equalTo('item'), $this->equalTo('*'), $this->isType('array'))->
      will($this->returnValue(FALSE));
    $item = $this->repository->findByTitle('ekbqGZvyAUcT0aoayxRJNBIu');
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
    $this->databaseConnectionMock->
      expects($this->once())->
      method('select')->
      with($this->equalTo('item'), $this->equalTo('*'), $this->isNull())->
      will($this->returnValue([]));
    $items = $this->repository->findAll();
    $this->assertTrue($items instanceof GenericModelCollection);
  }

  /**
   * @test
   */
  public function remove() {
    $this->databaseConnectionMock->
      expects($this->once())->
      method('insert')->
      with($this->equalTo('item'), $this->isType('array'))->
      will($this->returnValue(42));
    $item = new Item();
    $item->setTitle('test');
    $this->repository->save($item);
    $id = $item->getId();
    /** @var Item $gottenItem */
    $gottenItem = $this->repository->findById($id);
    $this->assertSame('test', $gottenItem->getTitle());
    $this->repository->remove($gottenItem);
    $gottenItem2 = $this->repository->findById($id);
    $this->assertNull($gottenItem2);
  }

}
