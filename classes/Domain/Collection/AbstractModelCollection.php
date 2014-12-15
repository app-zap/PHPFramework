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
  public function get_by_id($id) {
    $id = (int) $id;
    foreach ($this->items as $item) {
      /** @var AbstractModel $item */
      if ($item->get_id() === $id) {
        return $item;
      }
    }
    return NULL;
  }

  /**
   * @return mixed
   */
  public function current() {
    return current($this->items);
  }

  /**
   * @return mixed
   */
  public function next() {
    return next($this->items);
  }

  /**
   * @return mixed
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

  /**
   * @param AbstractModel $model
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->add() instead
   */
  public function set_item(AbstractModel $model) {
    $this->add($model);
  }

  /**
   * @param AbstractModel $model
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->remove() instead
   */
  public function remove_item(AbstractModel $model) {
    $this->remove($model);
  }

}
