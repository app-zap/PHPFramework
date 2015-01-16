<?php
namespace AppZap\PHPFramework\Tests\Unit\Persistence;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Persistence\DatabaseConnection;
use AppZap\PHPFramework\Persistence\StaticDatabaseConnection;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;

class DatabaseConnectionTest extends \PHPUnit_Extensions_Database_TestCase {

  /**
   * @var
   */
  protected $pdo;

  /**
   * @var DatabaseConnection
   */
  protected $fixture;

  public function setUp() {
    Configuration::reset();
    $database = 'phpunit_tests';
    $host = '127.0.0.1';
    $password = '';
    $user = 'travis';
    Configuration::set('phpframework', 'db.mysql.database', $database);
    Configuration::set('phpframework', 'db.mysql.host', $host);
    Configuration::set('phpframework', 'db.mysql.password', $password);
    Configuration::set('phpframework', 'db.mysql.user', $user);
    $this->pdo = new \PDO('mysql:host=' . $host . ';dbname=' . $database . ';port=3306', $user, $password);
    StaticDatabaseConnection::reset();
    $this->fixture = StaticDatabaseConnection::getInstance();
  }


  /**
   * Returns the test database connection.
   *
   * @return \PHPUnit_Extensions_Database_DB_IDatabaseConnection
   */
  protected function getConnection() {
    return $this->createDefaultDBConnection($this->pdo);
  }

  /**
   * Returns the test dataset.
   *
   * @return PHPUnit_Extensions_Database_DataSet_IDataSet
   */
  protected function getDataSet() {
    return $this->createXMLDataSet(dirname(__FILE__) . '/_files/sample_data.xml');
  }

  /**
   * @test
   */
  public function isConnected() {
    $this->assertFalse($this->fixture->isConnected());
    $this->fixture->connect();
    $this->assertTrue($this->fixture->isConnected());
  }

  /**
   * @test
   * @expectedException \PDOException
   */
  public function dbConnectionException() {
    Configuration::set('phpframework', 'db.mysql.host', 'non_existing_host');
    $this->fixture->connect();
  }

