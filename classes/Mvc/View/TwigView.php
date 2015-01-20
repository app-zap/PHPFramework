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
    if ($this->renderingEngine instanceof \Twig_Environment) {
      return $this->renderingEngine;
    }
    if (!is_dir(Configuration::get('application', 'templates_directory'))) {
      throw new ApplicationPartMissingException('Template directory "' . Configuration::get('application', 'templates_directory') . '" does not exist.');
    }
    $loader = new \Twig_Loader_Filesystem($this->templatesDirectory);
    $options = [];
    if (Configuration::get('phpframework', 'cache.enable')) {
      $options['cache'] = Configuration::get('phpframework', 'cache.twig_folder', './cache/twig/');
    }
    $this->renderingEngine = new \Twig_Environment($loader, $options);
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

}
