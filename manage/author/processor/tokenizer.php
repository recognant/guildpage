<?php

include_once(dirname(__FILE__) . "/token.php");

class Tokenizer {

	var $handle = null;
	
	function __construct($handle = null) {
		$this->handle = $handle;
	}

	function tokenize() {
		$tokens = array();
		
		if($this->handle == null)
			return $tokens;
	
		$line_number = 0;
	
		while(!feof($this->handle)){
			$line = rtrim(fgets($this->handle));
			$tokens = array_merge($tokens, $this->parseLine($line, $line_number++));
		}
		
		return $tokens;
	}
	
	function parseLine($line, $line_number) {
		return $this->getTokens($line, 0, $line_number);
	}
	
	function getTokens($line, $startIndex, $line_number) {
		$split = str_split($line);
		$tokens = array();
		$token = "";
		$i = $startIndex;
		
		while( $i < sizeof($split) ) {
			switch( $split[$i] ) {
			case "[":
				$result = $this->getKeyword($split, $i);
				
				if( $result[1])  {
					if( !empty($token) )
						$tokens[] = new Token($token, $line_number, $i);
						
					$token = $result[2];
					$tokens[] = new Token($token, $line_number, $i);
					$token = "";
				}
				else {
					$token = $token . $result[2];
				}
				
				$i = $result[0];
				break;
			default:
				if($split[$i] != "\s")
					$token = $token . $split[$i];
			}
			$i++;
		}
		
		if( !empty($token) ) {
			$tokens[] = new Token($token, $line_number, $i);
			$token = "";
		}

		return $tokens;
	}

	function getKeyword($split, $startIndex = 0) {
		$pos = array_search("]", array_slice($split, $startIndex));
		
		if( is_numeric($pos) ) {
			$token = implode( array_slice($split, $startIndex, $pos + 1) );
			$isValid = preg_match("/^\[(([a-zA-Z\*])+((=.+)?))(,([a-zA-Z]+=.+))*(,\{(,[a-zA-Z]+)*\})?\]/", $token) || preg_match("/^\[\/([a-zA-Z])+\]/", $token);
			if( $isValid ) {
				return array( $startIndex + $pos, true, $token);
			}
			else {
				return array( $startIndex, false, implode( array_slice($split, $startIndex, 1) ) );
			}
		}
		else {
			return array( $startIndex, false, implode( array_slice($split, $startIndex, 1) ) );
		}
	}

}

?>