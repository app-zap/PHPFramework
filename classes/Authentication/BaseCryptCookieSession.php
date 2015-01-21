<?php
namespace AppZap\PHPFramework\Authentication;

use AppZap\PHPFramework\Configuration\Configuration;

class BaseCryptCookieSession implements BaseSessionInterface {

  /**
   * @var array
   */
  protected $store = [];

  /**
   * @var string
   */
  protected $cookieName;

  /**
   * @var \Callable
   */
  protected $setCookieFunction;

  /**
   * @throws BaseCryptCookieSessionException
   */
  public function __construct() {
    $this->cookieName = Configuration::get('phpframework', 'authentication.cookie.name', 'SecureSessionCookie');;

    $encryptionKey = Configuration::get('phpframework', 'authentication.cookie.encrypt_key', NULL);
    if ($encryptionKey === NULL) {
      throw new BaseCryptCookieSessionException('Config key "authentication.cookie.encrypt_key" must be set!', 1415264244);
    }
    if (!in_array(strlen($encryptionKey), [16, 24, 32])) {
      throw new BaseCryptCookieSessionException('Encryption key must be of size 16, 24 or 32 ', 1421849111);
    }

    $this->decodeCryptCookie();
  }

  /**
   * @param string $key
   * @param mixed $default Default value to return when key is not found
   * @return mixed
   */
  public function get($key, $default = NULL) {
    return array_key_exists($key, $this->store) ? $this->store[$key] : $default;
  }

  /**
   * @param string $key
   * @param mixed $value
   * @return BaseSessionInterface
   */
  public function set($key, $value) {
    $this->store[$key] = $value;
    $this->encodeCryptCookie();
  }

  /**
   *
   */
  protected function decodeCryptCookie() {
    $sSecretKey = Configuration::get('phpframework', 'authentication.cookie.encrypt_key');
    if(!array_key_exists($this->cookieName, $_COOKIE)) {
      return;
    }
    $sEncrypted = $_COOKIE[$this->cookieName];
    $data = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, base64_decode($sEncrypted), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    $this->store = json_decode($data, TRUE);
  }

  /**
   *
   */
  protected function encodeCryptCookie() {
    $key = Configuration::get('phpframework', 'authentication.cookie.encrypt_key');
    $plaintext = json_encode($this->store);
    $cyphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $plaintext, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND));
    $data = trim(base64_encode($cyphertext));
    if ($this->setCookieFunction === NULL) {
      setcookie($this->cookieName, $data, time() + 31 * 86400, '/');
    } else {
      call_user_func($this->setCookieFunction, $this->cookieName, $data, time() + 31 * 86400, '/');
    }
  }

  /**
   * @param string $key
   * @return boolean
   */
  public function exist($key) {
    return array_key_exists($key, $this->store);
  }

  /**
   * @param string $key
   * @return BaseSessionInterface
   * @throws BaseSessionUndefinedIndexException
   */
  public function clear($key) {
    unset($this->store[$key]);
    $this->encodeCryptCookie();
  }

  /**
   * @return BaseSessionInterface
   */
  public function clearAll() {
    $this->store = [];
    $this->encodeCryptCookie();
  }

  /**
   * @param \Callable $setCookieFunction
   */
  public function injectSetCookieFunction($setCookieFunction) {
    $this->setCookieFunction = $setCookieFunction;
  }

}
