<?php
namespace AppZap\PHPFramework\Utility;

class Nomenclature {

  /**
   * \Vendor\MyApp\Domain\Collection\ItemCollection => \Vendor\MyApp\Domain\Repository\ItemRepository
   *
   * @param $collection_classname
   * @return string
   */
  public static function collectionclassname_to_repositoryclassname($collection_classname) {
    return str_replace('Collection', 'Repository', $collection_classname);
  }

  /**
   * my_field => getMyField
   *
   * @param string $fieldName
   * @return string
   */
  public static function fieldname_to_getter($fieldName) {
    return 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName)));
  }

  /**
   * my_field => setMyField
   *
   * @param string $fieldName
   * @return string
   */
  public static function fieldname_to_setter($fieldName) {
    return 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName)));
  }

  /**
   * getMyField => my_field
   *
   * @param string $getterMethodName
   * @return string
   */
  public static function getter_to_fieldname($getterMethodName) {
    if (strpos($getterMethodName, 'get_') === 0) {
      // @deprecated: Since: 1.4, Removal: 1.6, Reason: Use lower camel cased getters instead
      return substr($getterMethodName, strlen('get_'));
    }
    if (strpos($getterMethodName, 'get') === 0) {
      return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", substr($getterMethodName, strlen('get'))));
    }
    return FALSE;
  }

  /**
   * \Vendor\MyApp\Domain\Model\Item => \Vendor\MyApp\Domain\Collection\ItemCollection
   *
   * @param $model_classname
   * @return string
   */
  public static function modelclassname_to_collectionclassname($model_classname) {
    return str_replace('Model', 'Collection', $model_classname) . 'Collection';
  }

  /**
   * \Vendor\MyApp\Domain\Model\Item => \Vendor\MyApp\Domain\Repository\ItemRepository
   *
   * @param $model_classname
   * @return string
   */
  public static function modelclassname_to_repositoryclassname($model_classname) {
    return str_replace('Model', 'Repository', $model_classname) . 'Repository';
  }

  /**
   * \Vendor\MyApp\Domain\Repository\ItemRepository => \Vendor\MyApp\Domain\Collection\ItemCollection
   *
   * @param $repository_classname
   * @return mixed
   */
  public static function repositoryclassname_to_collectionclassname($repository_classname) {
    return str_replace('Repository', 'Collection', $repository_classname);
  }

  /**
   * \Vendor\MyApp\Domain\Repository\ItemRepository => \Vendor\MyApp\Domain\Model\Item
   *
   * @param $repository_classname
   * @return string
   */
  public static function repositoryclassname_to_modelclassname($repository_classname) {
    $model_classname = str_replace('Repository', 'Model', $repository_classname);
    $model_classname = substr($model_classname, 0, -strlen('Model'));
    return $model_classname;
  }

  /**
   * \Vendor\MyApp\Domain\Repository\ItemRepository => item
   *
   * @param $repository_classname
   * @return string
   */
  public static function repositoryclassname_to_tablename($repository_classname) {
    $repository_classname_parts = explode('\\', $repository_classname);
    $classname_without_namespace = array_pop($repository_classname_parts);
    return strtolower(substr($classname_without_namespace, 0, -strlen('Repository')));
  }

}
