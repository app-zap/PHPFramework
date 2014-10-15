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
    'repository' => '\\Vendor\\Project\\Domain\\Repository\\ItemRepository',
    'table' => 'item',
  ];

  /**
   * @test
   */
  public function collectionclassname_to_repositoryclassname() {
    $this->assertSame($this->names['repository'], Nomenclature::collectionclassname_to_repositoryclassname($this->names['collection']));
  }

  /**
   * @test
   */
  public function modelclassname_to_collectionclassname() {
    $this->assertSame($this->names['collection'], Nomenclature::modelclassname_to_collectionclassname($this->names['model']));
  }

  /**
   * @test
   */
  public function modelclassname_to_repositoryclassname() {
    $this->assertSame($this->names['repository'], Nomenclature::modelclassname_to_repositoryclassname($this->names['model']));
  }

  /**
   * @test
   */
  public function repositoryclassname_to_collectionclassname() {
    $this->assertSame($this->names['collection'], Nomenclature::repositoryclassname_to_collectionclassname($this->names['repository']));
  }

  /**
   * @test
   */
  public function repositoryclassname_to_modelclassname() {
    $this->assertSame($this->names['model'], Nomenclature::repositoryclassname_to_modelclassname($this->names['repository']));
  }

  /**
   * @test
   */
  public function repositoryclassname_to_tablename() {
    $this->assertSame($this->names['table'], Nomenclature::repositoryclassname_to_tablename($this->names['repository']));
  }



}