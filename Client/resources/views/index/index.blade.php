@extends('layout.app')

@section('content')
<!-- 投注計算機 -->
<div id='mask' style="display: none;"></div>
<div id="leftSlideOrder" style="display: none;">
    <div class="row m-0">
        <div class="col-6">{{ trans('index.bet_area.hi') }} <span class="player"></span></div>
        <div class="col-6 text-right" onclick="refreshBalence()">
            <span class="text-orange balance">{{ $player['balance'] }}</span>
            <i id="refreshIcon" class="fa-solid fa-arrows-rotate ml-1"></i>
        </div>
        <div class="col-12">
            <p class="fs-4 mb-0 mb-2 mt-4" key='series'></p>
        </div>
        <div class="col-12">
            <p class="mb-3">
                <span key='home'></span>
                <span style="font-style:italic;">&ensp;VS&ensp;</span>
                <span key='away'></span>
            </p>
        </div>
        <div class="col-12">
            <div class="leftSlideOrderCard row m-0">
                <div class="col-12"><span key='rate_name'></span></div>
                <div class="col-8 mb-2 mt-2"><span key='bet_name'></span></div>
                <div class="col-4 mb-2 mt-2 text-right" key='oddContainer'>
                    <span key='odd' class="odd"></span>
                </div>
                <div class="col-12">
                    <input class="w-100 text-right" id="moneyInput" autocomplete="off" inputmode="numeric" oninput="this.value = this.value.replace(/\D+/g, '')" placeholder="{{ trans('index.bet_area.limit') }}0-10000" >
                </div>
                <div class="col-6 mb-2 mt-2">{{ trans('index.bet_area.maxwin') }}</div>
                <div class="col-6 mb-2 mt-2 text-right" id="maxWinning">0.00</div>
                <div class="col-12 m-0" id="quickContainer">
                    <div class="col-3">
                        <div class="quick" value=100>+100</div>
                    </div>
                    <div class="col-3">
                        <div class="quick" value=200>+200</div>
                    </div>
                    <div class="col-3">
                        <div class="quick" value=500>+500</div>
                    </div>
                    <div class="col-3">
                        <div class="quick" value=1000>+1000</div>
                    </div>
                </div>
            </div>
            <div class="w-100 mt-3">
                <input type="checkbox" name="better_rate" id="better_rate">
                <label for="better_rate">{{ trans('index.bet_area.better_rate') }}</label>
            </div>
            <button onclick="sendOrder()">{{ trans('index.bet_area.bet') }}</button>
            <button id="cancelOrder">{{ trans('index.bet_area.cancel') }}</button>
        </div>
    </div>
</div>
<div id='searchCondition'>
    {{ trans('common.search_area.search') }}
</div>
<div id="indexContainer">
    <div id="indexContainerLeft">
        
    </div>
</div>

<!-- early living toggle template -->
<div template='elToggleTemplate' hidden>
    <div class="catWrapperTitle">
        <span class="elToggleText"></span>
        (<span class="elToggleCount"></span>)
        <span class="elToggleDir" style="float: right;">
            <i class="fa-solid fa-chevron-down"></i> 
        </span>
    </div>
    <div class="catWrapperContent">
    </div>
</div>

<!-- league toggle template -->
<div template='leagueToggleTitleTemplate' class="seriesWrapperTitle" hidden>
    <span class="legToggleName"></span>
    (<span class="legToggleCount"></span>)
    <span class="legToggleDir" style="float: right;">
        <i class="fa-solid fa-circle-chevron-down"></i>
    </span>
</div>
<div template='leagueToggleContentTemplate' class="seriesWrapperContent" hidden>
</div>

