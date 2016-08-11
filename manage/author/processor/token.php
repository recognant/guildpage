<?php

class Token {
	
	var $text;
	var $type;
	var $lineNumber;
	var $rowNumber;
	var $length;
	
	var $isClosing;
	var $variables;
	
	var $tokenlist = array("title", "icon", "br", "u", "i", "b", "*", "img", "size", "important", "list", "code", "quote", "url", "alert", "panel", "video", "header", "chapter", "subchapter", "subsubchapter");

	public function __toString() {
		return "[" . ( $this->isClosing ? "/" : "" ) . $this->type . "]";
	}
	
	function __construct($text, $lineNumber=0, $rowNumber=0) {
		$this->text = $text;
		$this->lineNumber = $lineNumber;
		$this->rowNumber = $rowNumber;
		$this->length = strlen($text);
		
		$isKeyword = preg_match("/^(\[(\w|\*)+(=.+)?((\,.+\=.*)*)?(\{\w+((,(.+)*)*)?\})?\])$/i", $text) > 0 || preg_match("/^(\[\/(\w|\*)+((\,.+\=.*)*)?\])$/i", $text) > 0;

		if( !$isKeyword) {
			$this->type = "text";
			
		} else {
			$this->isClosing = preg_match("/^(\[\/(\w|\*)+((\,.+\=.*)*)?\])$/i", $text) > 0;
		
			preg_match("/^(\[(\/)?(?P<keyword>((\w|\*|)+))(=(?P<value>([^\,\s])+))?(?P<vars>((,((.)+\=(.)*|\@(.)+))*)?)\])$/i", $text, $result);
			
			$key = array_key_exists("keyword", $result) ? $result["keyword"] : "";
			$value = array_key_exists("value", $result) ? $result["value"] : "";
			$vars = array_key_exists("vars", $result) ? $result["vars"] : "";
			
			if( !in_array($key, $this->tokenlist) ) {
				$this->type = "text";
			
			} else {
				$this->type = $key;
				
				/* variables */
				$variables = array();
				
				preg_match_all('/\,(\s*)(?P<key>(\w|\d)+)(\s*)\=(\s*)(?P<value>([^(\,|\"|\'|\´|\`)])*)(\s*)/i', $vars, $matches);
				if(array_key_exists("key", $matches) && sizeof($matches["key"]) > 0) {
					$keys = $matches["key"];
					$values = $matches["value"];
					
					$i = 0;
					while($i < sizeof($keys)) {
						$variables[trim($keys[$i])] = trim($values[$i]);
						$i++;
					}
				}
				
				/* classes */
				preg_match_all('/(\s*)(\@(?P<class>(\w|\_|\-|\d)+))(\s*)/i', $vars, $matches);
				if(array_key_exists("class", $matches) && sizeof($matches["class"]) > 0) {
					$classes = $matches["class"];
					
					$i = 0;
					while($i < sizeof($classes)) {
						$class = trim($classes[$i]);
						$variables[$class] = $class;
						$i++;
					}
				}
				
				/* set tag value */
				$variables["value"] = $value;
				
				$this->variables = $variables;
			}
			
			
		}

	}
	
}

?>