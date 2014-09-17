<?php
namespace AppZap\PHPFramework\Mvc;

use AppZap\PHPFramework\Configuration\Configuration;

class BaseHttpResponse {

  /**
   * @var \Twig_Environment
   */
  protected $rendering_engine;

  /**
   * @var string
   */
  protected $template_name;

  protected $template_vars = [];
  protected $headers = [];
  protected $output_filters = [];
  protected $output_functions = [];

  public function __construct() {
    \Twig_Autoloader::register();
    $loader = new \Twig_Loader_Filesystem(Configuration::get('application', 'templates_directory'));
    $options = [];
    if (Configuration::get('cache', 'enable')) {
      $options['cache'] = Configuration::get('cache', 'twig_cache_folder', './cache/twig/');
    }
    $this->rendering_engine = new \Twig_Environment($loader, $options);
  }

  /**
   * @param $template_name
   */
  public function set_template_name($template_name) {
    $this->template_name = $template_name;
  }

  /**
   * Sets a header to the specified value for delivery when the page is rendered
   *
   * @param string $header_name Name of the header not including the colon
   * @param string $header_value Values of the header to send
   */
  public function header($header_name, $header_value) {
    $this->headers[$header_name] = $header_value;
  }

  /**
   * Returns the value of a template value previously set
   *
   * @param string $template_variable_name Name of the template variable
   * @param mixed $default_value Value to be returned when the template variable was not set previously
   * @return mixed
   */
  public function get($template_variable_name, $default_value = null) {
    if(array_key_exists($template_variable_name, $this->template_vars)) {
      return $this->template_vars[$template_variable_name];
    }
    return $default_value;
  }

  /**
   * Sets a template value for later use in twig template while rendering
   *
   * @param string $template_variable_name Name of the template variable
   * @param mixed $template_variable_value Value of the template variable to set to
   */
  public function set($template_variable_name, $template_variable_value) {
    $this->template_vars[$template_variable_name] = $template_variable_value;
  }

  /**
   * Renders the template with the previously defined variables and returns the rendered version
   *
   * @param string $template_name Name of the template in the template directory without extension
   * @return string
   */
  public function render($template_name = NULL) {
    $this->send_headers();
    $template = $this->get_template_environment($template_name);
    return $template->render($this->template_vars);
  }

  /**
   * Sends the headers if not already done and puts the content
   * to output stream
   *
   * @param string $content Content to send to browser
   */
  public function write($content) {
    if(!headers_sent()) {
      $this->send_headers();
    }

    echo $content;
  }

  /**
   * Sends an json encoded object to the browser using correct content type
   *
   * @param mixed $object Object (most likely an array) to json encode
   * @param null|string $callback If set to string answer will be sent as JSONP output with this function
   */
  public function json_output($object, $callback = null) {
    if($callback !== null) {
      $ctype = 'text/javascript';
      $output = $callback . '(' . json_encode($object) . ');';
    } else {
      $ctype = 'application/json';
      $output = json_encode($object);
    }
    $this->header('Content-Type', $ctype);
    $this->send_headers();

    die($output);
  }

  /**
   * Sets the location header including the HTTP status header for redirects
   *
   * @param string $target The target to use in location header
   * @param int $http_code The HTTP code to use
   * @see \AppZap\PHPFramework\Mvc\HttpStatus
   */
  public function redirect($target, $http_code = HttpStatus::STATUS_307_TEMPORARY_REDIRECT) {
    HttpStatus::set_status($http_code, [
      HttpStatus::HEADER_FIELD_LOCATION => $target
    ]);
    HttpStatus::send_headers();
  }

  /**
   * Adds a filter to use in the template
   *
   * @param $name string Name of the filter to use in the template
   * @param $function string Name of the function to execute for the value from the template
   */
  public function add_output_filter($name, $function, $htmlEscape = FALSE) {
    $options = [];
    if (!$htmlEscape) {
      $options = ['is_safe' => ['all']];
    }
    $this->rendering_engine->addFilter(new \Twig_SimpleFilter($name, $function, $options));

  }

  /**
   * @param $name
   * @return bool
   */
  public function has_output_filter($name) {
    return $this->rendering_engine->getFilter($name) instanceof \Twig_SimpleFilter;
  }

  /**
   * Adds a function to use in the template
   *
   * @param $name string Name of the function to use in the template
   * @param $function string Name of the function to execute for the value from the template
   */
  public function add_output_function($name, $function, $htmlEscape = FALSE) {
    $options = [];
    if (!$htmlEscape) {
      $options = ['is_safe' => ['all']];
    }
    $this->rendering_engine->addFunction(new \Twig_SimpleFunction($name, $function, $options));
  }

  /**
   * @param $name
   * @return bool
   */
  public function has_output_function($name) {
    return $this->rendering_engine->getFunction($name) instanceof \Twig_SimpleFunction;
  }

  protected function send_headers() {
    foreach($this->headers as $header => $value) {
      header($header . ': ' . $value);
    }
  }

  /**
   * @param string $template_name
   * @return \Twig_TemplateInterface
   */
  protected function get_template_environment($template_name = NULL) {
    if (is_null($template_name)) {
      $template_name = $this->template_name;
    }
    $template_file_extension = Configuration::get('phpframework', 'template_file_extension') ?: 'twig';
    $template = $this->rendering_engine->loadTemplate($template_name . '.' . $template_file_extension);

    return $template;
  }

}