<!-- fixture card template -->
<div template='fixtureCardTemplate' class="indexEachCard" hidden>
    <div class="indexBetCard">
        <div class="indexBetCardInfo">
            <div class="timeSpan">
                <span class="timer"></span>
            </div>
            <div key='homeTeamInfo' class="w-100" style="display: inline-flex;">
                <div class="textOverFlow teamSpan" style="width: 80%;">
                </div>
                <div class="scoreSpan" style="width: 20%;">
                </div>
            </div>
            <div key='awayTeamInfo' class="w-100" style="display: inline-flex;">
                <div class="textOverFlow teamSpan" style="width: 80%;">
                </div>
                <div class="scoreSpan" style="width: 20%;">
                </div>
            </div>
        </div>
        <div class="indexBetCardTable row m-0 text-center">
            
        </div>
    </div>
</div>

<!-- bet div template -->
<div class="col-2 p-0" template='betDiv' hidden>
    <div class="betLabel"></div>
    <div class="betItemDiv" index=0>
        <span class="rate_name"></span>&ensp;
        <span class="odd"></span>
        <i class="fa-solid fa-lock"></i>
    </div>
    <div class="betItemDiv" index=1>
        <span class="rate_name"></span>&ensp;
        <span class="odd"></span>
        <i class="fa-solid fa-lock"></i>
    </div>
</div>

<!-- no data -->
<div id="noData" style="display: none;">
    <i class="fa-solid fa-circle-exclamation"></i>
    <p class="mb-0">{{ trans('index.mainArea.nogame') }}</p>
</div>
@endsection



@section('styles')
<!-- <link href="{{ asset('css/index.css?v=' . $system_config['version']) }}" rel="stylesheet"> -->
<link href="{{ asset('css/index.css?v=' . $current_time) }}" rel="stylesheet">
<link href="{{ asset('css/index_ind.css?v=' . $current_time) }}" rel="stylesheet">
@endSection

@push('main_js')

