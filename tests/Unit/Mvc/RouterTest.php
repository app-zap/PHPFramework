<?php
namespace AppZap\PHPFramework\Tests\Mvc;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mvc\Router;

class RouterMock extends Router {
  public function _enhanceRegex($regex) {
    return $this->enhanceRegex($regex);
  }
}

class Responder_Index {
}

class Responder_Foo {
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
    $this->loadRoutesFile('this_file_doesnt_even_exist');
    new Router('/');
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\InvalidHttpResponderException
   * @expectedExceptionCode 1415129223
   */
  public function classNotExisting() {
    $this->loadRoutesFile('routes_class_not_existing');
    new Router('/');
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\InvalidHttpResponderException
   * @expectedExceptionCode 1415135585
   */
  public function noReturn() {
    $this->loadRoutesFile('no_return');
    new Router('/');
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\InvalidHttpResponderException
   * @expectedExceptionCode 1415129333
   */
  public function neitherClassNorCallableButObject() {
    $this->loadRoutesFile('neither_class_nor_callable');
    new Router('/object');
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\InvalidHttpResponderException
   * @expectedExceptionCode 1415129333
   */
  public function neitherClassNorCallableButInteger() {
    $this->loadRoutesFile('neither_class_nor_callable');
    new Router('/integer');
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\InvalidHttpResponderException
   * @expectedExceptionCode 1415136995
   */
  public function resourceNotRoutable() {
    $this->loadRoutesFile('classnames');
    new Router('/this/resource/doesnt/even/exist');
  }

  /**
   * @test
   */
  public function routeToClassNames() {
    $this->loadRoutesFile('classnames');
    $router = new Router('/');
    $responder_class = $router->getResponder();
    $this->assertTrue(class_exists($responder_class));
    $this->assertSame('\AppZap\PHPFramework\Tests\Mvc\Responder_Index', $responder_class);
    $router = new Router('/foo');
    $responder_class = $router->getResponder();
    $this->assertTrue(class_exists($responder_class));
    $this->assertSame('\AppZap\PHPFramework\Tests\Mvc\Responder_Foo', $responder_class);
  }

  /**
   * @test
   */
  public function routeToCallables() {
    $this->loadRoutesFile('callables');
    $router = new Router('/');
    /** @var callable $responder_callable */
    $responder_callable = $router->getResponder();
    $this->assertTrue(is_callable($responder_callable));
    $this->assertSame('index', call_user_func($responder_callable, $router->getParameters()));
    $router = new Router('/user/42');
    /** @var callable $responder_callable */
    $responder_callable = $router->getResponder();
    $this->assertTrue(is_callable($responder_callable));
    $this->assertSame('user:42', call_user_func($responder_callable, $router->getParameters()));
  }

  /**
   * @test
   */
  public function routeWithSubpaths() {
    $this->loadRoutesFile('subpaths');
    $router = new Router('/user/42/group/23/edit/');
    /** @var callable $responder_callable */
    $responder_callable = $router->getResponder();
    $this->assertTrue(is_callable($responder_callable));
    $this->assertSame('user:42:group:23:edit', call_user_func($responder_callable, $router->getParameters()));
  }

  /**
   * @test
   */
  public function enhanceRegex() {
    $this->loadRoutesFile('classnames');
    $router = new RouterMock('/');
    $expressions = [
      '.' => '|^$|',
      '|^/$|' => '|^/$|',
      'foo/' => '|^foo/$|',
      'foo/%' => '|^foo/|',
      '%foo/' => '|foo/$|',
      '/user/?/%' => '|^/user/([a-z0-9]*)/|',
    ];
    foreach ($expressions as $before => $after) {
      $this->assertSame($after, $router->_enhanceRegex($before));
    }
  }

  /**
   * @param string $filename
   */
  protected function loadRoutesFile($filename) {
    $routes_file = dirname(__FILE__) . '/_routesfiles/' . $filename . '.php';
    Configuration::set('application', 'routes_file', $routes_file);
  }

}