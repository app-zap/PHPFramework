<?php
namespace AppZap\PHPFramework\Mvc\Responder;

use AppZap\PHPFramework\Mvc\AbstractController;
use AppZap\PHPFramework\Mvc\DispatchingInterruptedException;
use AppZap\PHPFramework\Mvc\Request;
use AppZap\PHPFramework\Mvc\View\AbstractView;
use AppZap\PHPFramework\Mvc\View\TwigView;

class ControllerResponder extends AbstractResponder {

	/**
	 * @var AbstractController
	 */
	protected $controller;

	/**
	 * @param mixed $definition
	 * @return bool
	 */
	public function handlesDefinition($definition) {
		if (is_string($definition) && class_exists($definition)) {
			$this->controller = new $definition();
			if ($this->controller instanceof AbstractController) {
				return TRUE;
			}
			unset($this->controller);
		}
		return FALSE;
	}

	/**
	 * @param \AppZap\PHPFramework\Mvc\Request $request
	 * @return string
	 */
	public function dispatch(Request $request) {
		try {
			$this->controller->setRequest($request);
			if (!method_exists($this->controller, $request->getRequestMethod())) {
				// Send HTTP 405 response
				$this->controller->handle_not_supported_method($request->getRequestMethod());
			}
			$response = $this->createResponse();
			$this->controller->setResponse($response);
			$this->controller->initialize($request->getParameters());
			$output = $this->controller->{$request->getRequestMethod()}($request->getParameters());
			if (is_null($output)) {
				$output = $response->render();
			}
			return $output;
		} catch (DispatchingInterruptedException $e) {
			$output = '';
		}
		return $output;
	}

	/**
	 * @return \AppZap\PHPFramework\Mvc\AbstractController
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * return AbstractView
	 */
	protected function createResponse() {
		/** @var AbstractView $response */
		$response = new TwigView();
		$default_template_name = $this->determineDefaultTemplateName();
		if ($default_template_name) {
			$response->set_template_name($default_template_name);
		}
		return $response;
	}

	/**
	 * @param $responder_class
	 * @return string
	 */
	protected function determineDefaultTemplateName() {
		if (preg_match('|\\\\([a-zA-Z0-9]{2,50})Controller$|', get_class($this->controller), $matches)) {
			return $matches[1];
		}
		return NULL;
	}
}