<?php
namespace AppZap\PHPFramework\Mvc;

class Request {

  /**
   * @var array
   */
  protected $valueSources = [
    'get' => ['default' => 'get', 'fallback' => 'post'],
    'post' => ['default' => 'post', 'fallback' => 'get'],
    'head' => ['default' => 'get', 'fallback' => 'post'],
    'cli' => ['default' => 'body'],
    'put' => ['default' => 'body'],
    'delete' => ['default' => 'body'],
    'patch' => ['default' => 'body'],
    'options' => ['default' => 'body'],
  ];

  /**
   * @var string
   */
  protected $requestMethod;

  public function __construct($requestMethod) {
    $this->requestMethod = $requestMethod;
    // Early exit when not defined where to read request values
    if(!array_key_exists($this->requestMethod, $this->valueSources)) {
      throw new MethodNotSupportedException('Getting request parameters of ' . $this->requestMethod . ' is not supported.', 1415273543);
    }
  }

  /**
   * Returns a parameter value from the request sent by the client
   *
   * @param string $parameterName Name of the request parameter to deliver
   * @param mixed $defaultValue Default value to deliver when parameter is not found
   * @param boolean $useStrictMode Whether to allow searching in other request types than the used one
   * @return mixed
   * @throws MethodNotSupportedException when the parameter is read for a request type which is not supported by the framework
   */
  public function get($parameterName, $defaultValue = NULL, $useStrictMode = FALSE) {

    try {
      return $this->readRequestParameter($parameterName, $this->valueSources[$this->requestMethod]['default']);
    } catch(ParameterNotFoundException $ex) {}

    if(!$useStrictMode && isset($this->valueSources[$this->requestMethod]['fallback'])) {
      try {
        return $this->readRequestParameter($parameterName, $this->valueSources[$this->requestMethod]['fallback']);
      } catch(ParameterNotFoundException $ex) {}
    }

    return $defaultValue;
  }

  /**
   * Reads the plain request body as a string
   *
   * @return string
   * @throws ParameterNotFoundException if no body could be read
   */
  public function body() {
    $body = '';
    $fh   = @fopen('php://input', 'r');
    if ($fh) {
      while (!feof($fh)) {
        $s = fread($fh, 1024);
        if (is_string($s)) {
          $body .= $s;
        }
      }
      fclose($fh);
    } else {
      throw new ParameterNotFoundException('No body could be read.');
    }

    return $body;
  }

  /**
   * Returns the value of an parameter for a specified source if the source is supported and the parameter exists
   *
   * @param string $parameterName Name of the request parameter to deliver
   * @param string $valueSource Name of the source to use when reading the parameter
   * @return mixed
   * @throws ValueSourceNotSupportedException when the name of the source is unsupported
   * @throws ParameterNotFoundException when the parameter is not found in the source to avoid collision with other value types
   */
  protected function readRequestParameter($parameterName, $valueSource) {

    if($valueSource == 'get') {
      $source = &$_GET;
    } elseif($valueSource == 'post') {
      $source = &$_POST;
    }

    if(!isset($source)) {
      throw new ValueSourceNotSupportedException('The source ' . $valueSource . ' is not supported to read values from.', 1415273682);
    }

    if(array_key_exists($parameterName, $source)) {
      return $source[$parameterName];
    }

    throw new ParameterNotFoundException('Parameter ' . $parameterName . ' was not found in source ' . $valueSource, 1415273689);
  }

}
