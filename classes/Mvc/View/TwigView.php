<?php
namespace AppZap\PHPFramework\Mvc\View;

use AppZap\PHPFramework\Configuration\Configuration;

class TwigView extends AbstractView {

  /**
   * @var string
   */
  protected $default_template_file_extension = 'twig';

  /**
   *
   */
  public function __construct() {
    \Twig_Autoloader::register();
    $loader = new \Twig_Loader_Filesystem(Configuration::get('application', 'templates_directory'));
    $options = [];
    if (Configuration::get('phpframework', 'cache.enable')) {
      $options['cache'] = Configuration::get('phpframework', 'cache.twig_folder', './cache/twig/');
    }
    $this->rendering_engine = new \Twig_Environment($loader, $options);
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

}