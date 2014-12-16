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

  /**
   *
   */
  public function __construct() {
    $this->db = StaticDatabaseConnection::getInstance();
    $this->known_items = $this->getNewCollection();
    $this->entity_mapper = EntityMapper::get_instance();
    $this->tablename = Nomenclature::repositoryclassname_to_tablename(get_called_class());
  }

  /**
   * @param int $id
   * @return AbstractModel
   */
  public function findById($id) {
    $item = $this->known_items->getById($id);
    if (is_null($item)) {
      $model = $this->createIdentityModel($id);
      $item = $this->entity_mapper->recordToObject($this->db->row($this->tablename, '*', ['id' => (int)$id]), $model);
      if ($item instanceof AbstractModel) {
        $this->known_items->add($item);
      }
    }
    return $item;
  }

  /**
   * @return AbstractModelCollection
   */
  public function findAll() {
    return $this->queryMany();
  }

  /**
   * @param AbstractModel $object
   */
  public function save(AbstractModel $object) {
    $record = $this->entity_mapper->objectToRecord($object);
    if ($record['id']) {
      $where = ['id' => (int)$record['id']];
      $this->db->update($this->tablename, $record, $where);
    } else {
      $insertId = $this->db->insert($this->tablename, $record);
      $object->setId($insertId);
    }
  }

  /**
   * @param array $where
   * @return AbstractModel
   */
  protected function queryOne($where = NULL) {
    return $this->recordToObject($this->db->row($this->tablename, '*', $this->scalarizeWhere($where)));
  }

  /**
   * @param array $where
   * @return AbstractModelCollection
   */
  protected function queryMany($where = NULL) {
    $collection = $this->getNewCollection();
    $records = $this->db->select($this->tablename, '*', $this->scalarizeWhere($where));
    foreach ($records as $record) {
      $collection->add($this->recordToObject($record));
    }
    return $collection;
  }

  /**
   * @param array $where
   * @return string
   */
  protected function scalarizeWhere($where) {
    if (is_array($where)) {
      foreach ($where as $property => $value) {
        $where[$property] = $this->entity_mapper->scalarizeValue($value);
      }
    }
    return $where;
  }

  /**
   * @return AbstractModelCollection
   */
  protected function getNewCollection() {
    $collectionClassname = Nomenclature::repositoryclassname_to_collectionclassname(get_called_class());
    if (!class_exists($collectionClassname)) {
      $collectionClassname = 'AppZap\\PHPFramework\\Domain\\Collection\\GenericModelCollection';
    }
    return new $collectionClassname;
  }

  /**
   * @param $record
   * @return AbstractModel
   */
  protected function recordToObject($record) {
    return $this->entity_mapper->recordToObject($record, $this->createEmptyModel());
  }

  /**
   * @return AbstractModel
   */
  protected function createEmptyModel() {
    $modelClassname = Nomenclature::repositoryclassname_to_modelclassname(get_called_class());
    /** @var AbstractModel $model */
    $model = new $modelClassname();
    return $model;
  }

  /**
   * @param int $id
   * @return AbstractModel
   */
  protected function createIdentityModel($id) {
    $model = $this->createEmptyModel();
    $model->setId($id);
    $this->known_items->add($model);
    return $model;
  }

  /**
   * @param int $id
   * @return AbstractModel
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->findById() instead
   */
  public function find_by_id($id) {
    return $this->findById($id);
  }

  /**
   * @return AbstractModelCollection
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->findAll() instead
   */
  public function find_all() {
    return $this->findAll();
  }

  /**
   * @param array $where
   * @return AbstractModel
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->queryOne() instead
   */
  protected function query_one($where = NULL) {
    return $this->queryOne($where);
  }

  /**
   * @param array $where
   * @return AbstractModelCollection
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->queryMany() instead
   */
  protected function query_many($where = NULL) {
    return $this->queryMany($where);
  }

  /**
   * @param int $id
   * @return AbstractModel
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->createIdentityModel() instead
   */
  protected function create_identity_model($id) {
    return $this->createIdentityModel($id);
  }

}
