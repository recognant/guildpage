<?php

include_once(dirname(__FILE__) . "/../utils/index.php");

class Transformer {

	var $xml = null;
	
	var $chapter = 0;
	var $subchapter = 0;
	var $subsubchapter = 0;
	
	var $id = 0;
	
	function __construct($xml) {
		$this->xml = $xml;
	}
	
	function transform() {
		$html = new DOMDocument('1.0', 'utf-8');
		$html->preserveWhiteSpace = false;
		$html->formatOutput = true;
		
		$head = $html->createElement("head");
		$title = $html->createElement("title");
		$body = $html->createElement("body");
		
		$head->appendChild($title);
		$html->appendChild($head);
		$html->appendChild($body);
		
		$root = $this->xml->getElementsByTagName("guide")->item(0);
		$this->transformNode($root, $html, $body);
		
		$title->appendChild( $html->createTextNode( "" ) );

		return $html;
	}
	
	function transformNode($xmlRoot, $html, $root, $options=array()) {
		$e = null;
		$children = $xmlRoot->childNodes;
		
		if( $children === null || $children->length === 0)
			return;
		
		foreach($children as $child) {
			switch($child->nodeName) {
			case "title":
				$this->transformTitle($child, $html, $root, $options);
				break;
			case "b":
				$this->transformBold($child, $html, $root, $options);
				break;
			case "u":
				$this->transformUnderlined($child, $html, $root, $options);
				break;
			case "i":
				$this->transformItalic($child, $html, $root, $options);
				break;
			case "size":
				$this->transformSize($child, $html, $root, $options);
				break;
			case "list":
				$this->transformList($child, $html, $root, $options);
				break;
			case "text":
				$this->transformText($child, $html, $root, $options);
				break;	
			case "url":
				$this->transformURL($child, $html, $root, $options);
				break;
			case "img":
				$this->transformImage($child, $html, $root, $options);
				break;
			case "video":
				$this->transformVideo($child, $html, $root, $options);
				break;
			case "important":
				$this->transformImportant($child, $html, $root, $options);
				break;
			case "code":
				$this->transformCode($child, $html, $root, $options);
				break;
			case "quote":
				$this->transformQuote($child, $html, $root, $options);
				break;
			case "icon":
				$this->transformIcon($child, $html, $root, $options);
				break;
			case "br":
				$this->transformBreak($child, $html, $root, $options);
				break;
			case "alert":
				$this->transformAlert($child, $html, $root, $options);
				break;
			case "panel":
				$this->transformPanel($child, $html, $root, $options);
				break;
			case "header":
				$this->transformHeader($child, $html, $root, $options);
				break;
			case "chapter":
				$this->transformChapter($child, $html, $root, $options);
				break;
			case "subchapter":
				$this->transformSubChapter($child, $html, $root, $options);
				break;
			case "subsubchapter":
				$this->transformSubSubChapter($child, $html, $root, $options);
				break;
			default:
			}
		}
		
	}
	
