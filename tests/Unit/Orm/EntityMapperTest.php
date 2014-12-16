<?php
namespace AppZap\phpframework\tests\Unit\Orm;

use AppZap\PHPFramework\Domain\Model\AbstractModel;
use \AppZap\PHPFramework\Orm\EntityMapper;

class EntityTestItem extends AbstractModel {
  /**
   * @var \DateTime
   */
  protected $date;
  /**
   * @var EntityTestItem
   */
  protected $parent;
  /**
   * @var string
   */
  protected $title;

  /**
   * @return \DateTime
   */
  public function getDate() {
    return $this->date;
  }

  /**
   * @param \DateTime $date
   */
  public function setDate(\DateTime $date) {
    $this->date = $date;
  }
  /**
   * @return EntityTestItem
   */
  public function getParent() {
    return $this->parent;
  }
  /**
   * @param EntityTestItem $parent
   */
  public function setParent(EntityTestItem $parent) {
    $this->parent = $parent;
  }
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

class EntityMapperTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var EntityMapper
   */
  protected $entityMapper;

  public function setUp() {
    $this->entityMapper = EntityMapper::get_instance();
  }

  /**
   * @test
   */
  public function recordToObject() {
    $object = new EntityTestItem();
    $this->assertNull($this->entityMapper->recordToObject(NULL, $object));
    $this->entityMapper->recordToObject([
      'title' => 'qBzJtCy23R1y+c4wh57eprVW',
      'description' => 'zlMO+cTGtCJYV/eXHvoe+iBe',
    ], $object);
    $this->assertSame('qBzJtCy23R1y+c4wh57eprVW', $object->getTitle());
  }

  /**
   * @test
   */
  public function objectToRecord() {
    $id = 42;
    $timestamp = 1413182967;
    $title = '1kcfRvy6J1WsWtXvgOu/kXba';
    $object = new EntityTestItem();
    $object->setTitle($title);
    $parentObject = new EntityTestItem();
    $parentObject->setId($id);
    $object->setParent($parentObject);
    $date = new \DateTime();
    $date->setTimestamp($timestamp);
    $object->setDate($date);
    $record = $this->entityMapper->objectToRecord($object);
    $this->assertSame((string) $timestamp, $record['date']);
    $this->assertSame((string) $id, $record['parent']);
    $this->assertSame($title, $record['title']);
  }

}