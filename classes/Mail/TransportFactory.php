<?php
namespace AppZap\PHPFramework\Mail;

use AppZap\PHPFramework\Configuration\Configuration;

class TransportFactory {

  /**
   * @return \Swift_Transport
   * @throws TransportFactoryException
   */
  public function createTransport() {
    $method = Configuration::get('phpframework', 'mail.method', 'mail');
    switch ($method) {
      case 'mail':
        return $this->createMailTransport();
      case 'smtp':
        return $this->createSmtpTransport();
      default:
        throw new TransportFactoryException('Mail transport method ' . $method . ' not supported.', 1421678005);
    }
  }

  /**
   * @return \Swift_MailTransport
   */
  protected function createMailTransport() {
    return \Swift_MailTransport::newInstance();
  }

  /**
   * @return \Swift_SmtpTransport
   */
  protected function createSmtpTransport() {
    $mailConfiguration = Configuration::getSection(
      'phpframework',
      'mail',
      [
        'smtp_encryption' => 'ssl',
        'smtp_host' => 'localhost',
        'smtp_port' => FALSE,
      ]
    );
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
    if (isset($mailConfiguration['smtp_user']) && $mailConfiguration['smtp_user']) {
      $transport->setUsername($mailConfiguration['smtp_user']);
    }
    if (isset($mailConfiguration['smtp_password']) && $mailConfiguration['smtp_password']) {
      $transport->setPassword($mailConfiguration['smtp_password']);
    }
    return $transport;
  }

}