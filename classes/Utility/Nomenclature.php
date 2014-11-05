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
