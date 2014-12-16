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
    $this->assertSame($this->names['repository'], Nomenclature::collectionclassname_to_repositoryclassname($this->names['collection']));
  }

  /**
   * @test
   */
  public function fieldnameToGetter() {
    $this->assertSame($this->names['getter'], Nomenclature::fieldname_to_getter($this->names['fieldname']));
  }

  /**
   * @test
   */
  public function fieldnameToSetter() {
    $this->assertSame($this->names['setter'], Nomenclature::fieldname_to_setter($this->names['fieldname']));
  }

  /**
   * @test
   */
  public function getterToFieldname() {
    $this->assertSame($this->names['fieldname'], Nomenclature::getter_to_fieldname($this->names['getter']));
    $this->assertSame($this->names['fieldname'], Nomenclature::getter_to_fieldname('get_my_field'));
    $this->assertFalse(Nomenclature::getter_to_fieldname('not_a_getter_method'));
  }

  /**
   * @test
   */
  public function modelclassnameToCollectionclassname() {
    $this->assertSame($this->names['collection'], Nomenclature::modelclassname_to_collectionclassname($this->names['model']));
  }

  /**
   * @test
   */
  public function modelclassnameToRepositoryclassname() {
    $this->assertSame($this->names['repository'], Nomenclature::modelclassname_to_repositoryclassname($this->names['model']));
  }

  /**
   * @test
   */
  public function repositoryclassnameToCollectionclassname() {
    $this->assertSame($this->names['collection'], Nomenclature::repositoryclassname_to_collectionclassname($this->names['repository']));
  }

  /**
   * @test
   */
  public function repositoryclassnameToModelclassname() {
    $this->assertSame($this->names['model'], Nomenclature::repositoryclassname_to_modelclassname($this->names['repository']));
  }

  /**
   * @test
   */
  public function repositoryclassnameToTablename() {
    $this->assertSame($this->names['table'], Nomenclature::repositoryclassname_to_tablename($this->names['repository']));
  }

}
