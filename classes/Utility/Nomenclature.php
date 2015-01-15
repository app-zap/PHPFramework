<?php
namespace AppZap\PHPFramework\Utility;

class Nomenclature {

  /**
   * \Vendor\MyApp\Domain\Collection\ItemCollection => \Vendor\MyApp\Domain\Repository\ItemRepository
   *
   * @param $collectionClassname
   * @return string
   */
  public static function collectionClassnameToRepositoryClassname($collectionClassname) {
    return str_replace('Collection', 'Repository', $collectionClassname);
  }

  /**
   * my_field => getMyField
   *
   * @param string $fieldName
   * @return string
   */
  public static function fieldnameToGetter($fieldName) {
    return 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName)));
  }

  /**
   * my_field => setMyField
   *
   * @param string $fieldName
   * @return string
   */
  public static function fieldnameToSetter($fieldName) {
    return 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName)));
  }

  /**
   * getMyField => my_field
   *
   * @param string $getterMethodName
   * @return string
   */
  public static function getterToFieldname($getterMethodName) {
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
   * @param $modelClassname
   * @return string
   */
  public static function modelClassnameToCollectionClassname($modelClassname) {
    return str_replace('Model', 'Collection', $modelClassname) . 'Collection';
  }

  /**
   * \Vendor\MyApp\Domain\Model\Item => \Vendor\MyApp\Domain\Repository\ItemRepository
   *
   * @param $modelClassname
   * @return string
   */
  public static function modelClassnameToRepositoryClassname($modelClassname) {
    return str_replace('Model', 'Repository', $modelClassname) . 'Repository';
  }

  /**
   * \Vendor\MyApp\Domain\Repository\ItemRepository => \Vendor\MyApp\Domain\Collection\ItemCollection
   *
   * @param $repositoryClassname
   * @return string
   */
  public static function repositoryClassnameToCollectionClassname($repositoryClassname) {
    return str_replace('Repository', 'Collection', $repositoryClassname);
  }

  /**
   * \Vendor\MyApp\Domain\Repository\ItemRepository => \Vendor\MyApp\Domain\Model\Item
   *
   * @param $repositoryClassname
   * @return string
   */
  public static function repositoryClassnameToModelClassname($repositoryClassname) {
    $modelClassname = str_replace('Repository', 'Model', $repositoryClassname);
    $modelClassname = substr($modelClassname, 0, -strlen('Model'));
    return $modelClassname;
  }

  /**
   * \Vendor\MyApp\Domain\Repository\ItemRepository => item
   *
   * @param $repositoryClassname
   * @return string
   */
  public static function repositoryClassnameToTablename($repositoryClassname) {
    $repositoryClassnameParts = explode('\\', $repositoryClassname);
    $classnameWithoutNamespace = array_pop($repositoryClassnameParts);
    return strtolower(substr($classnameWithoutNamespace, 0, -strlen('Repository')));
  }

}
