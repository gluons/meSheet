<?php

/**
 * String helper
 *
 * @author Illuminator
 */
class StringHelper {
	/**
	 * Prettify text in HTML
	 * 
	 * @param string $text
	 * @return string
	 */
	public static function prettifyText($text) {
		$result = "";
		$text = str_split($text);
		foreach($text as $char) {
			if(ctype_alpha($char)) {
				$result .= "&" . $char . "scr;";
			} else if($char == " ") {
				$result .= "&nbsp;";
			}
		}
		return $result;
	}
}