<script>
    // 語系
    const langTrans = @json(trans('index'));

    // websocket用
    const messageQueue = []; // queue to store the package (FIFO)
    var renderInter = null // timer for refresh view layer
    var socket_status = false;
    var ws = null
    var heartbeatTimer = null

    /* ===== DATA LAYER ===== */
    /*  
        1. 現在大部份資料都api化，通過call ajax來loading
        2. 在所有所需的api被call完之前，要添加頁面loading樣式，等全部都call好了才顯示頁面

            有哪些api資料共用?
            1. account
            2. marquee
            3. sport_list
        
            index有那些需要call api?
            1. match_list
            2. bet limitation?

            有哪些需要沿用laravel映射?
            1. $player 
            2. $token (先寫死12345，之後正式再來換)
            3. $system_config['version']
            4. $current_time?
    
        3. 資料接收機制
            1. ws -> push to queue -> update the globe data (先註解掉)
            2. ajax -> update the globe data
    */

    // detect ini ajax
    var isReadyIndexInt = null
    var isReadyIndex = false

    // match list data
    var matchListD = {}
    var oldMatchListD = {}
    var callMatchListData = { token: token, player: player, sport_id: sport }
    const matchList_api = 'https://sportc.asgame.net/api/v2/match_index'

    // bet limitation data
    var betLimitationD = {}
    var callLimitationData = {}
    const betLimitation_api = ''

    // game priority and gameTitle
    const priorityArr = langTrans['sportBetData'][sport]['priorityArr']
    const gameTitle = langTrans['sportBetData'][sport]['gameTitle']
    /* ===== DATA LAYER ===== */
    
    /* ===== VIEW LAYER ===== */
    function viewIni() { // view ini

        // put the view ini function here  
        // ex: matchListD html element appedning, textoverflow handle, open the first toggle....

        // loop matchListD to generate html element here

        Object.entries(matchListD.data).map(([k, v]) => {  // living early toggle
            let el_toggle = $('div[template="elToggleTemplate"]').clone()
            let el_toggle_title = el_toggle.find('.catWrapperTitle')
            let el_toggle_text = el_toggle.find('.elToggleText')
            let el_toggle_count = el_toggle.find('.elToggleCount')
            let el_toggle_dir = el_toggle.find('.elToggleDir')
            let el_toggle_content = el_toggle.find('.catWrapperContent')

            el_toggle.attr('id', 'toggleContent_' + k)
            el_toggle_title.attr('id', `catWrapperTitle_${k}`)
            el_toggle_title.attr('onclick', `toggleCat('${k}')`)
            el_toggle_text.html(k === 'early' ? '{{ trans("index.mainArea.early") }}' : '{{ trans("index.mainArea.living") }}');
            el_toggle_count.attr('id', `catWrapperContent_${k}_total`)
            el_toggle_dir.attr('id', `catWrapperTitle_${k}_dir`)
            el_toggle_content.attr('id', `catWrapperContent_${k}`)
            
            el_toggle.removeAttr('hidden')
            el_toggle.removeAttr('template')

            Object.entries(v[sport].list).map(([k2, v2]) => { // league toggle
                let league_toggle = $('div[template="leagueToggleTitleTemplate"]').clone()
                let league_toggle_name = league_toggle.find('.legToggleName')
                let league_toggle_count = league_toggle.find('.legToggleCount')
                let league_toggle_dir = league_toggle.find('.legToggleDir')

                league_toggle.attr('id', `seriesWrapperTitle_${k}_${k2}`)
                league_toggle.attr('onclick', `toggleSeries('${k}_${k2}')`)
                league_toggle.attr('league_id', v2.league_id)
                league_toggle_name.html(v2.league_name)
                league_toggle_count.attr('id', `seriesWrapperTitle_${k}_${k2}_count`)
                league_toggle_dir.attr('id', `seriesWrapperTitle_${k}_${k2}_dir`)

                league_toggle.removeAttr('hidden')
                league_toggle.removeAttr('template')

                let league_toggle_content = $('div[template="leagueToggleContentTemplate"]').clone()
                league_toggle_content.attr('id', `seriesWrapperContent_${k}_${k2}`)

                Object.entries(v2.list).map(([k3, v3]) => {  // fixture card
                    let card = $('div[template="fixtureCardTemplate"]').clone()
                    let time = card.find('.timer');
                    let home_team_info = card.find('[key="homeTeamInfo"]');
                    let away_team_info = card.find('[key="awayTeamInfo"]')

                    card.attr('id', k3)
                    time.html(v3.start_time)
                    home_team_info.find('.teamSpan').html(v3.home_team_name)
                    home_team_info.find('.scoreSpan').html()
                    away_team_info.find('.teamSpan').html(v3.away_team_name)
                    away_team_info.find('.scoreSpan').html()

                    // bet area
                    priorityArr.forEach(( i, j ) => {
                        let bet_div = $('div[template="betDiv"]').clone()
                        let betData = Object.values(v3.list).find(m => m.priority === i)
                        bet_label = bet_div.find('.betLabel')
                        bet_label.html(gameTitle[j])

                        let firstDiv = bet_div.find('div[index=0]')
                        let secondDiv = bet_div.find('div[index=1]')
                        let item = null
                        if( betData && Object.keys(betData.list).length > 0 ) {
                            Object.entries(betData.list).map(([k4, v4], s) => { 
                                item = bet_div.find('.betItemDiv').eq(s)
                                // set attribute
                                item.attr('priority', i)
                                item.attr('fixture_id', k3)
                                item.attr('market_id', betData.market_id)
                                item.attr('market_bet_id', v4.market_bet_id)
                                item.attr('bet_rate', v4.price)
                                item.attr('bet_type', betData.market_name)
                                item.attr('bet_name', v4.market_bet_name + ' ' + v4.line)
                                item.attr('league', v2.league_name)
                                item.attr('home', v3.home_team_name)
                                item.attr('away', v3.away_team_name)
                                
                                item.find('.rate_name').html(v4.market_bet_name + ' ' + v4.line)
                                item.find('.odd').html(v4.price)

                                if( v4.status === 1 ) {
                                    item.find('.rate_name').show()
                                    item.find('.odd').show()
                                    item.find('i').hide()
                                    item.attr('onclick', 'openCal($(this))')
                                } else {
                                    item.find('.rate_name').hide()
                                    item.find('.odd').hide()
                                    item.find('i').show()
                                    item.removeAttr('onclick')
                                }
                                
                            })
                        } else {
                            firstDiv.find('.rate_name').hide()
                            firstDiv.find('.odd').hide()
                            firstDiv.find('i').show()
                            firstDiv.removeAttr('onclick')
                            secondDiv.find('.rate_name').hide()
                            secondDiv.find('.odd').hide()
                            secondDiv.find('i').show()
                            secondDiv.removeAttr('onclick')
                        }

                        bet_div.removeAttr('hidden')
                        bet_div.removeAttr('template')
                        card.find('.indexBetCardTable').append(bet_div)
                    });

                    card.removeAttr('hidden')
                    card.removeAttr('template')
                    league_toggle_content.append(card)
                })

                league_toggle_content.removeAttr('hidden')
                league_toggle_content.removeAttr('template')

                el_toggle_content.append(league_toggle)
                el_toggle_content.append(league_toggle_content)
            })

            el_toggle.removeAttr('hidden')
            el_toggle.removeAttr('template')

            $('#indexContainerLeft').append(el_toggle)
        })

        statistics()

        // loop matchListD to generate html element here
    }
    /* ===== VIEW LAYER ===== */

   


    $(document).ready(function() {
        // ===== DATA LATER =====

        // ini data from ajax
        caller(matchList_api, callMatchListData, matchListD) // match_list
        // then call every 5 sec
        setInterval(() => {
            caller(matchList_api, callMatchListData, matchListD, 1) // update 
        }, 5000);

        // check if api are all loaded every 500 ms 
        isReadyIndexInt = setInterval(() => {
            if (matchListD.status === 1) { isReadyIndex = true; }
            if( isReadyIndex && isReadyCommon) {
                oldMatchListD = matchListD // record
                $('#dimmer').dimmer('hide'); // hide loading
                $('#wrap').css('opacity', 1); // show the main content
                viewIni(); // ini data
                renderInter = setInterval(() => { // then refresh every 5 sec
                    renderView()
                }, 5000);
                clearInterval(isReadyIndexInt); // stop checking
            }
        }, 500);

        // websocket -> mark now
        // WebSocketDemo(); // ws connection
        // setInterval(reconnent, 5000); // detect ws connetion state
        // processMessageQueueAsync(); // detect if there's pkg in messageQueue
        // ===== DATA LATER =====
    });

    // websocket
    function WebSocketDemo() {
        console.log('WebSocketDemo')
        if ("WebSocket" in window) {
            try {
                let ws_url = langTrans['sportBetData'][sport]['ws']

                ws = new WebSocket(ws_url); // websocket 連線
                ws.onopen = function() {
                    wsRegisterMatch() // 註冊id
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

    // 重連機制
    function reconnent() {
        if (socket_status === false) {
            WebSocketDemo();
        }
    }

    // render view layer here
    function renderView() {
        Object.entries(matchListD.data).map(([k, v]) => {  // living early toggle
            Object.entries(v[sport].list).map(([k2, v2]) => { // league toggle
                Object.entries(v2.list).map(([k3, v3]) => {  // fixture card
                    let isExist = $(`#${k3}`).length > 0 ? true : false
                    if( isExist ) {
                        priorityArr.forEach(( i, j ) => {
                            let bet_div = $(`#${k3}`)
                            let firstDiv = bet_div.find('div[index=0]')
                            let secondDiv = bet_div.find('div[index=1]')
                            let item = null
                            if( betData && Object.keys(betData.list).length > 0 ) {
                                Object.entries(betData.list).map(([k4, v4], s) => { 
                                    item = bet_div.find('.betItemDiv').eq(s)
                                    // old attribute
                                    let market_bet_id = item.attr('market_bet_id')
                                    let price = item.attr('price')

                                    console.log(item.attr('home') + ' VS ' + item.attr('away'))
                                    // 判斷盤口是否有改變
                                    if( market_bet_id !== v4.market_bet_id ) {
                                        console.log('盤口::' + market_bet_id + ' ->' + v4.market_bet_id)
                                        // set attribute
                                        item.attr('market_bet_id', v4.market_bet_id)
                                        item.attr('bet_name', v4.market_bet_name + ' ' + v4.line)
                                    } else {
                                        // 判斷賠率是否有改變
                                        if( parseFloat(price) > parseFloat(v4.price) ) {
                                            console.log('賠率::' + price + ' ->' + v4.price)
                                            // 賠率下降
                                            lowerOdd(k3, betData.market_id, v4.market_bet_id)
                                        }
                                        if( parseFloat(price) < parseFloat(v4.price) ) {
                                            console.log('賠率::' + price + ' ->' + v4.price)
                                            // 賠率上升
                                            raiseOdd(k3, betData.market_id, v4.market_bet_id)
                                        }
                                        item.attr('bet_rate', v4.price)
                                    }

                                    // 顯示
                                    if( v4.status === 1 ) {
                                        item.find('.rate_name').show()
                                        item.find('.odd').show()
                                        item.find('i').hide()
                                        item.attr('onclick', 'openCal($(this))')
                                    } else {
                                        item.find('.rate_name').hide()
                                        item.find('.odd').hide()
                                        item.find('i').show()
                                        item.removeAttr('onclick')
                                    }
                                })
                            } else {
                                firstDiv.find('.rate_name').hide()
                                firstDiv.find('.odd').hide()
                                firstDiv.find('i').show()
                                firstDiv.removeAttr('onclick')

                                secondDiv.find('.rate_name').hide()
                                secondDiv.find('.odd').hide()
                                secondDiv.find('i').show()
                                secondDiv.removeAttr('onclick')
                            }
                        });
                    } else {
                        // 新的賽事
                    }
                })
            })
        })

        statistics()

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

        // 更新matchListD




        // 更新matchListD
    }

    // 註冊賽事id
    function wsRegisterMatch() {
        // 要註冊給ws的id陣列 ( 聯賽id )
        var registerId = $('.seriesWrapperTitle').map(function() {
            return parseInt($(this).attr('series_id'));
        }).get();

        const wsMsg = {
            "action": "register",
            "channel": 'match',
            "player": player,
            "game_id": parseInt(searchData.sport),
            "series": registerId // 要註冊的賽事
        }
        console.log('ws match send -> ')
        console.log(wsMsg)
        ws.send(JSON.stringify(wsMsg));
    }

    // 大分類收合
    function toggleCat( key ) {
        $('#catWrapperContent_' + key).slideToggle( "slow" );
        if($('#catWrapperTitle_' + key + '_dir i').hasClass('fa-chevron-down')) {
            $('#catWrapperTitle_' + key + '_dir i').removeClass('fa-chevron-down')
            $('#catWrapperTitle_' + key + '_dir i').addClass('fa-chevron-right')
        } else {
            $('#catWrapperTitle_' + key + '_dir i').addClass('fa-chevron-down')
            $('#catWrapperTitle_' + key + '_dir i').removeClass('fa-chevron-right')
        }
    }


    // 聯賽分類收合
    function toggleSeries( key ) {
        $('#seriesWrapperContent_' + key).slideToggle( "slow" );
        if($('#seriesWrapperTitle_' + key + '_dir i').hasClass('fa-circle-chevron-right')) {
            $('#seriesWrapperTitle_' + key + '_dir i').removeClass('fa-circle-chevron-right')
            $('#seriesWrapperTitle_' + key + '_dir i').addClass('fa-circle-chevron-down')
        } else {
            $('#seriesWrapperTitle_' + key + '_dir i').addClass('fa-circle-chevron-right')
            $('#seriesWrapperTitle_' + key + '_dir i').removeClass('fa-circle-chevron-down')
        }
    }


    // 內容太長 跑馬燈
    function fixTextOverflow() {
        $('.textOverFlow').each(function(){
            if ($(this).prop('scrollHeight') > $(this).height()) {
                $(this).removeClass('textOverFlow')
                $(this).wrap('<marquee behavior="scroll"><p></p></marquee>');
            }
        })
    }

    // 賠率上升
    function raiseOdd(fixture_id, market_id, market_bet_id) {
        console.log('raiseOdd')
        // 先移除現有樣式
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').removeClass('raiseOdd')
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-up').remove()
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').removeClass('lowerOdd')
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-down').remove()

        // 再加上賠率變化樣式
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').addClass('raiseOdd')
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .odd').after('<i class="fa-solid fa-caret-up"></i>')

        // 三秒後移除
        setTimeout(() => {
            $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').removeClass('raiseOdd')
            $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-up').remove()
        }, 3000);
    }
    // 賠率下降
    function lowerOdd(fixture_id, market_id, market_bet_id) {
        console.log('lowerOdd')
        // 先移除現有樣式
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').removeClass('raiseOdd')
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-up').remove()
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').removeClass('lowerOdd')
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-down').remove()

        // 再加上賠率變化樣式
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').addClass('lowerOdd')
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .odd').after('<i class="fa-sharp fa-solid fa-caret-down"></i>')

        // 三秒後移除
        setTimeout(() => {
            $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').removeClass('lowerOdd')
            $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-down').remove()
        }, 3000);
    }

    // 文字太常處理 參考
    function marqueeLongText(){
        $("td.home-name, td.away-name").each(function() {
            const $td = $(this);
            const $text = $td.find(".text");
            const $img = $td.find("img");
            if ($td.width() < ($text.width()+ parseInt($(this).css("padding-left")) + $img.width())) {
                $text.find("div").addClass("marquee");
                $text.css("overflow", "hidden");
            }
            else{
                $text.find("div").removeClass("marquee");
                $text.css("overflow", "auto");
            }
        });
    }


    // 打開投注計算機
    var sendOrderData = {}
    function openCal(e) {
        let fixture_id = e.attr('fixture_id')
        let market_id = e.attr('market_id')
        let market_bet_id = e.attr('market_bet_id')
        let bet_rate = e.attr('bet_rate')
        let bet_type = e.attr('bet_type')
        let bet_name = e.attr('bet_name')
        let league = e.attr('league')
        let home = e.attr('home')
        let away = e.attr('away')


        sendOrderData = {
            token: token,
            player: player,
            sport_id: sport,
            fixture_id: fixture_id,
            market_id: market_id,
            market_bet_id: market_bet_id,
            bet_rate: bet_rate,
            better_rate: 0,
        }

        $('#leftSlideOrder span[key="rate_name"]').html(bet_type)
        $('#leftSlideOrder span[key="bet_name"]').html(bet_name)
        $('#leftSlideOrder span[key="odd"]').html(bet_rate)
        $('#leftSlideOrder p[key="series"]').html(league)
        $('#leftSlideOrder span[key="home"]').html(home)
        $('#leftSlideOrder span[key="away"]').html(away)
        $('#leftSlideOrder div[key="oddContainer"]').attr('fixture_id', fixture_id)
        $('#leftSlideOrder div[key="oddContainer"]').attr('market_id', market_id)
        $('#leftSlideOrder div[key="oddContainer"]').attr('market_bet_id', market_bet_id)

        $('#leftSlideOrder').show("slide", {
            direction: "left"
        }, 500);
        $('#mask').fadeIn()

        // 選中樣式
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').addClass('m_order_on')

        // 判斷滾球or早盤
        // const start_time = new Date(result.start_time).getTime();
        // const now = new Date().getTime();
        // let placeholderStr = langTrans.js.limit

        // if (now > start_time) {
        //     // 滾球
        //     min = parseInt(limit.living[sport].min)
        //     max = parseInt(limit.living[sport].max)
        // } else {
        //     // 早盤
        //     min = parseInt(limit.early[sport].min)
        //     max = parseInt(limit.early[sport].max)
        // }
        // placeholderStr += min
        // placeholderStr += '-'
        // placeholderStr += max
        // $('#moneyInput').attr('placeholder', placeholderStr)
        // $('#moneyInput').val(min)
        // $('#moneyInput').trigger('change')
        // $('#moneyInput').focus()
    }

    // 關閉左邊投注區塊
    $('#mask, #cancelOrder').click(function() {
        closeCal()
    })

    function closeCal() {
        $('#leftSlideOrder').hide("slide", {
            direction: "left"
        }, 500);
        $('#mask').fadeOut()
        $('#moneyInput').val('')
        $('#moneyInput').trigger('change')
        // 移除所有選中樣式
        $('div').removeClass('m_order_on')
    }

    // 金額快速鍵
    $('#leftSlideOrder .quick').click(function() {
        let inputMoney = parseInt($('#moneyInput').val())
        if (isNaN(inputMoney)) inputMoney = 0
        inputMoney += parseInt($(this).attr('value'))
        $('#moneyInput').val(inputMoney)
        $('#moneyInput').trigger('change')
    })

    // 最高可贏
    $('#moneyInput').on('keyup input change', function(event) {
        let inputMoney = parseInt($(this).val())
        if (isNaN(inputMoney)) inputMoney = 0
        // if (inputMoney < min) inputMoney = min
        // if (inputMoney > max) inputMoney = max
        let odd = parseFloat($('span[key="odd"]').html())
        let maxMoney = (inputMoney * odd).toFixed(2);
        $('#maxWinning').html(maxMoney)
        $(this).val(inputMoney)
        sendOrderData.bet_amount = inputMoney
    })

    // 最佳賠率
    $('#better_rate').on('change', function() {
        let is_better_rate = $(this).is(':checked')
        let bool = is_better_rate === true ? 1 : 0
        sendOrderData.better_rate = bool
    })

    // 投注
    function sendOrder() {
        if (sendOrderData.bet_amount === 0 || sendOrderData.bet_amount === undefined) {
            showErrorToast(langTrans.js.no_bet_amout);
            return;
        }
        // if (sendOrderData.bet_amount < min) {
        //     showErrorToast(langTrans.js.tooless_bet_amout + min);
        //     return;
        // }
        // if (sendOrderData.bet_amount > max) {
        //     showErrorToast(langTrans.js.toohigh_bet_amout + max);
        //     return;
        // }

        $.ajax({
            url: 'https://sportc.asgame.net/api/v2/game_bet',
            method: 'POST',
            data: sendOrderData,
            success: function(response) {
                let res = JSON.parse(response)
                console.log(res)
                if (res.message === 'SUCCESS_API_GAME_BET_01') {
                    // 餘額更新
                    refreshBalence()
                    showSuccessToast(res.message)
                } else {
                    showErrorToast(res.message)
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('error');
                showErrorToast(jqXHR)
            }
        });
       
        // 金額歸零
        $('#moneyInput').val('')
        $('#moneyInput').trigger('change')
        // 隱藏計算機
        closeCal()
    }

    // 統計
    function statistics() {
        $('#indexContainer .elToggleCount').each(function() {
            let id = $(this).attr('id').replace('_total', '')
            let count = $('#' + id).find('.indexEachCard').length
            $(this).html(count)
            if( count === 0 ) $(this).closest('div[id^="toggleContent"]').hide()
        })

        $('#indexContainer .legToggleCount').each(function() {
            let idArr = $(this).attr('id').split('_')
            let id = `seriesWrapperContent_${idArr[1]}_${idArr[2]}` 
            let count = $('#' + id).find('.indexEachCard').length
            $(this).html(count)
        })
    }

    // 餘額
    function refreshBalence() {
        $('#refreshIcon').addClass('rotate-animation')
        caller(account_api, commonCallData, accountD)
        setTimeout(() => {
            $('.balance').html(accountD.data.balance)
            $('#refreshIcon').removeClass('rotate-animation')
        }, 1000);
    }
</script>
@endpush