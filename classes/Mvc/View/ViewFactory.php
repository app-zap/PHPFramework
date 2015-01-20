<?php
namespace AppZap\PHPFramework\Mvc\View;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\SignalSlot\Dispatcher as SignalSlotDispatcher;
use AppZap\PHPFramework\Singleton;

class ViewFactory {
  use Singleton;

  const SIGNAL_VIEW_CLASSNAME = 1421760471;

  /**
   * @var string
   */
  protected $defaultViewClassname = 'AppZap\PHPFramework\Mvc\View\TwigView';

  /**
   * @return ViewInterface
   * @throws ViewFactoryException
   */
  public function createView() {
    $viewClassname = $this->defaultViewClassname;
    SignalSlotDispatcher::emitSignal(self::SIGNAL_VIEW_CLASSNAME, $viewClassname);
    if (!class_exists($viewClassname)) {
      throw new ViewFactoryException('Class ' . $viewClassname . ' couldn\'t be loaded by the ViewFactory', 1421760637);
    }
    /** @var ViewInterface $view */
    $view = new $viewClassname();
    if (!$view instanceof ViewInterface) {
      throw new ViewFactoryException('Class ' . $viewClassname . ' can\'t be used as view because it doesn\'t implement the ViewInterface', 1421760697);
    }
    $view->setTemplatesDirectory(Configuration::get('application', 'templates_directory'));
    return $view;
  }

}
