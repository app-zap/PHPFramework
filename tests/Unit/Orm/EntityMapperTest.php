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
  public function get_date() {
    return $this->date;
  }

  /**
   * @param \DateTime $date
   */
  public function set_date(\DateTime $date) {
    $this->date = $date;
  }
  /**
   * @return EntityTestItem
   */
  public function get_parent() {
    return $this->parent;
  }
  /**
   * @param EntityTestItem $parent
   */
  public function set_parent(EntityTestItem $parent) {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function get_title() {
    return $this->title;
  }
  /**
   * @param string $title
   */
  public function set_title($title) {
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
  public function record_to_object() {
    $object = new EntityTestItem();
    $this->assertNull($this->entityMapper->record_to_object(FALSE, $object));
    $this->entityMapper->record_to_object([
      'title' => 'qBzJtCy23R1y+c4wh57eprVW',
      'description' => 'zlMO+cTGtCJYV/eXHvoe+iBe',
    ], $object);
    $this->assertSame('qBzJtCy23R1y+c4wh57eprVW', $object->get_title());
  }

  /**
   * @test
   */
  public function object_to_record() {
    $id = 42;
    $timestamp = 1413182967;
    $title = '1kcfRvy6J1WsWtXvgOu/kXba';
    $object = new EntityTestItem();
    $object->set_title($title);
    $parent_object = new EntityTestItem();
    $parent_object->set_id($id);
    $object->set_parent($parent_object);
    $date = new \DateTime();
    $date->setTimestamp($timestamp);
    $object->set_date($date);
    $record = $this->entityMapper->object_to_record($object);
    $this->assertSame((string) $timestamp, $record['date']);
    $this->assertSame((string) $id, $record['parent']);
    $this->assertSame($title, $record['title']);
  }

}