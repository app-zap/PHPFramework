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
  protected $entityMapper;

  /**
   * @var AbstractModelCollection
   */
  protected $knownItems;

  /**
   * @var string
   */
  protected $tablename;

  /**
   * @var DatabaseConnection
   */
  protected $db;

  /**
   * @param DatabaseConnection $db
   */
  public function __construct(DatabaseConnection $db = NULL) {
    $this->db = $db ?: StaticDatabaseConnection::getInstance();
    $this->knownItems = $this->getNewCollection();
    $this->entityMapper = EntityMapper::getInstance();
    $this->tablename = Nomenclature::repositoryClassnameToTablename(get_called_class());
  }

  /**
   * @param int $id
   * @return AbstractModel
   */
  public function findById($id) {
    $item = $this->knownItems->getById($id);
    if ($item === NULL) {
      $model = $this->createIdentityModel($id);
      $item = $this->entityMapper->recordToObject($this->db->row($this->tablename, '*', ['id' => (int)$id]), $model);
      if ($item instanceof AbstractModel) {
        $this->knownItems->add($item);
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
    $this->knownItems->add($object);
    $record = $this->entityMapper->objectToRecord($object);
    if ($record['id']) {
      $where = ['id' => (int)$record['id']];
      $this->db->update($this->tablename, $record, $where);
    } else {
      $insertId = $this->db->insert($this->tablename, $record);
      $object->setId($insertId);
    }
  }

  /**
   * @param AbstractModel $object
   */
  public function remove(AbstractModel $object) {
    $this->knownItems->remove($object);
    $this->db->delete($this->tablename, ['id' => $object->getId()]);
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
        $where[$property] = $this->entityMapper->scalarizeValue($value);
      }
    }
    return $where;
  }

  /**
   * @return AbstractModelCollection
   */
  protected function getNewCollection() {
    $collectionClassname = Nomenclature::repositoryClassnameToCollectionClassname(get_called_class());
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
    return $this->entityMapper->recordToObject($record, $this->createEmptyModel());
  }

  /**
   * @return AbstractModel
   */
  protected function createEmptyModel() {
    $modelClassname = Nomenclature::repositoryClassnameToModelClassname(get_called_class());
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
    $this->knownItems->add($model);
    return $model;
  }

}
