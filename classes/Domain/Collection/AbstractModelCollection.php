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
  public function set_item(AbstractModel $model) {
    $this->items[spl_object_hash($model)] = $model;
  }

  /**
   * @param AbstractModel $model
   */
  public function remove_item(AbstractModel $model) {
    unset($this->items[spl_object_hash($model)] );
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

}
