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
    if (!isset($this->renderingEngine)) {
      if (!is_dir(Configuration::get('application', 'templates_directory'))) {
        throw new ApplicationPartMissingException('Template directory "' . Configuration::get('application', 'templates_directory') . '" does not exist.');
      }
      $loader = new \Twig_Loader_Filesystem(Configuration::get('application', 'templates_directory'));
      $options = [];
      if (Configuration::get('phpframework', 'cache.enable')) {
        $options['cache'] = Configuration::get('phpframework', 'cache.twig_folder', './cache/twig/');
      }
      $this->renderingEngine = new \Twig_Environment($loader, $options);
    }
    return $this->renderingEngine;
  }

  /**
   * @param string $name Name of the filter to use in the template
   * @param mixed $function Name of the function to execute for the value from the template
   * @param bool $htmlEscape
   */
  public function addOutputFilter($name, $function, $htmlEscape = FALSE) {
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
  public function hasOutputFilter($name) {
    return $this->getRenderingEngine()->getFilter($name) instanceof \Twig_SimpleFilter;
  }

  /**
   * Adds a function to use in the template
   *
   * @param string $name string Name of the function to use in the template
   * @param mixed $function string Name of the function to execute for the value from the template
   * @param bool $htmlEscape
   */
  public function addOutputFunction($name, $function, $htmlEscape = FALSE) {
    $options = [];
    if (!$htmlEscape) {
      $options = ['is_safe' => ['all']];
    }
    $this->getRenderingEngine()->addFunction(new \Twig_SimpleFunction($name, $function, $options));
  }

  /**
   * @param string $name
   * @return bool
   */
  public function hasOutputFunction($name) {
    return $this->getRenderingEngine()->getFunction($name) instanceof \Twig_SimpleFunction;
  }

  /**
   * @param string $name Name of the filter to use in the template
   * @param mixed $function Name of the function to execute for the value from the template
   * @param bool $htmlEscape
   * @deprecated Since: 1.4, Removal: 1.5, Reason: Use ->addOutputFilter() instead
   */
  public function add_output_filter($name, $function, $htmlEscape = FALSE) {
    $this->addOutputFilter($name, $function, $htmlEscape);
  }

  /**
   * @param string $name
   * @return bool
   * @deprecated Since: 1.4, Removal: 1.5, Reason: Use ->hasOutputFilter() instead
   */
  public function has_output_filter($name) {
    return $this->hasOutputFilter($name);
  }

  /**
   * Adds a function to use in the template
   *
   * @param string $name string Name of the function to use in the template
   * @param mixed $function string Name of the function to execute for the value from the template
   * @param bool $htmlEscape
   * @deprecated Since: 1.4, Removal: 1.5, Reason: Use ->addOutputFunction() instead
   */
  public function add_output_function($name, $function, $htmlEscape = FALSE) {
    $this->addOutputFunction($name, $function, $htmlEscape);
  }

  /**
   * @param string $name
   * @return bool
   * @deprecated Since: 1.4, Removal: 1.5, Reason: Use ->hasOutputFunction() instead
   */
  public function has_output_function($name) {
    return $this->hasOutputFunction($name);
  }

}
