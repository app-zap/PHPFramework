<?php
namespace AppZap\PHPFramework\Tests\Unit\Utility;

use AppZap\PHPFramework\Utility\Nomenclature;

class NomenclatureTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var array
   */
  protected $names = [
    'collection' => '\\Vendor\\Project\\Domain\\Collection\\ItemCollection',
    'model' => '\\Vendor\Project\Domain\Model\Item',
    'fieldname' => 'my_field',
    'getter' => 'getMyField',
    'repository' => '\\Vendor\\Project\\Domain\\Repository\\ItemRepository',
    'setter' => 'setMyField',
    'table' => 'item',
  ];

  /**
   * @test
   */
  public function collectionclassnameToRepositoryclassname() {
    $this->assertSame($this->names['repository'], Nomenclature::collectionClassnameToRepositoryClassname($this->names['collection']));
  }

  /**
   * @test
   */
  public function fieldnameToGetter() {
    $this->assertSame($this->names['getter'], Nomenclature::fieldnameToGetter($this->names['fieldname']));
  }

  /**
   * @test
   */
  public function fieldnameToSetter() {
    $this->assertSame($this->names['setter'], Nomenclature::fieldnameToSetter($this->names['fieldname']));
  }

  /**
   * @test
   */
  public function getterToFieldname() {
    $this->assertSame($this->names['fieldname'], Nomenclature::getterToFieldname($this->names['getter']));
    $this->assertSame($this->names['fieldname'], Nomenclature::getterToFieldname('get_my_field'));
    $this->assertFalse(Nomenclature::getterToFieldname('not_a_getter_method'));
  }

  /**
   * @test
   */
  public function modelClassnameToCollectionClassname() {
    $this->assertSame($this->names['collection'], Nomenclature::modelClassnameToCollectionClassname($this->names['model']));
  }

  /**
   * @test
   */
  public function modelClassnameToRepositoryClassname() {
    $this->assertSame($this->names['repository'], Nomenclature::modelClassnameToRepositoryClassname($this->names['model']));
  }

  /**
   * @test
   */
  public function repositoryClassnameToCollectionClassname() {
    $this->assertSame($this->names['collection'], Nomenclature::repositoryClassnameToCollectionClassname($this->names['repository']));
  }

  /**
   * @test
   */
  public function repositoryclassnameToModelclassname() {
    $this->assertSame($this->names['model'], Nomenclature::repositoryClassnameToModelClassname($this->names['repository']));
  }

  /**
   * @test
   */
  public function repositoryclassnameToTablename() {
    $this->assertSame($this->names['table'], Nomenclature::repositoryClassnameToTablename($this->names['repository']));
  }

}
