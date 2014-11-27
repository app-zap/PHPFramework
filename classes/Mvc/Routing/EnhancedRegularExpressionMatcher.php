<?php
namespace AppZap\PHPFramework\Mvc\Routing;

class EnhancedRegularExpressionMatcher {

	/**
	 * @param $regularExpression
	 * @param $string
	 *
	 * @return array
	 */
	public static function match($regularExpression, $string) {
		$regularExpression = self::enhanceRegularExpression($regularExpression);
		if (preg_match($regularExpression, $string, $matches)) {
			array_shift($matches);
			return $matches;
		}
		return NULL;
	}

	/**
	 * @param string $regularExpression
	 *
	 * @return string
	 */
	public static function enhanceRegularExpression($regularExpression) {
		set_error_handler(function() {}, E_WARNING);
		$isRegularExpression = preg_match($regularExpression, "") !== FALSE;
		restore_error_handler();
		if (!$isRegularExpression) {
			// if $regex is no regular expression
			if ($regularExpression === '.') {
				$regularExpression = '';
			}
			if ( ! $regularExpression || $regularExpression{0} !== '%') {
				$regularExpression = '^' . $regularExpression;
			} else {
				$regularExpression = substr($regularExpression, 1);
			}
			if ( ! $regularExpression || $regularExpression{strlen($regularExpression) - 1} !== '%') {
				$regularExpression .= '$';
			} else {
				$regularExpression = substr($regularExpression, 0, strlen($regularExpression) - 1);
			}
			$regularExpression = str_replace('?', '([a-z0-9]*)', $regularExpression);
			$regularExpression = '|' . $regularExpression . '|';
		}
		return $regularExpression;
	}

}