	function transformList($xmlRoot, $html, $root, $options=array()) {
		$type = $xmlRoot->getAttribute("value");
				
		if( $type === null || empty($type))
			$type = "default";
					
		$isValid = preg_match("/^(numeric|tab|default|media|column|row|table)$/i", $type) > 0;
		
		// recover list type
		if( !$isValid)
			$type = "default";
			
		$options["list-type"] = $type;
		
		$e = null;
		
		switch($type) {
		case "table":
			$e = $html->createElement("table");
			$e->setAttribute("class", "table table-striped");
			
			$columns = $xmlRoot->getAttribute("columns");
			if( !isset($columns) || $columns === null || empty($columns))
				return;
				
			$maxWidth = intval($columns);
			$width = 0;
			
			$titles = $xmlRoot->getAttribute("title");
			if( !empty($titles) ) {
				$titles = explode(";", $titles);
				$header = $html->createElement("thead");
				
				if( sizeof($titles) > 0) {
					foreach($titles as $title) {
						$th = $html->createElement("th");
						$th->appendChild( $html->createTextNode($title) ); 
						$header->appendChild( $th );
					}
					$e->appendChild($header);
				}
			}
			
			$children = $xmlRoot->childNodes;
			
			if( $children === null || $children->length === 0)
				return;

			$root->appendChild($e);
			$body = $html->createElement("tbody");
			$e->appendChild($body);
			
			$row = $html->createElement("tr");
			$body->appendChild($row);
			foreach($children as $child) {
				if( $child->nodeName === "star") {
					if( $width >= $maxWidth) {
						$row = $html->createElement("tr");
						$body->appendChild($row);
						$width = 0;
					}
					$this->transformStar($child, $html, $row, $options);
					$width++;
				}
			}
			
			for($i = $width; $i < $maxWidth; $i++) {
				$row->appendChild( $html->createElement("td") );
			}
			
			return;
		
		case "row":
			$e = $html->createElement("div");
			$e->setAttribute("class", "row");
			
			$fix = $html->createElement("div");
			$fix->setAttribute("class", "clearfix");
			
			$root->appendChild($e);
			$root->appendChild($fix);
		
			$children = $xmlRoot->childNodes;
			
			if( $children === null || $children->length === 0)
				return;
				
			foreach($children as $child) {
				if( $child->nodeName === "star") {
					$this->transformStar($child, $html, $e, $options);
				}
			}
			return;
		case "column":
			$e = $html->createElement("div");
			$e->setAttribute("class", "row");
			
			$fix = $html->createElement("div");
			$fix->setAttribute("class", "clearfix");
			
			$root->appendChild($e);
			$root->appendChild($fix);
		
			$children = $xmlRoot->childNodes;
			
			if( $children === null || $children->length === 0)
				return;
				
			$length = 0;
			foreach($children as $child) {
				if( $child->nodeName === "star") {
					$length++;
				}
			}
			$options["children-length"] = $length;
			
			$i = 0;
			foreach($children as $child) {
				if( $child->nodeName === "star") {
					$options["child-index"] = $i++;
					$this->transformStar($child, $html, $e, $options);
				}
			}
			return;
		case "media":
			$e = $html->createElement("div");
			$e->setAttribute("style", "padding-top: 10px;");
			break;
		case "tab":
			$e = $html->createElement("div");
			$e->setAttribute("role", "tabpanel");
			
			$ul = $html->createElement("ul");
			$ul->setAttribute("class", "nav nav-tabs nav-justified");
			$ul->setAttribute("role", "tablist");
			
			$body = $html->createElement("div");
			$body->setAttribute("class", "tab-content");
			$body->setAttribute("style", "border: 1px solid #ddd; border-top:0; padding-top: 15px; padding-bottom: 15px;");
			
			$e->appendChild($ul);
			$e->appendChild($body);
			$root->appendChild($e);
			
			$options["tab-header"] = $ul;
		
			$children = $xmlRoot->childNodes;
			
			if( $children === null || $children->length === 0)
				return;
			
			$i = 0;
			foreach($children as $child) {
				if( $child->nodeName === "star") {
					$options["child-index"] = $i++;
					$this->transformStar($child, $html, $body, $options);
				}
			}
			
			return;
		case "default":
			$e = $html->createElement("ul");
			break;
		case "numeric":
			$e = $html->createElement("ol");
			break;
		default:
			$e = $html->createElement("ul");
		}
		
		$root->appendChild($e);
		
		$children = $xmlRoot->childNodes;
		
		if( $children === null || $children->length === 0)
			return;
		
		foreach($children as $child) {
			if( $child->nodeName === "star") {
				$this->transformStar($child, $html, $e, $options);
			}
		}
	}
	
	function transformTitle($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("div");
		$e->setAttribute("class", "page-header");
		
		$h = $html->createElement("h1");
		
		if( isset($options["text-size"]) ) {

		switch( $options["text-size"] ) {
			case "x-small":
				$h = $html->createElement("h6");
				break;
			case "small":
				$h = $html->createElement("h5");
				break;
			case "normal":
				$h = $html->createElement("h4");
				break;
			case "large":
				$h = $html->createElement("h3");
				break;
			case "x-large":
				$h = $html->createElement("h2");
				break;
			case "xx-large":
				$h = $html->createElement("h1");
				break;
			default:
			}
		}
		
		$e->appendChild($h);
		$root->appendChild($e);
		$this->transformNode($xmlRoot, $html, $h, $options);
	}
	
	function transformChapter($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("h2");
		$this->subchapter = 0;
		$this->subsubchapter = 0;
		$e->setAttribute("class", "gs-chapter");
		$e->appendChild( $html->createTextNode(++$this->chapter . ". ") );
		$root->appendChild($e);
		$this->transformNode($xmlRoot, $html, $e, $options);
	}
	
