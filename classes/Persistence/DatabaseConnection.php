<?php
namespace AppZap\PHPFramework\Persistence;

use AppZap\PHPFramework\Configuration\Configuration;

/**
 * Database wrapper class
 */
class DatabaseConnection {

  const QUERY_MODE_LIKE = 1;
  const QUERY_MODE_NOT = 2;
  const QUERY_MODE_REGULAR = 3;

  /**
   * @var \PDO
   */
  protected $connection = NULL;

  /**
   * Connects to the MySQL server, sets the charset for the connection and
   * selects the database
   */
  public function connect() {
    if (!($this->connection instanceof \PDO)) {
      $dbConfiguration = Configuration::getSection('phpframework', 'db');
      $dsn = 'mysql:host=' . $dbConfiguration['mysql.host'] . ';dbname=' . $dbConfiguration['mysql.database'];
      $this->connection = new \PDO($dsn, $dbConfiguration['mysql.user'], $dbConfiguration['mysql.password']);
      if (isset($dbConfiguration['charset'])) {
        $this->setCharset($dbConfiguration['charset']);
      }
    }
  }

  /**
   * Checks whether the connection to the database is established
   *
   * @return bool
   */
  public function isConnected() {
    return (bool) $this->connection;
  }

  /**
   * Sets the charset for transfer encoding
   *
   * @param string $charset Connection transfer charset
   */
  protected function setCharset($charset) {
    $sql = 'SET NAMES ' . $charset;
    $this->execute($sql);
  }

  /**
   * Executes the passed SQL statement
   *
   * @param string $sql Finally escaped SQL statement
   * @return array Result data of the query
   */
  public function query($sql) {
    $result = $this->execute($sql);
    $rows = [];
    foreach ($result as $row) {
      $rows[] = $row;
    }
    return $rows;
  }

  /**
   * Executes the passed SQL statement
   *
   * @param string $sql Finally escaped SQL statement
   * @param array $parameters
   * @return resource Result data of the query
   * @throws DatabaseQueryException
   */
  public function execute($sql, $parameters = []) {
    $this->connect();
    // execute the query
    $statement = $this->connection->prepare($sql);
    try {
      $success = $statement->execute($parameters);
    } catch(\PDOException $e) {
      // under HHVM we get a \PDOException instead of FALSE if the query fails
      $success = FALSE;
    }
    if ($success === FALSE) {
      throw new DatabaseQueryException('Database query failed. Error: "' . print_r($statement->errorInfo(), 1) . '". Query was: "' . $statement->queryString . '". Parameters: ' . print_r($parameters, 1), 1415219261);
    }
    return $statement->fetchAll();
  }

  /**
   * Returns the auto increment ID of the last query
   *
   * @return int
   */
  public function lastId() {
    $this->connect();
    return $this->connection->lastInsertId();
  }

  /**
   * Lists the fields of a table
   *
   * @param string $table Name of the table
   * @return array with field names
   */
  public function fields($table) {
    $sql = 'SHOW COLUMNS FROM ' . $table;
    $result = $this->query($sql);

    $fields = [];
    foreach ($result as $row) {
      $fields[] = $row['Field'];
    }

    return $fields;
  }

  /**
   * Inserts dataset into the table and returns the auto increment key for it
   *
   * @param string $table Name of the table
   * @param array $input Dataset to insert into the table
   * @param boolean $ignore Use "INSERT IGNORE" for the query
   * @return int
   */
  public function insert($table, $input, $ignore = FALSE) {
    $ignore = $ignore ? ' IGNORE' : '';
    if (count($input)) {
      $values = ' SET ' . $this->values($input);
    } else {
      $values = '(id) VALUES (NULL)';
    }
    $this->execute('INSERT' . $ignore . ' INTO ' . $table . $values);
    return $this->lastId();
  }

  /**
   * Replaces dataset in the table
   *
   * @param string $table Name of the table
   * @param array $input Dataset to replace in the table
   * @return resource
   */
  public function replace($table, $input) {
    return $this->execute('REPLACE INTO ' . $table . ' SET ' . $this->values($input));
  }

