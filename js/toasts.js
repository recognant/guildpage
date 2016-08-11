var $Toasts = {

	__anchor: "#toasts",
	__width: 450,
	__interval: 5000,
	__close: true,
	
	__toast: function(msg, opt) {

		var opt = $.type(opt) === "object" ? opt : {} ;

		var __o = {
			
			__body: null,
			__interval: 0,
			
			__show: function() {
				$($Toasts.__anchor).append(this.__body);
			},
		
			__hide: function() {
				this.__body.fadeOut(750, function() {
					this.remove();
				});
			},
			
			run: function() {
				this.__show();
				
				var __this = this;
				setTimeout(function() { __this.__hide(); }, this.__interval);
			}
		
		};
		
		__o.__body = $('<div class="alert" role="alert" align="left"></div>');
		__o.__body.css('margin-bottom', "5px");
		
		var width = opt && opt.hasOwnProperty('width') ? ( $.type(opt['width']) === "number" ? opt['width'] : ( isNaN(parseFloat(opt['width'])) ? $Toasts.__width : parseFloat(opt['width']) ) ) : $Toasts.__width;
		__o.__body.css('width', width + "px");
		
		__o.__interval = opt && opt.hasOwnProperty('interval') ? ( $.type(opt['interval']) === "number" ? opt['interval'] : ( isNaN(parseFloat(opt['interval'])) ? $Toasts.__interval : parseFloat(opt['interval']) ) ) : $Toasts.__interval;
		
		if( opt && opt.hasOwnProperty('close') && opt['close'] === true ) {
			__o.__body.addClass('alert-dismissible');
			__o.__body.append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
		}
		
		var type = opt && opt.hasOwnProperty('type') ? opt['type'] : "";
		switch(type) {
		case "success":
			__o.__body.addClass('alert-success');
			__o.__body.append('<i class="fa fa-lg fa-check-circle"></i>');
			break;
		case "warning":
			__o.__body.addClass('alert-warning');
			__o.__body.append('<i class="fa fa-lg fa-exclamation-circle"></i>');
			break;
		case "danger":
			__o.__body.addClass('alert-danger');
			__o.__body.append('<i class="fa fa-lg fa-exclamation-triangle"></i>');
			break;
		case "info":
		case "":
		default:
			__o.__body.addClass('alert-info');
			__o.__body.append('<i class="fa fa-lg fa-bell-o"></i>');
			break;
		}
		
		if( $.type(msg) === "string" ) {
			__o.__body.append('<b>' + msg + '</b>');
		}
		
		return __o;
	},
	
	success: function(msg, opt) {
		var __msg = $.type(msg) === "string" ? msg : "";
		var __opt = $.type(opt) === "object" ? opt : {};
		var __t = $Toasts.__toast(__msg, $.extend({ close: $Toasts.__close, width: $Toasts.__width }, $.extend(__opt, { type: "success" })));
		__t.run();
	},
	
	warning: function(msg, opt) {
		var __msg = $.type(msg) === "string" ? msg : "";
		var __opt = $.type(opt) === "object" ? opt : {};
		var __t = $Toasts.__toast(__msg, $.extend({ close: $Toasts.__close, width: $Toasts.__width }, $.extend(__opt, { type: "warning" })));
		__t.run();
	},
	
	alert: function(msg, opt) {
		var __msg = $.type(msg) === "string" ? msg : "";
		var __opt = $.type(opt) === "object" ? opt : {};
		var __t = $Toasts.__toast(__msg, $.extend({ close: $Toasts.__close, width: $Toasts.__width }, $.extend(__opt, { type: "danger" })));
		__t.run();
	},
	
	info: function(msg, opt) {
		var __msg = $.type(msg) === "string" ? msg : "";
		var __opt = $.type(opt) === "object" ? opt : {};
		var __t = $Toasts.__toast(__msg, $.extend({ close: $Toasts.__close, width: $Toasts.__width }, $.extend(__opt, { type: "info" })));
		__t.run();
	}

};