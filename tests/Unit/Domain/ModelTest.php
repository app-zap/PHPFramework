<?php
namespace AppZap\PHPFramework\Tests\Unit\Domain;

use AppZap\PHPFramework\Domain\Model\AbstractModel;

class MyModel extends AbstractModel {

  /**
   * @var string
   */
  protected $title;

  /**
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * @param string $title
   */
  public function setTitle($title) {
    $this->title = $title;
  }

}

class ModelTest extends \PHPUnit_Framework_TestCase {

  /**
   * @test
   */
  public function roundtripProperties() {
    $model = new MyModel();
    $model->setId(42);
    $model->setTitle('My Model');
    $this->assertSame(42, $model->getId());
    $this->assertSame('My Model', $model->getTitle());
  }

}