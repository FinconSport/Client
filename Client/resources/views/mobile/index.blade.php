<!doctype html><html lang="en"><head><meta charset="utf-8"/><link rel="icon" href="/favicon.ico"/><meta name="theme-color" content="#000000"/><meta name="description" content="Web site created using create-react-app"/><link rel="apple-touch-icon" href="/logo192.png"/><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><link rel="manifest" href="/manifest.json"/><title>React App</title><style>#root,body,html{height:100vh;height:calc(var(--vh,1vh) * 100);overflow:hidden}</style><script defer="defer" src="/static/js/main.55239b51.js"></script><link href="/static/css/main.ef30fc8d.css" rel="stylesheet"></head><body style="overflow:hidden"><noscript>You need to enable JavaScript to run this app.</noscript><div id="root" style="overflow:hidden"></div><script>// var player = 8 ;
		// var token = 12345 ;
		// var lang = 'tw'
		var player = {{ $player }};
		var token = {{ $token }};
		var lang = @json(session('player.lang'));
		console.log(player)
		console.log(token)
		console.log(lang)

		// 投注限額
		var limit = JSON.parse(@json(session('player.limit_data')));
		console.log(limit)
		// var limit = {
		// 	"early": {
		// 		"1": {
		// 			"min": "1000",
		// 			"max": "50000"
		// 		},
		// 		"2": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"3": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"4": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"5": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"6": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"7": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"8": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"9": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		}
		// 	},
		// 	"living": {
		// 		"1": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"2": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"3": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"4": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"5": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"6": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"7": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"8": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		},
		// 		"9": {
		// 			"min": "100",
		// 			"max": "10000"
		// 		}
		// 	}
		// }

		const wsUrl = {
			1: 'wss://soccer.asgame.net/ws',
			2: 'wss://basketball.asgame.net/ws',
			3: 'wss://baseball.asgame.net/ws'
		}
		
		// 菜單與體育彩種預設
		var menu = 0
		var sport = 1

		// websocket
		var ws = null;
		var wsInt = null;
		var socket_status = false;
		var heartbeatTimer = null
		
		function WebSocketDemo(sport) {
            console.log('WebSocketDemo')
			if( wsInt === null ) {
				// 監聽連線狀態
				wsInt = setInterval(reconnent, 5000);
			}
            if ("WebSocket" in window) {
				// console.log(sport)
				ws = new WebSocket(wsUrl[sport]); // 連線
				ws.onopen = function() {
					socket_status = true; // 重連機制
					heartbeatTimer = setInterval(() => { // 心跳
						const heartbeat = {
							"action":"heartbeat",
						}
						if ( ws.readyState === 1 ) {
							console.log(heartbeat)
							ws.send(JSON.stringify(heartbeat));
						}
					}, 10000);
				};

				// websocket is closed.
				ws.onclose = function() { 
					console.log('Connection closed with code: ', event.code);
					socket_status = false;
					// 移除心跳timer
					clearInterval(heartbeatTimer)
				};
            } else {
				// The browser doesn't support WebSocket
				console.log("WebSocket NOT supported by your Browser!");
            }
		}

		 
		// 重連機制
		function reconnent() {
			if (socket_status === false) {
				WebSocketDemo( window.sport);
			}
		}

		function safariHacks() {
			let windowsVH = window.innerHeight / 100;
			document.querySelector('html').style.setProperty('--vh', windowsVH + 'px');
			document.querySelector('body').style.setProperty('--vh', windowsVH + 'px');
			document.querySelector('#root').style.setProperty('--vh', windowsVH + 'px');
			window.addEventListener('resize', function() {
				console.log('resize')
				let windowsVH = window.innerHeight / 100;
				console.log(windowsVH)
				document.querySelector('html').style.setProperty('--vh', windowsVH + 'px');
				document.querySelector('body').style.setProperty('--vh', windowsVH + 'px');
				document.querySelector('#root').style.setProperty('--vh', windowsVH + 'px');
			});
		}

		
		
		// 網頁高度偵測
		safariHacks();</script></body></html>