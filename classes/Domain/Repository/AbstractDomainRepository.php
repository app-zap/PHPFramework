<?php
namespace AppZap\PHPFramework\Domain\Repository;

use AppZap\PHPFramework\Domain\Collection\AbstractModelCollection;
use AppZap\PHPFramework\Domain\Model\AbstractModel;
use AppZap\PHPFramework\Singleton;
use AppZap\PHPFramework\Utility\Nomenclature;
use AppZap\PHPFramework\Orm\EntityMapper;
use AppZap\PHPFramework\Persistence\DatabaseConnection;
use AppZap\PHPFramework\Persistence\StaticDatabaseConnection;

abstract class AbstractDomainRepository {
  use Singleton;

  /**
   * @var EntityMapper
   */
  protected $entity_mapper;

  /**
   * @var AbstractModelCollection
   */
  protected $known_items;

  /**
   * @var string
   */
  protected $tablename;

  /**
   * @var DatabaseConnection
   */
  protected $db;

  public function __construct() {
    $this->db = StaticDatabaseConnection::getInstance();
    $this->known_items = $this->get_new_collection();
    $this->entity_mapper = EntityMapper::get_instance();
    $this->tablename = Nomenclature::repositoryclassname_to_tablename(get_called_class());
  }

  /**
   * @param int $id
   * @return AbstractModel
   */
  public function find_by_id($id) {
    $item = $this->known_items->get_by_id($id);
    if (is_null($item)) {
      $model = $this->create_identity_model($id);
      $item = $this->entity_mapper->record_to_object($this->db->row($this->tablename, '*', ['id' => (int)$id]), $model);
      $this->known_items->set_item($item);
    }
    return $item;
  }

  /**
   * @return AbstractModelCollection
   */
  public function find_all() {
    return $this->query_many();
  }

  /**
   * @param AbstractModel $object
   */
  public function save(AbstractModel $object) {
    $record = $this->entity_mapper->object_to_record($object);
    if ($record['id']) {
      $where = ['id' => (int)$record['id']];
      $this->db->update($this->tablename, $record, $where);
    } else {
      $insert_id = $this->db->insert($this->tablename, $record);
      $object->set_id($insert_id);
    }
  }

  /**
   * @param array $where
   * @return AbstractModel
   */
  protected function query_one($where = NULL) {
    return $this->record_to_object($this->db->row($this->tablename, '*', $this->scalarize_where($where)));
  }

  /**
   * @param array $where
   * @return AbstractModelCollection
   */
  protected function query_many($where = NULL) {
    $collection = $this->get_new_collection();
    $records = $this->db->select($this->tablename, '*', $this->scalarize_where($where));
    foreach ($records as $record) {
      $collection->set_item($this->record_to_object($record));
    }
    return $collection;
  }

  /**
   * @param array $where
   */
  protected function scalarize_where($where) {
    if (is_array($where)) {
      foreach ($where as $property => $value) {
        $where[$property] = $this->entity_mapper->scalarize_value($value);
      }
    }
    return $where;
  }

  /**
   * @return \AppZap\PHPFramework\Domain\Collection\AbstractModelCollection
   */
  protected function get_new_collection() {
    $collection_classname = Nomenclature::repositoryclassname_to_collectionclassname(get_called_class());
    if (!class_exists($collection_classname)) {
      $collection_classname = 'AppZap\\PHPFramework\\Domain\\Collection\\GenericModelCollection';
    }
    return new $collection_classname;
  }

  /**
   * @param $record
   * @return AbstractModel
   */
  protected function record_to_object($record) {
    return $this->entity_mapper->record_to_object($record, $this->create_empty_model());
  }

  /**
   * @return AbstractModel
   */
  protected function create_empty_model() {
    $model_classname = Nomenclature::repositoryclassname_to_modelclassname(get_called_class());
    /** @var AbstractModel $model */
    $model = new $model_classname();
    return $model;
  }

  /**
   * @param int $id
   * @return AbstractModel
   */
  protected function create_identity_model($id) {
    $model = $this->create_empty_model();
    $model->set_id($id);
    $this->known_items->set_item($model);
    return $model;
  }

}