	function transformSubChapter($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("h3");
		$this->subsubchapter = 0;
		$e->setAttribute("class", "gs-chapter gs-subchapter");
		$e->appendChild( $html->createTextNode($this->chapter . "." . ++$this->subchapter . " ") );
		$root->appendChild($e);
		$this->transformNode($xmlRoot, $html, $e, $options);
	}
	
	function transformSubSubChapter($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("h4");
		$e->setAttribute("class", "gs-chapter gs-subsubchapter");
		$e->appendChild( $html->createTextNode($this->chapter . "." . $this->subchapter . "." . ++$this->subsubchapter . " ") );
		$root->appendChild($e);
		$this->transformNode($xmlRoot, $html, $e, $options);
	}
	
	function transformBold($xmlRoot, $html, $root, $options=array()) {
		if( !isset($options["text-style"]) )
			$options["text-style"] = array();
			
		$options["text-style"][] = "bold";

		$this->transformNode($xmlRoot, $html, $root, $options);
	}
	
	function transformUnderlined($xmlRoot, $html, $root, $options=array()) {
		if( !isset($options["text-style"]) )
			$options["text-style"] = array();
			
		$options["text-style"][] = "underlined";
		
		$this->transformNode($xmlRoot, $html, $root, $options);
	}

	function transformItalic($xmlRoot, $html, $root, $options=array()) {
		if( !isset($options["text-style"]) )
			$options["text-style"] = array();
			
		$options["text-style"][] = "italic";
		
		$this->transformNode($xmlRoot, $html, $root, $options);
	}
	
	function transformSize($xmlRoot, $html, $root, $options=array()) {
		$size = $xmlRoot->getAttribute("value");
		
		switch( $size ) {
		case "x-small":
		case "small":
		case "normal":
		case "large":
		case "x-large":
		case "xx-large":
			$options["text-size"] = $size;
			break;
		default:
		}

		$this->transformNode($xmlRoot, $html, $root, $options);
	}
	
	function transformText($xmlRoot, $html, $root, $options=array()) {
		$text = $xmlRoot->getAttribute("raw");

		$classes = array();

		if( isset($options["text-style"]) ) {
		
			if( in_array("bold", $options["text-style"]) ) {
				$classes[] = "gs-text-bold";
			}
			if( in_array("underlined", $options["text-style"]) ) {
				$classes[] = "gs-text-underlined";
			}
			if( in_array("italic", $options["text-style"]) ) {
				$classes[] = "gs-text-italic";
			}
		
		}
		
		if( isset($options["text-size"]) ) {
			
			switch( $options["text-size"] ) {
			case "x-small":
				$classes[] = "gs-text-x-small";
				break;
			case "small":
				$classes[] = "gs-text-small";
				break;
			case "normal":
				break;
			case "large":
				$classes[] = "gs-text-large";
				break;
			case "x-large":
				$classes[] = "gs-text-x-large";
				break;
			case "xx-large":
				$classes[] = "gs-text-xx-large";
				break;
			default:
			}

		}
		
		if( count($classes) == 0 ) {
			$e = $html->createTextNode($text);
		} 
		else {
			$e = $html->createElement("font");
			$e->appendChild( $html->createTextNode($text) );
			$e->setAttribute("class", implode(" ", $classes));
		}

		$root->appendChild($e);
	}
	
