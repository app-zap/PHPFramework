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
   * @throws BaseCryptCookieSessionException
   */
  public function __construct() {
    $this->cookieName = Configuration::get('phpframework', 'authentication.cookie.name', 'SecureSessionCookie');;

    if(!Configuration::get('phpframework', 'authentication.cookie.encrypt_key')) {
      throw new BaseCryptCookieSessionException('Config key "authentication.cookie.encrypt_key" must be set!', 1415264244);
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
    $this->store = json_decode($data, true);
  }

  /**
   *
   */
  protected function encodeCryptCookie() {
    $sSecretKey = Configuration::get('phpframework', 'authentication.cookie.encrypt_key');
    $sDecrypted = json_encode($this->store);
    $data = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, $sDecrypted, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    setcookie($this->cookieName, $data, time() + 31 * 86400, '/');
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
   * @return BaseSessionInterface
   * @deprecated Since 1.4, Removal: 1.5, Reason: Use ->clearAll() instead
   */
  public function clear_all() {
    $this->clearAll();
  }

}
