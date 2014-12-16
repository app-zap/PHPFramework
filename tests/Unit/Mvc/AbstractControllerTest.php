<?php
namespace AppZap\PHPFramework\Tests\Mvc;

use AppZap\PHPFramework\Mvc\AbstractController;
use AppZap\PHPFramework\Mvc\Request;
use AppZap\PHPFramework\Mvc\View\AbstractView;

class TestResponse extends AbstractView {
  protected function getRenderingEngine() {
  }
}

class TestController extends AbstractController {

  /**
   * @return array
   */
  public function _getImplementedMethods() {
    return $this->getImplementedMethods();
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
    $implementedMethods = $this->testController->_getImplementedMethods();
    $this->assertTrue(is_array($implementedMethods));
    $this->assertTrue(in_array('get', $implementedMethods));
    $this->assertSame(1, count($implementedMethods));
  }

}