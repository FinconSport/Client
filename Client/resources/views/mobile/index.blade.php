<!doctype html><html lang="en"><head><meta charset="utf-8"/><meta name="google" content="notranslate"><link rel="icon" href="/favicon.ico"/><meta name="theme-color" content="#000000"/><meta name="description" content="Web site created using create-react-app"/><link rel="apple-touch-icon" href="/logo192.png"/><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><link rel="manifest" href="/manifest.json"/><title>React App</title><style>#root,body,html{height:100vh;height:calc(var(--vh,1vh) * 100);overflow:hidden}</style><script defer="defer" src="/static/js/main.5f5b2443.js"></script><link href="/static/css/main.77eca244.css" rel="stylesheet"></head><body style="overflow:hidden"><noscript>You need to enable JavaScript to run this app.</noscript><div id="root" style="overflow:hidden"></div><script>// var player = 2 ;
		// var token = '827ccb0eea8a706c4c34a16891f84e7b' ;
		// var lang = 'tw'

		var player = @json(session('player.id'));
		var token = @json(session('player.token'));
		var lang = @json(session('player.lang'));

		// 菜單與體育彩種預設
		var menu = null
		var sport = null

		// Ajax用
		var ajaxInt = null

		// websocket用
		const messageQueue = []; // queue to store the package (FIFO)
		var socket_status = false;
		var ws = null
		var heartbeatTimer = null
		var registerMatchList = []
		var wsInt = null
		var wsStatus = null

		// websocket
		function WebSocketDemo( ) {
			console.log('WebSocketDemo')
			if ("WebSocket" in window) {
				try {
					ws = new WebSocket('wss://broadcast.asgame.net/ws'); // 連線
					ws.onopen = function() {
						wsInt = setInterval(reconnent, 5000); // detect ws connetion state
						
						// register
						const wsMsg = {
							"action": "register",
							"sport_id": sport,
							"player": player,
						}
						console.log('ws match send -> ')
						console.log(wsMsg)
						ws.send(JSON.stringify(wsMsg));


						socket_status = true; // for reconnection
						heartbeatTimer = setInterval(() => { // 心跳 
							const heartbeat = {
								"action": "heartbeat",
							}
							console.log(heartbeat)
							ws.send(JSON.stringify(heartbeat));
						}, 10000);
					};

					// websocket is closed
					ws.onclose = function(event) {
						console.log('Connection closed with code: ', event.code);
						socket_status = false;
						clearInterval(heartbeatTimer) // 移除心跳timer
						clearInterval(wsInt)
					};

					// websocket is getting message
					ws.onmessage = function(message) {
						messageQueue.push(message); // push package to messageQueue
					}
				} catch (error) {
				}
			} else {
				console.log("WebSocket NOT supported by your Browser!");
			}
		}

		 
		// 重連機制
		function reconnent() {
			if (socket_status === false) {
				WebSocketDemo( window.sport );
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