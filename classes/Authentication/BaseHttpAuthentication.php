<?php
namespace AppZap\PHPFramework\Authentication;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mvc\HttpStatus;

class BaseHttpAuthentication {

  /**
   * @var string
   */
  protected $name;

  /**
   * @var string
   */
  protected $password;

  public function __construct() {
    if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
      $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
      $this->name = $_SERVER['PHP_AUTH_USER'];
      $this->password = $_SERVER['PHP_AUTH_PW'];
    } elseif (isset($_ENV['HTTP_AUTHORIZATION'])) {
      if (preg_match('/^Basic\s+(.+)/i', $_ENV['HTTP_AUTHORIZATION'], $matches)) {
        $values = explode(':', base64_decode($matches[1]), 2);
        $this->name = $values[0];
        $this->password = $values[1];
      }
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
      list($this->name, $this->password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    }
  }

  /**
   * @throws \Exception
   */
  public function checkAuthentication() {
    if (!$this->isAuthenticated()) {
      HttpStatus::setStatus(HttpStatus::STATUS_401_UNAUTHORIZED);
      header('WWW-Authenticate: Basic realm="Login"');
      throw new HttpAuthenticationRequiredException('HTTP authentication was required but not fulfilled.', 1415266170);
    }
  }

  /**
   * @return bool
   */
  protected function isAuthenticated() {
    if ($this->name === NULL || $this->password === NULL) {
      return FALSE;
    }
    $httpAuthentication = Configuration::getSection('phpframework', 'authentication.http');
    if (!array_key_exists($this->name, $httpAuthentication)) {
      return FALSE;
    }
    return sha1($this->password) === $httpAuthentication[$this->name];
  }

}
