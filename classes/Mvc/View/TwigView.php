<?php
namespace AppZap\PHPFramework\Mvc\View;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mvc\ApplicationPartMissingException;

class TwigView extends AbstractView {

  /**
   * @var string
   */
  protected $default_template_file_extension = 'twig';

  /**
   * @throws ApplicationPartMissingException
   */
  public function __construct() {
    \Twig_Autoloader::register();
  }

  /**
   * @return \Twig_Environment
   * @throws ApplicationPartMissingException
   */
  protected function get_rendering_engine() {
    if (!isset($this->rendering_engine)) {
      if (!is_dir(Configuration::get('application', 'templates_directory'))) {
        throw new ApplicationPartMissingException('Template directory "' . Configuration::get('application', 'templates_directory') . '" does not exist.');
      }
      $loader = new \Twig_Loader_Filesystem(Configuration::get('application', 'templates_directory'));
      $options = [];
      if (Configuration::get('phpframework', 'cache.enable')) {
        $options['cache'] = Configuration::get('phpframework', 'cache.twig_folder', './cache/twig/');
      }
      $this->rendering_engine = new \Twig_Environment($loader, $options);
    }
    return $this->rendering_engine;
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
    $this->get_rendering_engine()->addFilter(new \Twig_SimpleFilter($name, $function, $options));
  }

  /**
   * @param $name
   * @return bool
   */
  public function has_output_filter($name) {
    return $this->get_rendering_engine()->getFilter($name) instanceof \Twig_SimpleFilter;
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
    $this->get_rendering_engine()->addFunction(new \Twig_SimpleFunction($name, $function, $options));
  }

  /**
   * @param $name
   * @return bool
   */
  public function has_output_function($name) {
    return $this->get_rendering_engine()->getFunction($name) instanceof \Twig_SimpleFunction;
  }

}
