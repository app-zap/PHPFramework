<?php
namespace AppZap\PHPFramework\Tests\Mvc;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mvc\AbstractController;
use AppZap\PHPFramework\Mvc\Request;
use AppZap\PHPFramework\Mvc\Responder\CallableResponder;
use AppZap\PHPFramework\Mvc\Responder\ControllerResponder;
use AppZap\PHPFramework\Mvc\Responder\SubpathResponder;
use AppZap\PHPFramework\Mvc\Routing\Router;

class Responder_Index extends AbstractController {
}

class Responder_Foo extends AbstractController {
}

class RouterTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    Configuration::reset();
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\ApplicationPartMissingException
   * @expectedExceptionCode 1415134009
   */
  public function routesfileNotSet() {
    new Router('/');
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\ApplicationPartMissingException
   * @expectedExceptionCode 1415134009
   */
  public function routesfileMissing() {
    $this->load_routes_file('this_file_doesnt_even_exist');
    new Router('/');
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\InvalidHttpResponderException
   * @expectedExceptionCode 1415135585
   */
  public function noReturn() {
    $this->load_routes_file('no_return');
    new Router('/');
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\InvalidHttpResponderException
   * @expectedExceptionCode 1415136995
   */
  public function resourceNotRoutable() {
    $this->load_routes_file('classnames');
    new Router('/this/resource/doesnt/even/exist');
  }

  /**
   * @test
   */
  public function routeToClassNames() {
    $this->load_routes_file('classnames');
    $router = new Router('/');
    /** @var ControllerResponder $responder */
    $responder = $router->get_responder();
    $this->assertTrue($responder instanceof ControllerResponder);
    $this->assertSame('AppZap\PHPFramework\Tests\Mvc\Responder_Index', get_class($responder->getController()));
    $router = new Router('/foo');
    $responder = $router->get_responder();
    $this->assertTrue($responder instanceof ControllerResponder);
    $this->assertSame('AppZap\PHPFramework\Tests\Mvc\Responder_Foo', get_class($responder->getController()));
  }

  /**
   * @test
   */
  public function routeToCallables() {
    $this->load_routes_file('callables');
    $router = new Router('/');
    $responder = $router->get_responder();
    $this->assertTrue($responder instanceof CallableResponder);
    $this->assertSame('index', $responder->dispatch(new Request('cli', $router->get_parameters())));
    $router = new Router('/user/42');
    $responder = $router->get_responder();
    $this->assertTrue($responder instanceof CallableResponder);
    $this->assertSame('user:42', $responder->dispatch(new Request('cli', $router->get_parameters())));
  }

  /**
   * @test
   */
  public function routeWithSubpaths() {
    $this->markTestIncomplete();
    $this->load_routes_file('subpaths');
    $router = new Router('/user/42/group/23/edit/');
    /** @var callable $responder_callable */
    $responder = $router->get_responder();
    $this->assertTrue($responder instanceof SubpathResponder);
    $this->assertSame('user:42:group:23:edit', $responder->dispatch(new Request('cli', $router->get_parameters())));
  }

  /**
   * @param $filename
   */
  protected function load_routes_file($name) {
    $routes_file = dirname(__FILE__) . '/_routesfiles/' . $name . '.php';
    Configuration::set('application', 'routes_file', $routes_file);
  }

}