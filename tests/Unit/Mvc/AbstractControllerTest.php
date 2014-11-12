<?php
namespace AppZap\PHPFramework\Tests\Mvc;

use AppZap\PHPFramework\Mvc\AbstractController;
use AppZap\PHPFramework\Mvc\Request;
use AppZap\PHPFramework\Mvc\View\AbstractView;

class TestResponse extends AbstractView {
  public function __construct() {
  }
  protected function get_rendering_engine() {
  }
}

class TestController extends AbstractController {

  /**
   * @return array
   */
  public function _get_implemented_methods() {
    return $this->get_implemented_methods();
  }

  public function get($params) {
  }

}

class AbstractControllerTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var TestController
   */
  protected $testController;

  public function setUp() {
    $this->testController = new TestController(new Request('cli'), new TestResponse());
  }

  /**
   * @test
   */
  public function implementedMethods() {
    $implementedMethods = $this->testController->_get_implemented_methods();
    $this->assertTrue(is_array($implementedMethods));
    $this->assertTrue(in_array('get', $implementedMethods));
    $this->assertSame(1, count($implementedMethods));
  }

}