  /**
   * Updates datasets in the table
   *
   * @param string $table Name of the table
   * @param array $input Dataset to write over the old one into the table
   * @param array $where Selector for the datasets to overwrite
   * @return resource
   */
  public function update($table, $input, $where) {
    $sql = sprintf(
      'UPDATE %s SET %s%s',
      $table,
      $this->values($input),
      $this->where($where)
    );
    return $this->execute($sql);
  }

  /**
   * Deletes datasets from table
   *
   * @param string $table Name of the table
   * @param array $where Selector for the datasets to delete
   */
  public function delete($table, $where = NULL) {
    $sql = sprintf(
        'DELETE FROM %s%s',
        $table,
        $this->where($where)
    );
    $this->execute($sql);
  }

  /**
   * Selects datasets from table
   *
   * @param string $table Name of the table
   * @param string $select Fields to retrieve from table
   * @param array $where Selector for the datasets to select
   * @param string $order Already escaped content of order clause
   * @param int $offset First index of dataset to retrieve
   * @param int $limit Number of entries to retrieve
   * @return array
   */
  public function select($table, $select = '*', $where = NULL, $order = '', $offset = 0, $limit = NULL) {
    $sql = sprintf(
        'SELECT %s FROM %s%s%s%s',
        $select,
        $table,
        $this->where($where),
        $this->order($order),
        $this->limit($limit),
        $this->offset($offset)
    );
    return $this->query($sql);
  }

  /**
   * Select one row from table or false if there is no row
   *
   * @param string $table Name of the table
   * @param string $select Fields to retrieve from table
   * @param array $where Selector for the datasets to select
   * @param string $order Already escaped content of order clause
   * @return array|boolean
   */
  public function row($table, $select = '*', $where = NULL, $order = NULL) {
    $result = $this->select($table, $select, $where, $order, 0, 1);
    return (count($result) > 0) ? $result[0] : FALSE;
  }

  /**
   * Select one field from table
   *
   * @param string $table Name of the table
   * @param string $field Name of the field to return
   * @param array $where Selector for the datasets to select
   * @param string $order Already escaped content of order clause
   * @return mixed
   */
  public function field($table, $field, $where = NULL, $order = NULL) {
    $result = $this->row($table, $field, $where, $order);
    return $result[$field];
  }

  /**
   * Counts the rows matching the where clause in table
   *
   * @param string $table Name of the table
   * @param array $where Selector for the datasets to select
   * @return int
   */
  public function count($table, $where = NULL) {
    $result = $this->row($table, 'count(1)', $where);
    return ($result) ? (int) $result['count(1)'] : 0;
  }

  /**
   * Selects the minmum of a column or false if there is no data
   *
   * @param string $table Name of the table
   * @param string $column Name of column to retrieve
   * @param array $where Selector for the datasets to select
   * @return int|boolean
   */
  public function min($table, $column, $where = NULL) {
    $result = $this->row($table, 'MIN(`' . $column . '`) as min', $where);
    return ($result) ? $result['min'] : FALSE;
  }

  /**
   * Selects the maximum of a column or false if there is no data
   *
   * @param string $table Name of the table
   * @param string $column Name of column to retrieve
   * @param string|array $where Selector for the datasets to select
   * @return int|boolean
   */
  public function max($table, $column, $where = NULL) {
    $result = $this->row($table, 'MAX(`' . $column . '`) as max', $where);
    return ($result) ? $result['max'] : FALSE;
  }

  /**
   * Selects the sum of a column
   *
   * @param string $table Name of the table
   * @param string $column Name of column to retrieve
   * @param string|array $where Selector for the datasets to select
   * @return int
   */
  public function sum($table, $column, $where = NULL) {
    $result = $this->row($table, 'SUM(`' . $column . '`) as sum', $where);
    return ($result) ? $result['sum'] : 0;
  }

  /**
   * @param array $input
   * @return string
   */
  protected function values($input) {
    $retval = [];
    foreach ($input as $key => $value) {
      if ($value === NULL) {
        $retval[] = '`' . $key . '`' . ' = NULL';
      } else {
        $retval[] = '`' . $key . '`' . ' = ' . $this->escape($value);
      }
    }
    return implode(', ', $retval);

  }

