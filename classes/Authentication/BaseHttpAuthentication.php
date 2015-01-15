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
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
      $this->name = $_SERVER['PHP_AUTH_USER'];
      $this->password = $_SERVER['PHP_AUTH_PW'];
    } elseif (isset($_ENV['HTTP_AUTHORIZATION'])) {
      if (preg_match('/^Basic\s+(.+)/i', $_ENV['HTTP_AUTHORIZATION'], $matches)) {
        $vals = explode(':', base64_decode($matches[1]), 2);
        $this->name = $vals[0];
        $this->password = $vals[1];
      }
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
      list($this->name, $this->password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    }
  }

  /**
   * @throws \Exception
   */
  public function checkAuthentication() {
    $httpAuthentication = Configuration::getSection('phpframework', 'authentication.http');
    if (is_array($httpAuthentication) && !$this->isAuthenticated()) {
      HttpStatus::setStatus(HttpStatus::STATUS_401_UNAUTHORIZED);
      header('WWW-Authenticate: Basic realm="Login"');
      throw new HttpAuthenticationRequiredException('HTTP authentication was required but not fulfilled.', 1415266170);
    }
  }

  /**
   * @return bool
   */
  protected function isAuthenticated() {
    $httpAuthentication = Configuration::getSection('phpframework', 'authentication.http');
    return
        $this->name !== NULL &&
        $this->password !== NULL &&
        array_key_exists($this->name, $httpAuthentication) &&
        sha1($this->password) === $httpAuthentication[$this->name];
  }

}
