<?php
namespace AppZap\PHPFramework\Orm;

use AppZap\PHPFramework\Domain\Model\AbstractModel;
use AppZap\PHPFramework\Singleton;

class EntityMapper {
  use Singleton;

  /**
   * @param array $record
   * @param AbstractModel $object
   * @return AbstractModel
   */
  public function record_to_object($record, AbstractModel $object) {
    if (!is_array($record)) {
      return NULL;
    }
    /** @var AbstractModel $object */
    foreach ($record as $key => $value) {
      $setter = 'set_' . $key;
      if (method_exists($object, $setter)) {
        call_user_func([$object, $setter], $value);
      }
    }
    return $object;
  }

  /**
   * @param AbstractModel $object
   * @return array
   */
  public function object_to_record(AbstractModel $object) {
    $record = [];
    foreach (get_class_methods($object) as $method_name) {
      if (substr($method_name, 0, 4) == 'get_') {
        $field_name = substr($method_name, 4);
        $value = call_user_func([$object, $method_name]);
        $value = $this->scalarize_value($value);
        $record[$field_name] = $value;
      }
    }
    return $record;
  }

  /**
   * @param $value
   * @return string
   */
  public function scalarize_value($value) {
    if ($value instanceof AbstractModel) {
      $value = (string) $value->get_id();
    } elseif ($value instanceof \DateTime) {
      $value = (string) $value->getTimestamp();
    }
    return (string) $value;
  }

}