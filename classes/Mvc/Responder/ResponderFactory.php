<?php
namespace AppZap\PHPFramework\Mvc\Responder;

use AppZap\PHPFramework\SignalSlot\Dispatcher as SignalSlotDispatcher;

class ResponderFactory {

	const SIGNAL_REGISTERED_RESPONDER_CLASSES = 1417079190;

	/**
	 * @return array
	 */
	protected static function getRegisteredResponderClasses() {
		$registeredResponderClasses = [
			'\AppZap\PHPFramework\Mvc\Responder\ControllerResponder',
			'\AppZap\PHPFramework\Mvc\Responder\CallableResponder',
			'\AppZap\PHPFramework\Mvc\Responder\StringResponder',
			'\AppZap\PHPFramework\Mvc\Responder\SubpathResponder',
		];
		SignalSlotDispatcher::emitSignal(self::SIGNAL_REGISTERED_RESPONDER_CLASSES, $registeredResponderClasses);
		return $registeredResponderClasses;
	}

	/**
	 * @param mixed $definition
	 * @return AbstractResponder
	 * @throws \AppZap\PHPFramework\Mvc\Responder\InvalidRegisteredResponderException
	 */
	public static function createResponderFromDefinition($definition) {
		foreach(self::getRegisteredResponderClasses() as $registeredResponderClass) {
			if (!class_exists($registeredResponderClass)) {
				throw new InvalidRegisteredResponderException('Registered responder ' . $registeredResponderClass . ' could not be autoloaded.', 1417076569);
			}
			$responder = new $registeredResponderClass();
			if (!$responder instanceof AbstractResponder) {
				throw new InvalidRegisteredResponderException('Registered responder ' . $registeredResponderClass . ' is not an instance of \\AppZap\\PHPFramework\\Mvc\\Responder\\AbstractResponder', 1417076521);
			}
			if ($responder->handlesDefinition($definition)) {
				return $responder;
			}
			unset($responder);
		}
		throw new InvalidRegisteredResponderException('No registered responder was able to handle the following definition: ' . print_r($definition, 1), 1417076676);
	}

}