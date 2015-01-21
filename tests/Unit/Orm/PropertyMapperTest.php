<?php
namespace AppZap\PHPFramework\Tests\Unit\Orm;

use AppZap\PHPFramework\Domain\Collection\AbstractModelCollection;
use AppZap\PHPFramework\Domain\Model\AbstractModel;
use AppZap\PHPFramework\Domain\Repository\AbstractDomainRepository;
use AppZap\PHPFramework\Orm\PropertyMapper;

class MyDateTime extends \DateTime {}

class Item extends AbstractModel{}
class ItemRepository extends AbstractDomainRepository{
  public function findById($id) {
    return $this->createIdentityModel($id);
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
  public function sourceIsAlreadyOfTargetType() {
    $source = new \DateTime();
    $this->assertSame($source, $this->fixture->map($source, '\\DateTime'));
  }

  /**
   * @test
   */
  public function timestampToDatetime() {
    $source = 1409738029;
    /** @var \DateTime $datetime */
    $datetime = $this->fixture->map($source, '\\DateTime');
    $this->assertTrue($datetime instanceof \DateTime);
    $this->assertSame($source, $datetime->getTimestamp());
  }

  /**
   * @test
   */
  public function withOrWithoutTrailingBackslash() {
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
  public function dontConvertToDatetimeIfNotNumeric() {
    $source = 'abc';
    $this->assertSame($source, $this->fixture->map($source, 'DateTime'));
  }

  /**
   * @test
   */
  public function convertToClassExtendingDatetime() {
    $source = 1409744701;
    /** @var MyDateTime $myDatetime */
    $myDatetime = $this->fixture->map($source, '\\AppZap\\PHPFramework\\Tests\\Unit\\Orm\\MyDateTime');
    $this->assertTrue($myDatetime instanceof MyDateTime);
    $this->assertSame($source, $myDatetime->getTimestamp());
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Orm\PropertyMappingException
   */
  public function conversionNotSupported() {
    $source = 'abc';
    $this->fixture->map($source, 'NotExistingClass');
  }

  /**
   * @test
   */
  public function convertToModel() {
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
  public function convertToModelWithoutRepo() {
    $source = 1;
    $this->fixture->map($source, 'AppZap\\PHPFramework\\Tests\\Unit\\Orm\\ItemWithoutRepo');
  }

}
