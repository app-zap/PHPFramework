<?php
namespace AppZap\PHPFramework\Domain\Model;

use AppZap\PHPFramework\Orm\PropertyMapper;

abstract class AbstractModel {

  /**
   * @var int
   */
  protected $id;

  /**
   * @var PropertyMapper
   */
  protected $propertyMapper;

  public function __construct() {
    $this->propertyMapper = new PropertyMapper();
  }

  /**
   * @return int
   */
  public function get_id() {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function set_id($id) {
    $this->id = (int) $id;
  }

}
