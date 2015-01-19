<?php
namespace AppZap\PHPFramework\Tests\Unit\Mail;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mail\TransportFactory;

class TransportFactoryTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var TransportFactory
   */
  protected $transportFactory;

  public function setUp() {
    Configuration::reset();
    $this->transportFactory = new TransportFactory();
  }

  /**
   * @expectedException \AppZap\PHPFramework\Mail\TransportFactoryException
   * @expectedExceptionCode 1421678005
   */
  public function unsupportedTransportMethod() {
    Configuration::set('phpframework', 'mail.method', 'not_supported');
    $this->transportFactory->createTransport();
  }

  /**
   * @test
   */
  public function createMailTransport() {
    $transport = $this->transportFactory->createTransport();
    $this->assertSame('Swift_MailTransport', get_class($transport));
    Configuration::set('phpframework', 'mail.method', 'mail');
    $transport = $this->transportFactory->createTransport();
    $this->assertSame('Swift_MailTransport', get_class($transport));
  }

  /**
   * @test
   */
  public function createSmtpTransport() {
    Configuration::set('phpframework', 'mail.method', 'smtp');
    /** @var \Swift_SmtpTransport $transport */
    $transport = $this->transportFactory->createTransport();
    $this->assertSame('Swift_SmtpTransport', get_class($transport));
    $this->assertSame(465, $transport->getPort());
    Configuration::set('phpframework', 'mail.smtp_encryption', 'ssl');
    $transport = $this->transportFactory->createTransport();
    $this->assertSame(465, $transport->getPort());
    Configuration::set('phpframework', 'mail.smtp_port', '123');
    $transport = $this->transportFactory->createTransport();
    $this->assertSame(123, $transport->getPort());
    Configuration::set('phpframework', 'mail.smtp_encryption', 'none');
    $transport = $this->transportFactory->createTransport();
    $this->assertSame(123, $transport->getPort());
  }

  /**
   * @test
   */
  public function defaultSmtpEncryption() {
    Configuration::set('phpframework', 'mail.method', 'smtp');
    /** @var \Swift_SmtpTransport $transport */
    $transport = $this->transportFactory->createTransport();
    $this->assertSame('ssl', $transport->getEncryption());
  }

  /**
   * @test
   */
  public function sslSmtpEncryption() {
    Configuration::set('phpframework', 'mail.method', 'smtp');
    Configuration::set('phpframework', 'mail.smtp_encryption', 'ssl');
    /** @var \Swift_SmtpTransport $transport */
    $transport = $this->transportFactory->createTransport();
    $this->assertSame('ssl', $transport->getEncryption());
  }

  /**
   * @test
   */
  public function noSmtpEncryption() {
    Configuration::set('phpframework', 'mail.method', 'smtp');
    Configuration::set('phpframework', 'mail.smtp_encryption', 'none');
    /** @var \Swift_SmtpTransport $transport */
    $transport = $this->transportFactory->createTransport();
    $this->assertNull($transport->getEncryption());
  }

  /**
   * @test
   */
  public function defaultSmtpPort() {
    Configuration::set('phpframework', 'mail.method', 'smtp');
    /** @var \Swift_SmtpTransport $transport */
    $transport = $this->transportFactory->createTransport();
    $this->assertSame(465, $transport->getPort());
    Configuration::set('phpframework', 'mail.smtp_encryption', 'none');
    $transport = $this->transportFactory->createTransport();
    $this->assertSame(587, $transport->getPort());
  }

  /**
   * @test
   */
  public function customSmtpPort() {
    Configuration::set('phpframework', 'mail.method', 'smtp');
    Configuration::set('phpframework', 'mail.smtp_port', '123');
    /** @var \Swift_SmtpTransport $transport */
    $transport = $this->transportFactory->createTransport();
    $this->assertSame(123, $transport->getPort());
    Configuration::set('phpframework', 'mail.smtp_encryption', 'none');
    $transport = $this->transportFactory->createTransport();
    $this->assertSame(123, $transport->getPort());
  }

}
