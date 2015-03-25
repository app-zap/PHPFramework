<?php
namespace AppZap\PHPFramework\Tests\Unit;

use AppZap\PHPFramework\Domain\Repository\AbstractDomainRepository;

class TestRepository1 extends AbstractDomainRepository{}
class TestRepository2 extends AbstractDomainRepository{}

class SingletonTest extends \PHPUnit_Framework_TestCase {

  /**
   * @test
   */
  public function getTheSameInstanceEveryTime() {
    $repo1Instance1 = TestRepository1::getInstance();
    $repo1Instance2 = TestRepository1::getInstance();
    $this->assertSame($repo1Instance1, $repo1Instance2);
  }

  /**
   * @test
   */
  public function getRightClass() {
    $repo1Instance1 = TestRepository1::getInstance();
    $repo2Instance1 = TestRepository2::getInstance();
    $this->assertTrue($repo1Instance1 instanceof TestRepository1);
    $this->assertTrue($repo2Instance1 instanceof TestRepository2);
  }

}
