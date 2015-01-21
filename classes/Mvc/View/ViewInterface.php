<?php
namespace AppZap\PHPFramework\Mvc\View;

interface ViewInterface {

  /**
   * @param string $templateName
   * @return void
   */
  public function setTemplateName($templateName);

  /**
   * @param string $key
   * @param mixed $value
   * @return void
   */
  public function set($key, $value);

  /**
   * @param string $key
   * @return mixed
   */
  public function get($key);

  /**
   * @return string
   */
  public function getTemplatesDirectory();

  /**
   * @param string $templatesDirectory
   */
  public function setTemplatesDirectory($templatesDirectory);

  /**
   * @param string $templateName
   * @return string
   */
  public function render($templateName = NULL);

}
