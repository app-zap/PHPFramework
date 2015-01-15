<?php
namespace AppZap\PHPFramework\Tests\Mvc;

use AppZap\PHPFramework\Mvc\Request;

class RequestTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    $_GET = [];
    $_POST = [];
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\MethodNotSupportedException
   * @expectedExceptionCode 1415273543
   */
  public function constructWithUnsupportedRequestMethod() {
    new Request('_unsupported');
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\ValueSourceNotSupportedException
   * @expectedExceptionCode 1415273682
   */
  public function readFromUnsupportedSource() {
    $request = new Request('cli');
    $request->get('foo');
  }

  /**
   * @test
   */
  public function getGetParameter() {
    $_GET['foo'] = 'bar';
    $request = new Request('get');
    $this->assertSame('bar', $request->get('foo'));
    $this->assertSame('bar', $request->get('foo', 'baz', TRUE));
  }

  /**
   * @test
   */
  public function getGetParameterWithFallback() {
    $_GET['_unused'] = 'abc';
    $_POST['foo'] = 'bar';
    $request = new Request('get');
    $this->assertSame('bar', $request->get('foo'));
    $this->assertSame('baz', $request->get('foo', 'baz', TRUE));
  }

  /**
   * @test
   */
  public function getHeadParameter() {
    $_GET['foo'] = 'bar';
    $request = new Request('head');
    $this->assertSame('bar', $request->get('foo'));
    $this->assertSame('bar', $request->get('foo', 'baz', TRUE));
  }

  /**
   * @test
   */
  public function getHeadParameterWithFallback() {
    $_GET['_unused'] = 'abc';
    $_POST['foo'] = 'bar';
    $request = new Request('head');
    $this->assertSame('bar', $request->get('foo'));
    $this->assertSame('baz', $request->get('foo', 'baz', TRUE));
  }

  /**
   * @test
   */
  public function getPostParameter() {
    $_POST['foo'] = 'bar';
    $request = new Request('post');
    $this->assertSame('bar', $request->get('foo'));
    $this->assertSame('bar', $request->get('foo', 'baz', TRUE));
  }

  /**
   * @test
   */
  public function getPostParameterWithFallback() {
    $_POST['_unused'] = 'abc';
    $_GET['foo'] = 'bar';
    $request = new Request('post');
    $this->assertSame('bar', $request->get('foo'));
    $this->assertSame('baz', $request->get('foo', 'baz', TRUE));
  }

  /**
   * @test
   */
  public function getNotPresentParameter() {
    $request = new Request('get');
    $this->assertNull($request->get('foo'));
  }

  /**
   * @test
   */
  public function readRequestBodyFromCli() {
    $request = new Request('cli');
    $request->body();
  }

}