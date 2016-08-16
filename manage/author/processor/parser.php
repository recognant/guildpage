<?php

class Parser {
	
	var $doc;
	var $root;
	
	function __construct() {
		$this->doc = new DOMDocument('1.0', 'utf-8');
		$this->doc->preserveWhiteSpace = false;
		$this->doc->formatOutput = true;

		$this->root = $this->doc->createElement("guide");
		$this->doc->appendChild($this->root);
	}

	public function run($tokenstream=array()) {
		$result = $this->parseToken($tokenstream, $this->root, array());
		$this->doc->normalize();

		if( $result["status"] === 0 ) {
			if( count($result["log"]) > 0 ) {
				throw new Exception( print_r( array( "status" => 1, "error" => array("lineNumber" => end($result["log"])->lineNumber, "rowNumber" => end($result["log"])->rowNumber, "token" => end($result["log"])->type ) ) ) );
			}
			return $this->doc;
			
		}
		else {
			throw new Exception( print_r($result) );
		}
	}
	
	private function parseToken($tokenstream=array(), $root, $open) {
		if($tokenstream === null || sizeof($tokenstream) <= 0)
			return array("status" => 0, "error" => null, "log" => $open);
		
		$token = array_shift($tokenstream);
		$tokenType = $token->type;
				
		switch( $tokenType ) {
		/* Tag and closing tag, no variables */
		case "title":
		case "b":
		case "u":
		case "i":
		case "important":
		case "code":
		case "chapter":
		case "subchapter":
		case "subsubchapter":
			if( $token->isClosing ) {
				$last = array_pop($open);
				
				if( $last->type === $tokenType ) {
					$root = $root->parentNode;
				}
				else {
					return array( "status" => 1, "error" => array("lineNumber" => $last->lineNumber, "rowNumber" => $last->rowNumber, "token" => $last->type ) );
				}
			}
			else {
				array_push($open, $token);
				$element = $this->doc->createElement($tokenType);
				$element->setAttribute("raw", $token->text);
				$root->appendChild($element);
				$root = $element;
			}
			break;
			
		case "br":
		case "text":
			$element = $this->doc->createElement($tokenType);
			$element->setAttribute("raw", $token->text);
			$root->appendChild($element);
			break;
			
		case "icon":
			$element = $this->doc->createElement($tokenType);
			$element->setAttribute("raw", $token->text);
			$root->appendChild($element);
			
			foreach($token->variables as $key=>$value) {
				if( $key === "value") {
					$element->setAttribute("value", $value);
				}
			}
			break;
			
		case "*":
			$element = $this->doc->createElement("star");
			$element->setAttribute("raw", $token->text);

			$last = end($open);
			switch( $last->type ) {
			case "*":
				$root = $root->parentNode;
				break;
			case "list":
				array_push($open, $token);
				break;
			default:
				return array( "status" => 1, "error" => array("lineNumber" => $last->lineNumber, "rowNumber" => $last->rowNumber, "token" => $last->type ) );
			}

			$root->appendChild($element);
			$root = $element;
			foreach($token->variables as $key => $value) {
				$element->setAttribute($key, $value);
			}
			break;
			
		case "list":
			if( $token->isClosing ) {
				$last = array_pop($open);
				
				switch( $last->type ) {
				case "*":
					$root = $root->parentNode->parentNode;
					array_pop($open);
					break;
				case "list":
					$root = $root->parentNode;
					break;
				default:
					return array( "status" => 1, "error" => array("lineNumber" => $last->lineNumber, "rowNumber" => $last->rowNumber, "token" => $last->type ) );
				}
			}
			else {
				array_push($open, $token);
				$element = $this->doc->createElement($tokenType);
				$element->setAttribute("raw", $token->text);
				$root->appendChild($element);
				$root = $element;
				foreach($token->variables as $key => $value) {
					$element->setAttribute($key, $value);
				}
			}
			break;
			
		case "img":
		case "size":
		case "quote":
		case "url":
		case "alert":
		case "panel":
		case "video":
		case "header":			
		default:
			if( $token->isClosing ) {
				$last = array_pop($open);
				
				if( $last->type === $tokenType ) {
					$root = $root->parentNode;
				}
				else {
					return array( "status" => 1, "error" => array("lineNumber" => $last->lineNumber, "rowNumber" => $last->rowNumber, "token" => $last->type ) );
				}
			}
			else {
				array_push($open, $token);
				$element = $this->doc->createElement($tokenType);
				$element->setAttribute("raw", $token->text);
				$root->appendChild($element);
				$root = $element;
				foreach($token->variables as $key => $value) {
					$element->setAttribute($key, $value);
				}
			}
			break;
		}
		
		return $this->parseToken($tokenstream, $root, $open);
	}

}

?>