<?php
namespace AppZap\PHPFramework\Mvc\View;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mvc\ApplicationPartMissingException;

class TwigView extends AbstractView {

  /**
   * @var string
   */
  protected $defaultTemplateFileExtension = 'twig';

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
  protected function getRenderingEngine() {
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
    $this->getRenderingEngine()->addFilter(new \Twig_SimpleFilter($name, $function, $options));
  }

  /**
   * @param $name
   * @return bool
   */
  public function has_output_filter($name) {
    return $this->getRenderingEngine()->getFilter($name) instanceof \Twig_SimpleFilter;
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
    $this->getRenderingEngine()->addFunction(new \Twig_SimpleFunction($name, $function, $options));
  }

  /**
   * @param $name
   * @return bool
   */
  public function has_output_function($name) {
    return $this->getRenderingEngine()->getFunction($name) instanceof \Twig_SimpleFunction;
  }

}
