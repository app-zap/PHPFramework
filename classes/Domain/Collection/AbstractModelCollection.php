<?php
namespace AppZap\PHPFramework\Domain\Collection;

use AppZap\PHPFramework\Domain\Model\AbstractModel;

abstract class AbstractModelCollection implements \Iterator, \Countable {

  /**
   * @var array
   */
  protected $items = [];

  /**
   * @param AbstractModel $model
   */
  public function add(AbstractModel $model) {
    $this->items[spl_object_hash($model)] = $model;
  }

  /**
   * @param AbstractModel $model
   */
  public function remove(AbstractModel $model) {
    unset($this->items[spl_object_hash($model)] );
  }

  /**
   * @param AbstractModelCollection $itemsToRemove
   */
  public function removeItems(AbstractModelCollection $itemsToRemove) {
    foreach ($itemsToRemove as $item) {
      $this->remove($item);
    }
  }

  /**
   * @param $id
   * @return AbstractModel
   */
  public function getById($id) {
    $id = (int) $id;
    foreach ($this->items as $item) {
      /** @var AbstractModel $item */
      if ($item->getId() === $id) {
        return $item;
      }
    }
    return NULL;
  }

  /**
   * @return AbstractModel
   */
  public function current() {
    return current($this->items);
  }

  /**
   * @return AbstractModel
   */
  public function next() {
    return next($this->items);
  }

  /**
   * @return string
   */
  public function key() {
    return key($this->items);
  }

  /**
   *
   */
  public function rewind() {
    reset($this->items);
  }

  /**
   * @return bool
   */
  public function valid() {
    $key = key($this->items);
    return ($key !== NULL && $key !== FALSE);
  }

  /**
   * @return int
   */
  public function count() {
    return count($this->items);
  }

}
