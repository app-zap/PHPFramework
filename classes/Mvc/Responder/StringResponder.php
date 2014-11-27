<?php
namespace AppZap\PHPFramework\Mvc\Responder;

use AppZap\PHPFramework\Configuration\Configuration;
use AppZap\PHPFramework\Mvc\Request;

class StringResponder extends AbstractResponder {

	/**
	 * @var string
	 */
	protected $reponseString;

	/**
	 * @param mixed $definition
	 *
	 * @return bool
	 */
	public function handlesDefinition($definition) {
		if (Configuration::get('phpframework', 'responder.\AppZap\PHPFramework\Mvc\Responder\StringResponder.enable', FALSE) && is_string($definition)) {
			$this->reponseString = $definition;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @param Request $request
	 *
	 * @return string
	 */
	public function dispatch(Request $request) {
		return $this->reponseString;
	}

}