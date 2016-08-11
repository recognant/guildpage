<?php

class Translator {

	function run($tokenstream) {
		$tokens = array();
		
		foreach($tokenstream as $token) {
			$tokens[] = $this->translate($token);
		}

		return $tokens;
	}
	
	function translate($token = null) {
		return $token;
	}

}

?>