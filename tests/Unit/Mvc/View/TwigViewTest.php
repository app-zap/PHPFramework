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
    $this->reponse->add_output_filter('foo', function(){});
    $this->assertTrue($this->reponse->has_output_filter('foo'));
    $this->assertFalse($this->reponse->has_output_filter('bar'));
  }

  /**
   * @test
   */
  public function addOutputFunction() {
    $this->reponse->add_output_function('foo', function(){});
    $this->assertTrue($this->reponse->has_output_function('foo'));
    $this->assertFalse($this->reponse->has_output_function('bar'));
  }

}