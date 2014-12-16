<?php
namespace AppZap\PHPFramework\Tests\Unit\Mvc;

use AppZap\PHPFramework\Mvc\View\AbstractView;

class TestView extends AbstractView {
  protected function get_rendering_engine() {
  }
}

class AbstractViewTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var AbstractView
   */
  protected $view;

  public function setUp() {
    $this->view = new TestView();
  }

  /**
   * @test
   */
  public function setHeader() {
    $this->view->header('foo', 'bar');
  }

  /**
   * @test
   */
  public function templateVariables() {
    $this->view->set('foo', 'bar');
    $this->assertSame('bar', $this->view->get('foo'));
    $this->assertSame('bar', $this->view->get('foo', 'baz'));
    $this->assertSame('baz', $this->view->get('_foo', 'baz'));
  }

  /**
   * @test
   */
  public function write() {
    ob_start();
    $this->view->write('bar');
    $this->assertSame('bar', ob_get_clean());
  }

  /**
   * @test
   */
  public function redirect() {
    $this->view->redirect('/');
  }

}