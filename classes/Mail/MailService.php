<?php
namespace AppZap\PHPFramework\Mail;

use AppZap\PHPFramework\Singleton;

class MailService {
  use Singleton;

  /**
   * @var TransportFactory
   */
  protected $transportFactory;

  public function __construct() {
    $this->transportFactory = new TransportFactory();
  }

  /**
   * @param MailMessage $message
   */
  public function send(MailMessage $message) {
    $transport = $this->transportFactory->createTransport();
    $mailer = new \Swift_Mailer($transport);
    $mailer->send($message);
  }

}
