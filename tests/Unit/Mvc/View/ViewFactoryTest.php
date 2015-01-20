<?php
namespace AppZap\PHPFramework\Tests\Unit\Mvc\View;

use AppZap\PHPFramework\Mvc\View\AbstractView;
use AppZap\PHPFramework\Mvc\View\TwigView;
use AppZap\PHPFramework\Mvc\View\ViewFactory;
use AppZap\PHPFramework\SignalSlot\Dispatcher as SignalSlotDispatcher;

class TestCustomView extends AbstractView {

  /**
   * @return \Twig_Environment
   */
  protected function getRenderingEngine() {
  }

}

class InvalidCustomView {}

class ViewFactoryTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var ViewFactory
   */
  protected $viewFactory;

  public function setUp() {
    $this->viewFactory = ViewFactory::getInstance();
  }

  /**
   * @test
   */
  public function defaultViewClass() {
    $view = $this->viewFactory->createView();
    $this->assertTrue($view instanceof TwigView);
  }

  /**
   * @test
   */
  public function customViewClass() {
    SignalSlotDispatcher::registerSlot(ViewFactory::SIGNAL_VIEW_CLASSNAME, function(&$classname){
      $classname = 'AppZap\PHPFramework\Tests\Unit\Mvc\View\TestCustomView';
    });
    $view = $this->viewFactory->createView();
    $this->assertTrue($view instanceof TestCustomView);
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\View\ViewFactoryException
   * @expectedExceptionCode 1421760637
   */
  public function notExistingCustomViewClass() {
    SignalSlotDispatcher::registerSlot(ViewFactory::SIGNAL_VIEW_CLASSNAME, function(&$classname){
      $classname = 'NotExistingClass';
    });
    $this->viewFactory->createView();
  }

  /**
   * @test
   * @expectedException \AppZap\PHPFramework\Mvc\View\ViewFactoryException
   * @expectedExceptionCode 1421760697
   */
  public function invalidCustomViewClass() {
    SignalSlotDispatcher::registerSlot(ViewFactory::SIGNAL_VIEW_CLASSNAME, function(&$classname){
      $classname = 'AppZap\PHPFramework\Tests\Unit\Mvc\View\InvalidCustomView';
    });
    $this->viewFactory->createView();
  }

}
