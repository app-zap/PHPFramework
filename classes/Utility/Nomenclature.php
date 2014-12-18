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

  /**
   * \Vendor\MyApp\Domain\Collection\ItemCollection => \Vendor\MyApp\Domain\Repository\ItemRepository
   *
   * @param $collectionClassname
   * @return string
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->collectionclassnameToRepositoryclassname() instead
   */
  public static function collectionclassname_to_repositoryclassname($collectionClassname) {
    return self::collectionClassnameToRepositoryClassname($collectionClassname);
  }

  /**
   * my_field => getMyField
   *
   * @param string $fieldName
   * @return string
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->fieldnameToGetter() instead
   */
  public static function fieldname_to_getter($fieldName) {
    return self::fieldnameToGetter($fieldName);
  }

  /**
   * my_field => setMyField
   *
   * @param string $fieldName
   * @return string
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->fieldnameToSetter() instead
   */
  public static function fieldname_to_setter($fieldName) {
    return self::fieldnameToSetter($fieldName);
  }

  /**
   * getMyField => my_field
   *
   * @param string $getterMethodName
   * @return string
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->getterToFieldname() instead
   */
  public static function getter_to_fieldname($getterMethodName) {
    return self::getterToFieldname($getterMethodName);
  }

  /**
   * \Vendor\MyApp\Domain\Model\Item => \Vendor\MyApp\Domain\Collection\ItemCollection
   *
   * @param $modelClassname
   * @return string
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->modelClassnameToCollectionClassname() instead
   */
  public static function modelclassname_to_collectionclassname($modelClassname) {
    return self::modelClassnameToCollectionClassname($modelClassname);
  }

  /**
   * \Vendor\MyApp\Domain\Model\Item => \Vendor\MyApp\Domain\Repository\ItemRepository
   *
   * @param $modelClassname
   * @return string
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->modelClassnameToRepositoryClassname() instead
   */
  public static function modelclassname_to_repositoryclassname($modelClassname) {
    return self::modelClassnameToRepositoryClassname($modelClassname);
  }

  /**
   * \Vendor\MyApp\Domain\Repository\ItemRepository => \Vendor\MyApp\Domain\Collection\ItemCollection
   *
   * @param $repositoryClassname
   * @return string
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->modelClassnameToRepositoryClassname() instead
   */
  public static function repositoryclassname_to_collectionclassname($repositoryClassname) {
    return self::repositoryClassnameToCollectionClassname($repositoryClassname);
  }

  /**
   * \Vendor\MyApp\Domain\Repository\ItemRepository => \Vendor\MyApp\Domain\Model\Item
   *
   * @param $repositoryClassname
   * @return string
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->repositoryClassnameToModelClassname() instead
   */
  public static function repositoryclassname_to_modelclassname($repositoryClassname) {
    return self::repositoryClassnameToModelClassname($repositoryClassname);
  }

  /**
   * \Vendor\MyApp\Domain\Repository\ItemRepository => item
   *
   * @param $repositoryClassname
   * @return string
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->repositoryClassnameToTablename() instead
   */
  public static function repositoryclassname_to_tablename($repositoryClassname) {
    return self::repositoryClassnameToTablename($repositoryClassname);
  }

}
