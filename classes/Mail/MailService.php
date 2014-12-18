<?php
namespace AppZap\PHPFramework\Mail;

use AppZap\PHPFramework\Configuration\Configuration;

class MailService {

  /**
   * @param MailMessage $message
   */
  public function send(MailMessage $message) {
    $transport = $this->createTransport();
    $mailer = new \Swift_Mailer($transport);
    $mailer->send($message);
  }

  /**
   * @return \Swift_Transport
   */
  protected function createTransport() {
    $mailConfiguration = Configuration::getSection(
        'phpframework',
        'mail',
        [
          'smtp_encryption' => 'ssl',
          'smtp_host' => 'localhost',
          'smtp_password' => '',
          'smtp_port' => FALSE,
          'smtp_user' => FALSE,
        ]
    );
    if (!$mailConfiguration['smtp_user']) {
      return \Swift_MailTransport::newInstance();
    }
    if ($mailConfiguration['smtp_encryption'] === 'none') {
      $mailConfiguration['smtp_encryption'] = NULL;
    }
    if (!$mailConfiguration['smtp_port']) {
      if ($mailConfiguration['smtp_encryption'] === 'ssl') {
        $mailConfiguration['smtp_port'] = '465';
      } else {
        $mailConfiguration['smtp_port'] = '587';
      }
    }
    $transport = \Swift_SmtpTransport::newInstance(
        $mailConfiguration['smtp_host'],
        $mailConfiguration['smtp_port'],
        $mailConfiguration['smtp_encryption']
    );
    $transport->setUsername($mailConfiguration['smtp_user']);
    $transport->setPassword($mailConfiguration['smtp_password']);
    return $transport;
  }

}
