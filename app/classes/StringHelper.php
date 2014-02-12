<?php

/**
 * Description of StringHelper
 *
 * @author Illuminator
 */
class StringHelper {
	// Prettify text in HTML
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
