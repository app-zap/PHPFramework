<?php
namespace AppZap\PHPFramework\Tests\Unit\Domain\Collection;

use AppZap\PHPFramework\Domain\Collection\GenericModelCollection;
use AppZap\PHPFramework\Domain\Model\AbstractModel;

class MyModel extends AbstractModel {
}

class GenericModelCollectionTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var GenericModelCollection
   */
  protected $collection;

  /**
   *
   */
  public function setUp() {
    $this->collection = new GenericModelCollection();
  }

  /**
   * @test
   */
  public function setAndGetModel() {
    $model = new MyModel();
    $model->setId(42);
    $this->collection->add($model);
    $gottenModel = $this->collection->getById(42);
    $this->assertSame(42, $gottenModel->getId());
  }

  /**
   * @test
   */
  public function getNotExistingModel() {
    $model = new MyModel();
    $model->setId(42);
    $this->collection->add($model);
    $gottenModel = $this->collection->getById(43);
    $this->assertNull($gottenModel);
  }

  /**
   * @test
   */
  public function setAndRemoveModel() {
    $model = new MyModel();
    $model->setId(42);
    $this->collection->add($model);
    $gottenModel = $this->collection->getById(42);
    $this->assertSame(42, $gottenModel->getId());
    $this->collection->remove($gottenModel);
    $gottenModel = $this->collection->getById(43);
    $this->assertNull($gottenModel);
  }

  /**
   * @test
   */
  public function removeItems() {
    $model1 = new MyModel();
    $model1->setId(1);
    $model2 = new MyModel();
    $model2->setId(2);
    $model3 = new MyModel();
    $model3->setId(3);
    $this->collection->add($model1);
    $this->collection->add($model2);
    $this->collection->add($model3);
    $itemsToRemove = new GenericModelCollection();
    $itemsToRemove->add($model1);
    $itemsToRemove->add($model3);
    $this->collection->removeItems($itemsToRemove);
    $this->assertSame(1, count($this->collection));
    foreach ($this->collection as $model) {
      /** @var $model AbstractModel */
      $this->assertSame(2, $model->getId());
    }
  }

  /**
   * @test
   */
  public function foreachOverCollection() {
    $model1 = new MyModel();
    $model1->setId(1);
    $this->collection->add($model1);
    $model2 = new MyModel();
    $model2->setId(2);
    $this->collection->add($model2);
    $model3 = new MyModel();
    $model3->setId(3);
    $this->collection->add($model3);
    $i = 0;
    foreach ($this->collection as $model) {
      /** @var $model MyModel */
      $this->assertSame(spl_object_hash($model), $this->collection->key());
      $this->assertTrue(in_array($model->getId(), [1, 2, 3]));
      $i++;
    }
    $this->assertSame(3, $i);
  }

  /**
   * @test
   */
  public function countCollection() {
    $model1 = new MyModel();
    $this->collection->add($model1);
    $model2 = new MyModel();
    $this->collection->add($model2);
    $model3 = new MyModel();
    $this->collection->add($model3);
    $this->assertSame(3, count($this->collection));
  }

}