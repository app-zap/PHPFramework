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
  public function getId() {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id) {
    $this->id = (int) $id;
  }

  /**
   * @return int
   * @deprecated Since 1.4, Removal: 1.6, Reason: Use ->getId() instead
   */
  public function get_id() {
    return $this->getId();
  }

  /**
   * @param int $id
   * @deprecated Since 1.4, Removal: 1.6, Reason: Use ->setId() instead
   */
  public function set_id($id) {
    $this->setId($id);
  }

}