	function transformStar($xmlRoot, $html, $root, $options=array()) {
		$e = null;

		if( isset($options["list-type"]) ) {
			switch($options["list-type"]) {
			case "table":
				$e = $html->createElement("td");
				$root->appendChild($e);
				$this->transformNode($xmlRoot, $html, $e, $options);
				break;
			case "row":
				$e = $html->createElement("div");
				$e->setAttribute("class", "col-xs-12");
				
				$root->appendChild($e);
				$this->transformNode($xmlRoot, $html, $e, $options);
				break;
			case "column":
				$e = $html->createElement("div");
				
				$length = intval($options["children-length"]);
				$width = 100 / $length;
				
				$e->setAttribute("style", "position:relative; padding-left:15px; padding-right:15px; min-height:1px; float:left; width:" . $width . "%;");
				
				$root->appendChild($e);
				$this->transformNode($xmlRoot, $html, $e, $options);
				break;
			case "media":
				$e = $html->createElement("div");
				$e->setAttribute("class", "media");
				
				$left = $html->createElement("div");
				$left->setAttribute("class", "media-left media-middle");
				
				$body = $html->createElement("div");
				$body->setAttribute("class", "media-body");
				$header = $html->createElement("h4");
				$header->setAttribute("class", "media-heading");
				
				$title = $xmlRoot->getAttribute("value");
				if( !empty($title) && $title !== null) {
					$header->appendChild( $html->createTextNode($title) );
				}
					
				$src = $xmlRoot->getAttribute("img");
				if( !empty($src) && $src !== null) {
					$img = $html->createElement("img");
					$img->setAttribute("class", "media-object thumbnail gs-media-img");
					$img->setAttribute("src", $src);
					$img->setAttribute("alt", $src);
					$left->appendChild($img);
				}
				
				$e->appendChild($left);
				$body->appendChild($header);
				$e->appendChild($body);

				$root->appendChild($e);
				$this->transformNode($xmlRoot, $html, $body, $options);
				break;
			case "tab":
				$index = $options["child-index"];
				if( !isset($index) || $index === null )
					$index = 0;
				
				$e = $html->createElement("div");
				if($index === 0)
					$e->setAttribute("class", "tab-pane active");
				else
					$e->setAttribute("class", "tab-pane");
				
				
				$header = $options["tab-header"];
				$title = $xmlRoot->getAttribute("value");
				$id = "tab" . ($this->id++);
				
				if( !empty($title) && $title !== null) {
					$li = $html->createElement("li");
					$li->setAttribute("role", "presentation");
					if($index === 0)
						$li->setAttribute("class", "active");
					
					$a = $html->createElement("a");
					$a->setAttribute("role", "tab");
					$a->setAttribute("aria-controls", "" . $id);
					if( $index === 0)
						$a->setAttribute("aria-expanded", "true");
					$a->setAttribute("href", "#" . $id);
					$a->setAttribute("data-toggle", "tab");
					$a->appendChild( $html->createTextNode($title) );
					
					$li->appendChild($a);
					
					if( isset($header) && $header !== null)
						$header->appendChild($li);
				}
				
				$e->setAttribute("id", "" . $id);
				
				$body = $html->createElement("div");
				$body->setAttribute("class", "container-fluid");
				$e->appendChild($body);
				
				$root->appendChild($e);
				$this->transformNode($xmlRoot, $html, $body, $options);
				break;
				
			case "default":
			case "numeric":
			default:
				$e = $html->createElement("li");
				$root->appendChild($e);
				$this->transformNode($xmlRoot, $html, $e, $options);
				break;
			}
		}
		
	}
	
	function transformURL($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("a");
		
		$uri = $xmlRoot->getAttribute("value");
		if( $uri === null || $uri === "") {
			return;
		}
		
		if( $xmlRoot->childNodes == null && $xmlRoot->childNodes->length == 0) {
			$node = $this->xml->createElement("text");
			$node->setAttribute("raw", $uri);
			$xmlRoot->appendChild($node);
		}
		$this->transformNode($xmlRoot, $html, $e, $options);

		$e->setAttribute("href", $uri);
		$e->setAttribute("target", "_blank");
		
		$root->appendChild($e);
	}
	
	function transformImage($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("img");
		
		$uri = $xmlRoot->getAttribute("value");
		if( $uri === null || $uri === "") {
			return;
		}
				
		$text = $uri;
		
		if( $xmlRoot->childNodes !== null && $xmlRoot->childNodes->length !== 0) {
		
			foreach($xmlRoot->childNodes as $child) {
				if( $child->nodeName === "text") {
					$text = $child->getAttribute("raw");
				}
			}

		}

		$e->setAttribute("src", $uri);
		$e->setAttribute("alt", $text);
		$e->setAttribute("class", "img-responsive gs-img");
		
		$root->appendChild($e);
	}
	
	function transformImportant($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("div");
		$e->setAttribute("class", "jumbotron gs-important");
		
		$root->appendChild($e);
		$this->transformNode($xmlRoot, $html, $e, $options);
	}
	
	function transformCode($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("div");
		$e->setAttribute("class", "highlight");
		
		$p = $html->createElement("div");
		$p->setAttribute("class", "pre");
		
		$c = $html->createElement("code");
		
		$e->appendChild($p);
		$p->appendChild($c);
		
		$root->appendChild($e);
		$this->transformNode($xmlRoot, $html, $c, $options);
	}
	
