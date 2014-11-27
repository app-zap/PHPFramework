<?php
namespace AppZap\PHPFramework\Mvc\Responder;

use AppZap\PHPFramework\Mvc\Request;
use AppZap\PHPFramework\Mvc\Routing\EnhancedRegularExpressionMatcher;

class SubpathResponder extends AbstractResponder {

	/**
	 * @var array
	 */
	protected $subroutes;

	/**
	 * @param mixed $definition
	 *
	 * @return bool
	 */
	public function handlesDefinition($definition) {
		if (is_array($definition)) {
			$this->subroutes = $definition;
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
		$subpath = preg_replace(EnhancedRegularExpressionMatcher::enhanceRegularExpression($this->matchedRegularExpression), '', $this->resource);
		// todo: route $subpath with $this->subroutes
	}

}
