<?php
namespace AppZap\PHPFramework\Orm;

use AppZap\PHPFramework\Domain\Model\AbstractModel;
use AppZap\PHPFramework\Singleton;
use AppZap\PHPFramework\Utility\Nomenclature;

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
    foreach ($record as $fieldname => $value) {
      $setter = Nomenclature::fieldname_to_setter($fieldname);
      if (method_exists($object, $setter)) {
        call_user_func([$object, $setter], $value);
        continue;
      }
      // @deprecated: Since: 1.4, Removal: 1.6, Reason: Use lower camel cased setters instead
      $setter = 'set_' . $fieldname;
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
    foreach (get_class_methods($object) as $methodName) {
      $fieldname = Nomenclature::getter_to_fieldname($methodName);
      if (is_string($fieldname)) {
        $value = call_user_func([$object, $methodName]);
        $value = $this->scalarize_value($value);
        $record[$fieldname] = $value;
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
      $value = (string) $value->getId();
    } elseif ($value instanceof \DateTime) {
      $value = (string) $value->getTimestamp();
    }
    return (string) $value;
  }

}