	function transformQuote($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("blockquote");
		
		$p = $html->createElement("p");
		$e->appendChild($p);
		
		$source = $xmlRoot->getAttribute("value");
		if( !empty($source) && $source !== null) { 
			$f = $html->createElement("footer");
			$f->appendChild( $html->createTextNode($source) );
			$e->appendChild($f);
		}
		
		$root->appendChild($e);
		$this->transformNode($xmlRoot, $html, $p, $options);
	}
	
	function transformIcon($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("i");
		
		$type = $xmlRoot->getAttribute("value");
		if( !empty($type) && $type !== null ) {
			$e->setAttribute("class", "fa fa-fw fa-" . $type);
			$root->appendChild($e);
		}
	}
	
	function transformBreak($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("br");
		$root->appendChild($e);
	}
	
	function transformAlert($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("div");
		
		$valid = array("success", "danger", "warning", "info");
		$type = $xmlRoot->getAttribute("value");
		
		if( in_array($type, $valid) ) {
		
			switch($type) {
			case "success":	
				$icon = $html->createElement("i");
				$icon->setAttribute("class", "fa fa-check fa-lg fa-fw");
				break;
			case "danger":
				$icon = $html->createElement("i");
				$icon->setAttribute("class", "fa fa-exclamation-triangle fa-lg fa-fw");
				break;
			case "warning":
				$icon = $html->createElement("i");
				$icon->setAttribute("class", "fa fa-question-circle fa-lg fa-fw");
				break;
			case "info":
				$icon = $html->createElement("i");
				$icon->setAttribute("class", "fa fa-info-circle fa-lg fa-fw");
				break;
			default:
			}
		
			if( isset($icon) ) {
				$e->appendChild($icon);
			}
			
			$e->setAttribute("class", "alert alert-" . $type);
			$e->setAttribute("role", "alert");
		
		}
		
		$root->appendChild($e);
		$this->transformNode($xmlRoot, $html, $e, $options);
	}
	
	function transformPanel($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("div");
		$e->setAttribute("class", "panel panel-default");
		
		$title = $xmlRoot->getAttribute("value");
		if( !empty($title) && $title !== null) {
			$header = $html->createElement("div");
			$header->setAttribute("class", "panel-heading");
			$header->appendChild( $html->createTextNode($title) );
			$e->appendChild($header);
		}

		$body = $html->createElement("div");
		$body->setAttribute("class", "panel-body");
		$e->appendChild($body);
		
		$root->appendChild($e);
		$this->transformNode($xmlRoot, $html, $body, $options);
	}

	function transformVideo($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("div");
		$e->setAttribute("class", "embed-responsive embed-responsive-16by9 center-block gs-video-width");
		
		$uri = $xmlRoot->getAttribute("value");
		if( $uri === null || $uri === "")
			return;
				
		$text = $uri;
		
		if( $xmlRoot->childNodes !== null && $xmlRoot->childNodes->length !== 0) {
		
			foreach($xmlRoot->childNodes as $child) {
				if( $child->nodeName === "text") {
					$text = $child->getAttribute("raw");
				}
			}

		}
		
		$uri = preg_replace("/youtu\.be/i", "youtube.com/watch?v=", $uri);
		$uri = preg_replace("/watch\?v\=/i", "embed/", $uri) . "?rel=0";
		$video = $html->createElement("iframe");
		$video->setAttribute("class", "embed-responsive-item");
		$video->setAttribute("src", $uri);
		$video->setAttribute("alt", $text);
		$video->setAttribute("allowfullscreen", "allowfullscreen");
		
		$e->appendChild($video);
		$root->appendChild($e);
	}
	
	function transformHeader($xmlRoot, $html, $root, $options=array()) {
		$e = $html->createElement("div");
		$e->setAttribute("class", "well");

		$fillParent = $xmlRoot->hasAttribute("fillParent");
		if( !$fillParent ) {
			if( array_key_exists("tab-header", $options) ) {
				$e->setAttribute("style", "border-radius: 0; margin-top:-15px; margin-left:-15px; margin-right:-15px;");
			}
		}
		else {
			if( array_key_exists("tab-header", $options) ) {
				$e->setAttribute("style", "border-radius: 0; margin:-15px;");
			}
		}
		
		$root->appendChild($e);
		$this->transformNode($xmlRoot, $html, $e, $options);
	}
	
}

?>