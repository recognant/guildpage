<?php

include_once(dirname(__FILE__) . "/tokenizer.php");
include_once(dirname(__FILE__) . "/translator.php");
include_once(dirname(__FILE__) . "/parser.php");
include_once(dirname(__FILE__) . "/transformer.php");

$dir = '../node/';

function getNodes() {

	global $dir;
	
	foreach(glob($dir . '*.gfl') as $file) {
		print $file;
	}

}

function process($tag) {

	global $dir;
	
	if(!file_exists($dir . $tag)) {
		return false;
	}
	
	$file = $dir . $tag;
	$handle = fopen($file, "r");
	$tokenstream = (new Tokenizer($handle))->tokenize();

	$tokenstream = (new Translator())->run($tokenstream);

	$parser = new Parser();
	
	try {
		$xml = $parser->run($tokenstream);
	} catch (Exception $e) {
		die($e->getMessage());
	}

	
	$transformer = new Transformer($xml);
	$html = $transformer->transform();
	
	$info = pathinfo($file);
	$htmlFile = $info["dirname"] . "/" . $info["filename"] . ".html"; 
	$html->save($htmlFile);
	
	echo $html->saveHTML();
}

?>