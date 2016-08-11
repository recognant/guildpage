var ts3viewer = {

	__anchor: "#ts3-viewer",
	__data: null,
	__timestamp: 0,
	__server: {},
	__channels: [],
	__players: [],
	__uri: null,
	
	load: function(uri) {
		var __this = this;
		var __time = Date.now() || new Date().getTime();
		
		if( __this.__timestamp + 5000 >= __time ) {
			__this.print();
			return;
		}
		
		__this.__uri = uri;
		
		webi.loadJSON(uri).done(function(data) {
			__this.__data = data.data;

			if( $.isPlainObject(__this.__data) === true ) {
				__this.__timestamp = data.lastUpdated;
				__this.__server['host'] = __this.__data.query.address || __this.__data.query.host || "";
				__this.__server['banner_gtx'] = __this.__data.raw.virtualserver_hostbanner_gfx_url || "";
				__this.__server['banner_url'] = __this.__data.raw.virtualserver_hostbanner_url || "";
				__this.__server['port'] = __this.__data.query.port || "";
				__this.__server['clients'] = __this.__data.raw.virtualserver_clientsonline || "";
				__this.__server['clients_max'] = __this.__data.maxplayers || __this.__data.raw.virtualserver_maxclients || "";
				__this.__server['status'] = __this.__data.raw.virtualserver_status || "offline";
				__this.__server['name'] = __this.__data.raw.virtualserver_name || "";
				__this.__server['platform'] = __this.__data.name || __this.__data.raw.virtualserver_platform || "";
				__this.__server['ping'] = __this.__data.raw.virtualserver_total_ping || "";
				__this.__players = __this.__data.players || [];
				__this.__channels = __this.__data.raw.channels || [];
				__this.print();
			}
		});
	},
	
	reload: function() {
		if( this.__uri !== null )
			this.load(this.__uri);
	},
	
	print: function() {
		$(this.__anchor).empty();
		$(this.__anchor).append('<div class="panel-heading" style="padding: 1px 10px 0 10px;"><h4><button type="button" style="margin-top: -5px;" class="btn-circle btn-sm btn-danger pull-right" onclick="ts3viewer.reload();"><i class="fa fa-refresh fa-fw"></i></button>Teamspeak 3</h4></div>');
		
		var tree = $('<ul class="ts3 ts3-body panel-body"></ul>');
		$(this.__anchor).append(tree);
		
		for(var i = 0; i < this.__channels.length; i++) {
			var channel = this.__channels[i] || {};
			var channel_name = channel.channel_name || "";
			var total_clients = channel.total_clients || 0;
			var cid = channel.cid || -1;
			var pid = channel.pid || 0;
			var e = $('<li id="channel-' + cid + '" class="ts3 channel"><span class="collapser"></span><i class="fa fa-comment fa-fw"></i>' + channel_name + '</li>');
			
			if(pid == 0) {
				tree.append(e);
			} else {
				var ul = $('#channel-' + pid).find("ul:first");
				
				if( ul.length > 0) {
					ul.append(e);
				} else {
					var ul = $('<ul class="ts3 players"></ul>');
					ul.append(e);
					$('#channel-' + pid).append(ul);
				}
				
			}
			
			if(total_clients > 0) {
				//e.append('<div class="badge">' + total_clients + '</div>');
				var ul = $('<ul class="ts3 players"></ul>');
				e.append(ul);
				for(var j = 0; j < this.__players.length; j++) {
					var player = this.__players[j] || {};
					var player_name = player.client_nickname || "";
					var player_type = player.client_type || "-1";
					
					if(player_type === "0" && cid === player.cid) {
						ul.append('<li class="ts3 player"><i class="fa fa-user fa-fw"></i>' + player_name + '</li>');
					}
				}
			}
		}
		
		tree.find("span.collapser").each(function() {
			if( $(this).parent().find("ul:first").children().length > 0 ) {
				$(this).toggleClass('selectable');
				$(this).click(function() {
					$(this).parent().toggleClass('in');
					$(this).parent().find("ul:first").toggle();
				});
			} else {
				$(this).parent().toggleClass('empty');
			}
		});
	},
	
	serverinfo: function() {
		return this.__server;
	}

};