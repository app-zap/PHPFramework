<?php
namespace AppZap\PHPFramework\Mvc\Responder;

use AppZap\PHPFramework\Mvc\Request;

abstract class AbstractResponder {

	/**
	 * @var string
	 */
	protected $matchedRegularExpression;

	/**
	 * @return string
	 */
	public function getMatchedRegularExpression() {
		return $this->matchedRegularExpression;
	}

	/**
	 * @param string $matchedRegularExpression
	 */
	public function setMatchedRegularExpression($matchedRegularExpression) {
		$this->matchedRegularExpression = $matchedRegularExpression;
	}

	/**
	 * @var string
	 */
	protected $resource;

	/**
	 * @return string
	 */
	public function getResource() {
		return $this->resource;
	}

	/**
	 * @param string $resource
	 */
	public function setResource($resource) {
		$this->resource = $resource;
	}

	/**
	 * @param mixed $definition
	 * @return bool
	 */
	abstract public function handlesDefinition($definition);

	/**
	 * @param Request $request
	 * @return string
	 */
	abstract public function dispatch(Request $request);

}