  /**
   * @test
   */
  public function setCharset() {
    Configuration::set('phpframework', 'db.charset', 'utf8');
    $this->fixture->connect();
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Persistence\DatabaseQueryException
   */
  public function failingQuery() {
    $this->fixture->query('SQL SYNTAX ERROR!');
  }

  /**
   * @test
   */
  public function fields() {
    $fields = $this->fixture->fields('item');
    $this->assertTrue(in_array('title', $fields));
  }

  /**
   * @test
   */
  public function insert() {
    $this->fixture->insert('item', ['title' => 'insert_test']);
  }

  /**
   * @test
   */
  public function insertAndUpdate() {
    $row = ['title' => 'insert_and_update_test'];
    $row['id'] = $this->fixture->insert('item', $row);
    $row['title'] = 'changed title';
    $this->fixture->update('item', $row, ['id' => $row['id']]);
    $queried_row = $this->fixture->row('item', '*', ['id' => $row['id']]);
    $this->assertSame($row['title'], $queried_row['title']);
  }

  /**
   * @test
   */
  public function replace() {
    $row = ['title' => 'insert_and_update_test'];
    $row['id'] = $this->fixture->insert('item', $row);
    $row['title'] = 'changed title';
    $this->fixture->replace('item', $row);
    $queried_row = $this->fixture->row('item', '*', ['id' => $row['id']]);
    $this->assertSame($row['title'], $queried_row['title']);
  }

  /**
   * @test
   */
  public function field() {
    $row = ['title' => 'field_test'];
    $insert_id = $this->fixture->insert('item', $row);
    $this->assertEquals('field_test', $this->fixture->field('item', 'title', ['id' => $insert_id]));
  }

  /**
   * @test
   */
  public function minAndMax() {
    $this->fixture->insert('item', ['title' => 'foo']);
    $this->fixture->insert('item', ['title' => 'bar']);
    $this->assertGreaterThan($this->fixture->min('item', 'id'), $this->fixture->max('item', 'id'));
  }

  /**
   * @test
   */
  public function emptyInsert() {
    $this->fixture->insert('item', []);
  }

  /**
   * @test
   */
  public function sum() {
    $this->fixture->insert('item', ['title' => 'foo']);
    $this->fixture->insert('item', ['title' => 'bar']);
    $this->assertGreaterThanOrEqual(3, $this->fixture->sum('item', 'id'));
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Persistence\DatabaseQueryException
   */
  public function insertIntoNotExistingTable() {
    $this->fixture->insert('not_existing_table', ['title' => 'bar']);
  }

  /**
   * @test
   */
  public function valueNull() {
    $insert_id = $this->fixture->insert('item', []);
    $insert_id2 = $this->fixture->insert('item', ['id' => NULL]);
    $this->assertGreaterThan($insert_id, $insert_id2);
  }

  /**
   * @test
   * @expectedException \InvalidArgumentException
   * @expectedExceptionCode 1409767864
   */
  public function whereString() {
    $this->fixture->select('item', '*', 'id = 1');
  }

  /**
   * @test
   */
  public function whereNot() {
    $this->fixture->insert('item', ['title' => 'foo']);
    $this->fixture->insert('item', ['title' => 'bar']);
    $row = $this->fixture->row('item', '*', ['title!' => 'foo']);
    $this->assertNotEquals('foo', $row['title']);
  }

  /**
   * @test
   */
  public function whereLike() {
    $insert_id = $this->fixture->insert('item', ['title' => 'fooliketestbaz']);
    $row = $this->fixture->row('item', '*', ['title?' => '%liketest%']);
    $this->assertEquals($insert_id, $row['id']);
  }

  /**
   * @test
   */
  public function whereMultiple() {
    $insert1 = $this->fixture->insert('item', ['title' => 'foo']);
    $insert2 = $this->fixture->insert('item', ['title' => 'bar']);
    $rows = $this->fixture->select('item', '*', ['id' => [$insert1, $insert2]], 'id ASC');
    $this->assertEquals(2, count($rows));
    $this->assertEquals(min($insert1, $insert2), $rows[0]['id']);
    $this->assertEquals(max($insert1, $insert2), $rows[1]['id']);
  }

  /**
   * @test
   */
  public function whereNotMultiple() {
    $insert1 = $this->fixture->insert('item', ['title' => 'wherenotmultipletest']);
    $insert2 = $this->fixture->insert('item', ['title' => 'wherenotmultipletest']);
    $insert3 = $this->fixture->insert('item', ['title' => 'wherenotmultipletest']);
    $rows = $this->fixture->select('item', '*', ['id!' => [$insert1, $insert2], 'title' => 'wherenotmultipletest']);
    $this->assertEquals(1, count($rows));
    $this->assertEquals($insert3, $rows[0]['id']);
  }

  /**
   * @test
   */
  public function whereLikeMultiple() {
    $this->fixture->delete('item');
    $insert1 = $this->fixture->insert('item', ['title' => 'wherelikemultipletest###']);
    $insert2 = $this->fixture->insert('item', ['title' => '###wheremultipleliketest']);
    $rows = $this->fixture->select('item', '*', ['title?' => ['wherelikemultipletest%', '%wheremultipleliketest']], 'id DESC');
    $this->assertEquals(2, count($rows));
    $this->assertEquals(max($insert1, $insert2), $rows[0]['id']);
    $this->assertEquals(min($insert1, $insert2), $rows[1]['id']);
  }

  /**
   * @test
   */
  public function whereInequal() {
    $this->fixture->delete('item');
    $insertItems = 10;
    for ($i = 1;$i <= $insertItems;$i++) {
      $this->fixture->insert('item', ['title' => $i]);
    }
    $this->assertSame(10, $this->fixture->count('item'));
    $this->assertSame(2, $this->fixture->count('item', ['title<' => 3]));
    $this->assertSame(4, $this->fixture->count('item', ['title<=' => 4]));
    $this->assertSame(5, $this->fixture->count('item', ['title>' => 5]));
    $this->assertSame(8, $this->fixture->count('item', ['title>=' => 3]));
  }

  /**
   * @test
   */
  public function whereInequalMultiple() {
    $this->fixture->delete('item');
    $insertItems = 10;
    for ($i = 1;$i <= $insertItems;$i++) {
      $this->fixture->insert('item', ['title' => $i]);
    }
    $this->assertSame(10, $this->fixture->count('item'));
    $this->assertSame(2, $this->fixture->count('item', ['title<' => [3]]));
    $this->assertSame(4, $this->fixture->count('item', ['title<=' => [4, 5]]));
    $this->assertSame(4, $this->fixture->count('item', ['title>' => [5, 6]]));
    $this->assertSame(8, $this->fixture->count('item', ['title>=' => [0, 3]]));
  }

  /**
   * @test
   */
  public function whereNull() {
    $this->fixture->delete('item');
    $this->fixture->insert('item', []);
    $this->fixture->insert('item', []);
    $this->fixture->insert('item', ['title' => 'foo']);
    $this->assertSame(3, $this->fixture->count('item'));
    $this->assertSame(2, $this->fixture->count('item', ['title' => NULL]));
    $this->assertSame(1, $this->fixture->count('item', ['title!' => NULL]));
  }

  /**
   * @test
   */
  public function emptyWhere() {
    $this->fixture->delete('item');
    $this->fixture->insert('item', ['title' => 'foo']);
    $this->fixture->insert('item', ['title' => 'bar']);
    $this->assertSame(2, $this->fixture->count('item'));
    $this->assertSame(2, $this->fixture->count('item', []));
  }

  /**
   * @test
   */
  public function delete() {
    $this->fixture->insert('item', ['title' => '1']);
    $this->fixture->insert('item', ['title' => '2']);
    $todelete = $this->fixture->insert('item', ['title' => '3']);
    $count = $this->fixture->count('item');
    $this->fixture->delete('item', ['id' => $todelete]);
    $this->assertSame($count-1, $this->fixture->count('item'));
    $this->fixture->delete('item');
    $this->assertSame(0, $this->fixture->count('item'));
  }

  /**
   * @test
   */
  public function truncate() {
    $this->fixture->insert('item', ['title' => '1']);
    $this->fixture->insert('item', ['title' => '2']);
    $this->fixture->truncate('item');
    $this->assertSame(0, $this->fixture->count('item'));
    $this->assertEquals('1', $this->fixture->insert('item'));
    $this->assertSame(1, $this->fixture->count('item'));
  }

}
