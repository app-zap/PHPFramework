<?php
namespace AppZap\PHPFramework\Mvc;

class HttpStatus {

  const HEADER_FIELD_ALLOW = 'Allow';
  const HEADER_FIELD_LOCATION = 'Location';

  const STATUS_200_OK = 200;
  const STATUS_201_CREATED = 201;
  const STATUS_202_ACCEPTED = 202;

  const STATUS_301_MOVED_PERMANENTLY = 301;
  const STATUS_304_NOT_MODIFIED = 304;
  const STATUS_307_TEMPORARY_REDIRECT = 307;

  const STATUS_400_BAD_REQUEST = 400;
  const STATUS_401_UNAUTHORIZED = 401;
  const STATUS_403_FORBIDDEN = 403;
  const STATUS_404_NOT_FOUND = 404;
  const STATUS_405_METHOD_NOT_ALLOWED = 405;
  const STATUS_409_CONFLICT = 409;
  const STATUS_410_GONE = 410;
  const STATUS_429_TOO_MANY_REQUESTS = 429;

  const STATUS_500_INTERNAL_SERVER_ERROR = 500;
  const STATUS_501_NOT_IMPLEMENTED = 501;

  /**
   * @var array
   */
  protected static $additionalHeaders = [];

  /**
   * @param int $code
   * @param string $options
   * @throws \Exception
   */
  public static function setStatus($code, $options = NULL) {
    $code = (int) $code;
    // Status codes with optional "Location"
    if (in_array($code, [self::STATUS_201_CREATED])) {
      if (is_array($options) && array_key_exists(self::HEADER_FIELD_LOCATION, $options)) {
        self::$additionalHeaders[self::HEADER_FIELD_LOCATION] = $options[self::HEADER_FIELD_LOCATION];
      }
    }
    // Status codes with required "Location"
    if (in_array($code, [self::STATUS_301_MOVED_PERMANENTLY, self::STATUS_307_TEMPORARY_REDIRECT])) {
      if (
          !(
              array_key_exists(self::HEADER_FIELD_LOCATION, self::$additionalHeaders) ||
              (
                  is_array($options) && array_key_exists(self::HEADER_FIELD_LOCATION, $options)
              )
          )
      ) {
        throw new \Exception('Tried to set HTTP status code ' . $code . ' without required location field');
      }
      if (array_key_exists(self::HEADER_FIELD_LOCATION, $options)) {
        self::$additionalHeaders[self::HEADER_FIELD_LOCATION] = $options[self::HEADER_FIELD_LOCATION];
      }
    }
    // Status codes with required "Allow"
    if (in_array($code, [self::STATUS_405_METHOD_NOT_ALLOWED])) {
      if (!(array_key_exists(self::HEADER_FIELD_ALLOW, self::$additionalHeaders) || array_key_exists(self::HEADER_FIELD_ALLOW, $options))) {
        throw new \Exception('Tried to set HTTP status code ' . $code . ' without required allow field');
      }
      if (array_key_exists(self::HEADER_FIELD_ALLOW, $options)) {
        self::$additionalHeaders[self::HEADER_FIELD_ALLOW] = $options[self::HEADER_FIELD_ALLOW];
      }
    }
    http_response_code($code);
  }

  /**
   * @return int
   */
  public static function getStatus() {
    return http_response_code();
  }

  /**
   *
   */
  public static function sendHeaders() {
    foreach (self::$additionalHeaders as $field => $value) {
      header($field . ':' . $value);
    }
    self::$additionalHeaders = [];
  }

  /**
   * @param int $code
   * @param string $options
   * @throws \Exception
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->setStatus() instead
   */
  public static function set_status($code, $options = NULL) {
    self::setStatus($code, $options);
  }

  /**
   * @return int
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->getStatus() instead
   */
  public static function get_status() {
    return self::getStatus();
  }

  /**
   * @deprecated Since: 1.4, Removal: 1.5, Reason: use ->sendHeaders() instead
   */
  public static function send_headers() {
    self::sendHeaders();
  }

}
