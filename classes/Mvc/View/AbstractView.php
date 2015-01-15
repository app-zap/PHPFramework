<?php
namespace AppZap\PHPFramework\Mvc\View;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mvc\HttpStatus;

abstract class AbstractView {

  /**
   * @var \Twig_Environment
   */
  protected $renderingEngine;

  /**
   * @var string
   */
  protected $templateName;

  /**
   * @var array
   */
  protected $templateVars = [];

  /**
   * @var array
   */
  protected $headers = [];

  /**
   * @var array
   */
  protected $outputFilters = [];

  /**
   * @var array
   */
  protected $outputFunctions = [];

  /**
   * @var string
   */
  protected $defaultTemplateFileExtension = 'html';

  /**
   * @return \Twig_Environment
   */
  abstract protected function getRenderingEngine();

  /**
   * @param string $templateName
   */
  public function setTemplateName($templateName) {
    $this->templateName = $templateName;
  }

  /**
   * Sets a header to the specified value for delivery when the page is rendered
   *
   * @param string $headerName Name of the header not including the colon
   * @param string $headerValue Values of the header to send
   */
  public function header($headerName, $headerValue) {
    $this->headers[$headerName] = $headerValue;
  }

  /**
   * Returns the value of a template value previously set
   *
   * @param string $templateVariableName Name of the template variable
   * @param mixed $defaultValue Value to be returned when the template variable was not set previously
   * @return mixed
   */
  public function get($templateVariableName, $defaultValue = NULL) {
    if(array_key_exists($templateVariableName, $this->templateVars)) {
      return $this->templateVars[$templateVariableName];
    }
    return $defaultValue;
  }

  /**
   * Sets a template value for later use in twig template while rendering
   *
   * @param string $template_variable_name Name of the template variable
   * @param mixed $template_variable_value Value of the template variable to set to
   */
  public function set($template_variable_name, $template_variable_value) {
    $this->templateVars[$template_variable_name] = $template_variable_value;
  }

  /**
   * Renders the template with the previously defined variables and returns the rendered version
   *
   * @param string $templateName Name of the template in the template directory without extension
   * @return string
   */
  public function render($templateName = NULL) {
    $this->sendHeaders();
    $template = $this->getTemplateEnvironment($templateName);
    return $template->render($this->templateVars);
  }

  /**
   * Sends the headers if not already done and puts the content
   * to output stream
   *
   * @param string $content Content to send to browser
   */
  public function write($content) {
    if(!headers_sent()) {
      $this->sendHeaders();
    }

    echo $content;
  }

  /**
   * Sends an json encoded object to the browser using correct content type
   *
   * @param mixed $object Object (most likely an array) to json encode
   * @param string $callback If set to string answer will be sent as JSONP output with this function
   */
  public function jsonOutput($object, $callback = NULL) {
    if($callback !== NULL) {
      $ctype = 'text/javascript';
      $output = $callback . '(' . json_encode($object) . ');';
    } else {
      $ctype = 'application/json';
      $output = json_encode($object);
    }
    $this->header('Content-Type', $ctype);
    $this->sendHeaders();

    echo $output;
  }

  /**
   * Sets the location header including the HTTP status header for redirects
   *
   * @param string $target The target to use in location header
   * @param int $httpCode The HTTP code to use
   * @see \AppZap\PHPFramework\Mvc\HttpStatus
   */
  public function redirect($target, $httpCode = HttpStatus::STATUS_307_TEMPORARY_REDIRECT) {
    if (php_sapi_name() !== 'cli') {
      HttpStatus::setStatus($httpCode, [
          HttpStatus::HEADER_FIELD_LOCATION => $target
      ]);
      HttpStatus::sendHeaders();
    }
  }

  /**
   *
   */
  protected function sendHeaders() {
    foreach($this->headers as $header => $value) {
      header($header . ': ' . $value);
    }
  }

  /**
   * @param string $template_name
   * @return \Twig_TemplateInterface
   */
  protected function getTemplateEnvironment($template_name = NULL) {
    if (is_null($template_name)) {
      $template_name = $this->templateName;
    }
    $template_file_extension = Configuration::get('phpframework', 'template_file_extension', $this->defaultTemplateFileExtension);
    $template = $this->getRenderingEngine()->loadTemplate($template_name . '.' . $template_file_extension);

    return $template;
  }

}
