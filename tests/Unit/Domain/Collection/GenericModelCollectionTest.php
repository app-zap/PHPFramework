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
    $model->set_id(42);
    $this->collection->set_item($model);
    $gotten_model = $this->collection->get_by_id(42);
    $this->assertSame(42, $gotten_model->get_id());
  }

  /**
   * @test
   */
  public function getNotExistingModel() {
    $model = new MyModel();
    $model->set_id(42);
    $this->collection->set_item($model);
    $gotten_model = $this->collection->get_by_id(43);
    $this->assertNull($gotten_model);
  }

  /**
   * @test
   */
  public function setAndRemoveModel() {
    $model = new MyModel();
    $model->set_id(42);
    $this->collection->set_item($model);
    $gotten_model = $this->collection->get_by_id(42);
    $this->assertSame(42, $gotten_model->get_id());
    $this->collection->remove_item($gotten_model);
    $gotten_model = $this->collection->get_by_id(43);
    $this->assertNull($gotten_model);
  }

  /**
   * @test
   */
  public function foreachOverCollection() {
    $model1 = new MyModel();
    $model1->set_id(1);
    $this->collection->set_item($model1);
    $model2 = new MyModel();
    $model2->set_id(2);
    $this->collection->set_item($model2);
    $model3 = new MyModel();
    $model3->set_id(3);
    $this->collection->set_item($model3);
    $i = 0;
    foreach ($this->collection as $model) {
      $this->assertSame(spl_object_hash($model), $this->collection->key());
      $this->assertTrue(in_array($model->get_id(), [1, 2, 3]));
      $i++;
    }
    $this->assertSame(3, $i);
  }

  /**
   * @test
   */
  public function countCollection() {
    $model1 = new MyModel();
    $this->collection->set_item($model1);
    $model2 = new MyModel();
    $this->collection->set_item($model2);
    $model3 = new MyModel();
    $this->collection->set_item($model3);
    $this->assertSame(3, count($this->collection));
  }

}