  /**
   * Escape values
   *
   * @param mixed $value
   * @return string
   */
  public function escape($value) {
    $this->connect();
    $value = stripslashes($value);
    return $this->connection->quote((string)$value);
  }

  /**
   * @param string $order
   * @return string
   */
  protected function order($order = '') {
    if ($order) {
      $order = sprintf(
        ' ORDER BY %s',
        $order
      );
    }
    return $order;
  }

  /**
   * @param int $limit
   * @return string
   */
  protected function limit($limit = 0) {
    if ($limit > 0) {
      return sprintf(
          ' LIMIT %d',
          $limit
      );
    } else {
      return '';
    }
  }

  /**
   * @param int $offset
   * @return string
   */
  protected function offset($offset = 0) {
    if ($offset > 0) {
      return sprintf(
          ' OFFSET %d',
          $offset
      );
    } else {
      return '';
    }
  }

  /**
   * @param array $where
   * @param string $method
   * @return string
   * @throws \InvalidArgumentException
   */
  protected function where($where, $method = 'AND') {
    if ($where === NULL) {
      return '';
    }
    if (!is_array($where)) {
      throw new \InvalidArgumentException('where clause has to be an associative array', 1409767864);
    }
    if (!count($where)) {
      return '';
    }

    $constraints = [];
    foreach ($where AS $field => $value) {

      // the last character of the field can modify the query mode:
      switch(substr($field, -1)) {
        case '!':
          $queryMode = self::QUERY_MODE_NOT;
          $field = substr($field, 0, -1);
          break;
        case '?':
          $queryMode = self::QUERY_MODE_LIKE;
          $field = substr($field, 0, -1);
          break;
        default:
          $queryMode = self::QUERY_MODE_REGULAR;
      }

      if ($queryMode === self::QUERY_MODE_LIKE && is_array($value)) {
        // LIKE and multiple values needs a special syntax in SQL
        $value = array_map([$this, 'escape'], $value);
        $constraints[] = '(`' . $field . '` LIKE ' . implode(' OR `' . $field . '` LIKE ', $value) . ')';
      } else {
        if (is_array($value)) {
          $constraints[] = $this->whereMultipleValues($value, $queryMode, $field);
        } elseif($value === NULL) {
          $constraints[] = $this->whereNullValue($queryMode, $field);
        } else {
          $constraints[] = $this->whereSingleValue($value, $queryMode, $field);
        }
      }
    }
    return sprintf(
        ' WHERE %s',
        implode(' ' . $method . ' ', $constraints)
    );
  }

  /**
   * @param array $value
   * @param int $queryMode
   * @param string $field
   * @return string
   */
  protected function whereMultipleValues($value, $queryMode, $field) {
    $value = implode(', ', array_map([$this, 'escape'], $value));
    if ($queryMode === self::QUERY_MODE_NOT) {
      $operand = 'NOT IN';
    } else {
      $operand = 'IN';
    }
    return sprintf('`%s` %s (%s)', $field, $operand, $value);
  }

  /**
   * @param $value
   * @param $queryMode
   * @param $field
   * @return string
   */
  protected function whereSingleValue($value, $queryMode, $field) {
    $value = $this->escape($value);
    switch ($queryMode) {
      case self::QUERY_MODE_LIKE:
        $operand = 'LIKE';
        break;
      case self::QUERY_MODE_NOT:
        $operand = '!=';
        break;
      default:
        $operand = '=';
    }
    return sprintf('`%s` %s %s', $field, $operand, $value);
  }

  /**
   * @param $queryMode
   * @param $field
   * @return string
   */
  protected function whereNullValue($queryMode, $field) {
    switch ($queryMode) {
      case self::QUERY_MODE_NOT:
        $operand = 'IS NOT';
        break;
      case self::QUERY_MODE_LIKE:
      default:
        $operand = 'IS';
    }
    return sprintf('`%s` %s %s', $field, $operand, 'NULL');
  }

}
