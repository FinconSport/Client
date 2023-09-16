<!DOCTYPE html>
<html lang="zn-tw">
  <head>
    <!--  Fonts and icons  -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200|Open+Sans+Condensed:700" rel="stylesheet">
    <!-- Jquery -->
    <link href="{{ asset('css/jquery-ui.css?v=' . $system_config['version']) }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui.min.css?v=' . $system_config['version']) }}" rel="stylesheet">
    <!-- COMM CSS Files -->
    <link href="{{ asset('css/bootstrap.min.css?v=' . $system_config['version']) }}" rel="stylesheet">
  </head>
  <body>
    <div id="websocketDemo">
        <h4>websocket demo</h5>
        <label for="packageSpeed">封包頻率 (每秒)</label>
        <select id="packageSpeed" name="speedP" onchange="loca()" >
            <option value=1000 selected>1次</option>
            <option value=100>10次</option>
            <option value=10>100次</option>
            <option value=5>200次</option>
            <option value=4>250次</option>
            <option value=1>1000次</option>
        </select>
        <button id="registerBtn" type="button" class="btn btn-primary" disabled>連接中</button>
        <br>
        <br>
        <label for="viewSpeed">畫面更新頻率 : </label>
        <select id="viewSpeed" name="speedV" onchange="viewTimer()" >
            <option value=1 selected>每1秒更新一次</option>
            <option value=2>每2秒更新一次</option>
            <option value=3>每3秒更新一次</option>
            <option value=999>按照封包頻率</option>
        </select>
        <br>
        <br>
        <h5>目前最新time: <span id="timeStamp"></span> </h5>
        <h5>目前Queue筆數: <span id="queueCount"></span> </h5>
    </div>

    <style>	
        #websocketDemo {
            width: 70%;
            margin: auto;
            padding: 2rem;
        }
    </style>
        
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
	<script>
        const messageQueue = []; // queue to store the package (FIFO)
        var renderInter = null // timer for refresh view layer
        var latestTimeStamp = 0; // data layer
        var socket_status = false;
        var ws = null
        var queue_count = 0; // count of packages been processd in set time unit

        $(document).ready(function() {
            WebSocketDemo(); // ws connection
            setInterval(reconnent, 5000); // detect ws connetion state
            waitConnection(); // wait for ws connect
            processMessageQueueAsync(); // detect if there's pkg in messageQueue

            // get speed value
            var queryString = window.location.search;
		    var urlParams = new URLSearchParams(queryString);
            var speed = urlParams.get("speed");
            if (speed) $('#packageSpeed').val(parseInt(urlParams.get('speed')))
            
            viewTimer() // refresh view layer by set time unit
        });

        // set speed to refresh view layer
        function viewTimer() {
            clearInterval(renderInter)
            let vSpeed = parseInt($('#viewSpeed').val())
            vSpeed !== 999 ? vSpeed *= 1000 : vSpeed = parseInt($('#packageSpeed').val())
            renderInter = setInterval(() => {
                $('#timeStamp').html(latestTimeStamp);
                $('#queueCount').html(queue_count);
                queue_count = 0;
            }, vSpeed );
        }

        // main ws function
        function WebSocketDemo() {
            console.log('WebSocketDemo')
            if ("WebSocket" in window) {
                try {
                    ws = new WebSocket('wss://soccer.asgame.net/ws'); 
                    ws.onopen = function() {
                        socket_status = true; // for reconnection
                    };
                    // websocket is closed
                    ws.onclose = function(event) {
                        console.log('Connection closed with code: ', event.code);
                        console.log('Connection closed with reason: ', event.reason);
                        socket_status = false;
                    };
                    // websocket is getting message
                    ws.onmessage = function(message) {
                        messageQueue.push(message); // push package to messageQueue
                    }
                } catch (error) {
                    console.error(langTrans.js.websocket_connect_err, error);
                }
            } else {
                console.log("WebSocket NOT supported by your Browser!");
            }
        }

        // ws register
        $('#registerBtn').click(function(){
            $(this).attr('disabled', true) // register btn
            let speed = parseInt($('#packageSpeed').val()) // pkg sent speed
            let registerData = {
                "action": "register",
                "interval": speed
            }
            ws.send(JSON.stringify(registerData)); // send the register msg
        })

        // reconnection
        function reconnent() {
            if (socket_status === false) {
                WebSocketDemo();
            } 
        }

        // wait for connection of the ws then register
        async function waitConnection() {
            while( true ) {
                if(ws.readyState === 1) {
                    $('#registerBtn').removeAttr('disabled')
                    $('#registerBtn').html('註冊')
                    break;
                } else {
                    await sleep(2); // check after 2 ms
                }
            }
        }

        // detect if there's still package need to be processed
        async function processMessageQueueAsync() {
            while (true) {
                if (messageQueue.length > 0) {
                    processMessageQueue(); // package process function
                } else {
                    await sleep(2); // check after 2 ms
                }
            }
        }

        // sleep function to pause
        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        // package process function
        function processMessageQueue() {
            const message = messageQueue.shift(); // to get the head pkg
            const msg = JSON.parse(message.data); // convert to json
            if(msg.time > latestTimeStamp) latestTimeStamp = msg.time // change the global latestTimeStamp ( data layer )
            queue_count++; // count how many pkg been processed
        }

        // set speed
        function loca() {
            const params = new URLSearchParams();
            let speed = parseInt($('#packageSpeed').val())
            params.append("speed", speed);
            const queryString = params.toString();
            const url = `/match_list/index?${queryString}`;
            window.location.href = url;
        }
    </script>
  </body>
</html>
