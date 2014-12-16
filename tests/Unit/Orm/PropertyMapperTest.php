<?php
namespace AppZap\PHPFramework\Tests\Unit\Orm;

use AppZap\PHPFramework\Domain\Collection\AbstractModelCollection;
use AppZap\PHPFramework\Domain\Model\AbstractModel;
use AppZap\PHPFramework\Domain\Repository\AbstractDomainRepository;
use AppZap\PHPFramework\Orm\PropertyMapper;

class MyDateTime extends \DateTime {}

class Item extends AbstractModel{}
class ItemRepository extends AbstractDomainRepository{
  public function find_by_id($id) {
    return $this->create_identity_model($id);
  }
}
class ItemCollection extends AbstractModelCollection{}
class ItemWithoutRepo extends AbstractModel{}

class PropertyMapperTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var \AppZap\PHPFramework\Orm\PropertyMapper
   */
  protected $fixture;

  public function setUp() {
    $this->fixture = new PropertyMapper();
  }

  /**
   * @test
   */
  public function source_is_already_of_target_type() {
    $source = new \DateTime();
    $this->assertSame($source, $this->fixture->map($source, '\\DateTime'));
  }

  /**
   * @test
   */
  public function timestamp_to_datetime() {
    $source = 1409738029;
    /** @var \DateTime $datetime */
    $datetime = $this->fixture->map($source, '\\DateTime');
    $this->assertTrue($datetime instanceof \DateTime);
    $this->assertSame($source, $datetime->getTimestamp());
  }

  /**
   * @test
   */
  public function with_or_without_trailing_backslash() {
    $source = 1409738157;
    /** @var \DateTime $datetime */
    $datetime = $this->fixture->map($source, '\\DateTime');
    $this->assertTrue($datetime instanceof \DateTime);
    $datetime = $this->fixture->map($source, 'DateTime');
    $this->assertTrue($datetime instanceof \DateTime);
  }

  /**
   * @test
   */
  public function dont_convert_to_datetime_if_not_numeric() {
    $source = 'abc';
    $this->assertSame($source, $this->fixture->map($source, 'DateTime'));
  }

  /**
   * @test
   */
  public function convert_to_class_extending_datetime() {
    $source = 1409744701;
    /** @var MyDateTime $my_datetime */
    $my_datetime = $this->fixture->map($source, '\\AppZap\\PHPFramework\\Tests\\Unit\\Orm\\MyDateTime');
    $this->assertTrue($my_datetime instanceof MyDateTime);
    $this->assertSame($source, $my_datetime->getTimestamp());
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Orm\PropertyMappingException
   */
  public function conversion_not_supported() {
    $source = 'abc';
    $this->fixture->map($source, 'NotExistingClass');
  }

  /**
   * @test
   */
  public function convert_to_model() {
    $source = 1;
    /** @var Item $item */
    $item = $this->fixture->map($source, 'AppZap\\PHPFramework\\Tests\\Unit\\Orm\\Item');
    $this->assertTrue($item instanceof Item);
    $this->assertSame(1, $item->getId());
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Orm\PropertyMappingException
   */
  public function convert_to_model_without_repo() {
    $source = 1;
    $this->fixture->map($source, 'AppZap\\PHPFramework\\Tests\\Unit\\Orm\\ItemWithoutRepo');
  }

}
