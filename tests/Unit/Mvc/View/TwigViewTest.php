<?php
namespace AppZap\PHPFramework\Tests\Unit\Mvc\View;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mvc\View\TwigView;

class TwigViewTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var TwigView
   */
  protected $reponse;

  public function setUp() {
    Configuration::set('application', 'templates_directory', dirname(__FILE__) . '/../_templates');
    Configuration::set('phpframework', 'cache.enable', TRUE);
    $this->reponse = new TwigView();
  }

  /**
   * @test
   */
  public function addOutputFilter() {
    $this->reponse->addOutputFilter('foo', function(){});
    $this->assertTrue($this->reponse->hasOutputFilter('foo'));
    $this->assertFalse($this->reponse->hasOutputFilter('bar'));
  }

  /**
   * @test
   */
  public function addOutputFunction() {
    $this->reponse->addOutputFunction('foo', function(){});
    $this->assertTrue($this->reponse->hasOutputFunction('foo'));
    $this->assertFalse($this->reponse->hasOutputFunction('bar'));
  }

}