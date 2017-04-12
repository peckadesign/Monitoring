(function($, undefined) {

	$.nette.ext('websocket', {
		init: function () {
			var onConnect = this.onConnect;

			var config = WebSocketConfig || {};
			this.debug = config.debug;
			this.server = config.server;

			$('[data-socket]').each(function () {
				var $self = $(this);
				onConnect.push(function () {
					var queue = $self.data('socket');
					var key = $self.data('socket-key');
					var link = $self.data('socket-link');
					this.subscribe("/exchange/" + queue + "/" + key, function (message) {
						var options = {
							url: link,
							method: 'get',
						};
						$.nette.ajax(options);
					});
				});
			});

			this.connect(this.server);
		}
	}, {
		connect: function(server) {
			var ws = new WebSocket(server);
			var client = Stomp.over(ws);
			if (!this.debug) {
				client.debug = undefined;
			}

			var connected = false;
			var errorOccurred = false;

			var onConnect = this.onConnect;

			client.connect({
				login: 'guest',
				passcode: 'guest',
				host: '/'
			}, function () {
				onConnect.forEach(function (onConnect) {
					onConnect.apply(client);
				});
				connected = true;
			}, function (message) {
				if (typeof message === 'object' && 'command' in message && message.command === 'ERROR') {
					errorOccurred = true;
				}
			});

			var onClose = ws.onclose; //needs to be after client.connect
			ws.onclose = function (event) {
				onClose.apply(arguments);
				if (connected && !errorOccurred) {
					this.connect(); //reconnect
				}
			};
		},
		onConnect: [],
		debug: false,
		server: 'ws://localhost:15672/ws'

	});

})(jQuery);
