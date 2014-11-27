<?php
namespace AppZap\PHPFramework\Mvc\Responder;

use AppZap\PHPFramework\Mvc\Request;

class CallableResponder extends AbstractResponder {

	/**
	 * @var \callable
	 */
	protected $callable;

	/**
	 * @param mixed $definition
	 *
	 * @return bool
	 */
	public function handlesDefinition($definition) {
		if (is_callable($definition)) {
			$this->callable = $definition;
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
		return call_user_func($this->callable, $request->getParameters());
	}
}