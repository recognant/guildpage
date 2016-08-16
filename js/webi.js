var webi = {};

webi.content = {};

webi.content.load = function(file) {
	if( $.type(file) === "string") {
		file = file.replace(/\.\.\//ig, "");
		
		$("#content").html("<div class='content-loader'><i class='fa fa-spinner fa-pulse fa-fw'></i><strong>Lädt</strong></div>");
		$("#content").load(file, function(response, status, xhr) {
			if( status === "error") {
				$("#content").load("views/notfound.html");
			}
			$WowheadPower.refreshLinks();
		});
	} else {
		$("#content").load("views/notfound.html");
	}
};

webi.content.loadJSON = function(container, url, data) {	
	var __that = this;
	var __container = container;
	var __xhr = webi.loadJSON(url, data);
		
	__container.html("<div class='content-loader'><i class='fa fa-spinner fa-pulse fa-fw'></i><strong>Lädt</strong></div>");

	this.done = function(func) {
		if( $.type(func) === "function" ) {
			__xhr.done(function(data) {
				__container.empty();
				func.call(__container, data);
			});
			return __that;
		}		
	};
	
	this.fail = function(func) {
		if( $.type(func) === "function" ) {
			__xhr.fail(function() {
				__container.empty();
				func.call(__container);
			});
			return __that;
		}
	};
	
	this.always = function(func) {
		if( $.type(func) === "function" ) {
			__xhr.always(function() {
				__container.empty();
				func.call(__container);
			});
			return __that;
		}
	};
	
	return __that;
};

webi.loadJSON = function(url, data) {
	var __url = "";
	var __data = {};
	
	if( $.type(url) === "string")
		__url = url;
	else
		return false;
		
	if( $.isPlainObject(data) )
		__data = $.extend(__data, data);	
	
	return $.getJSON(__url, __data);
};