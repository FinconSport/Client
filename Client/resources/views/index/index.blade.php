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
    <div class="betItemDiv" index=2>
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
<link href="{{ asset('css/index.css?v=' . $system_config['version']) }}" rel="stylesheet">
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

    var isReadySportInt = null

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
    var priorityArr = null
    var gameTitle = null

    
    /* ===== DATA LAYER ===== */
    
    /* ===== VIEW LAYER ===== */
    function viewIni() { // view ini

        // put the view ini function here  
        // ex: matchListD html element appedning, textoverflow handle, open the first toggle....

        // loop matchListD to generate html element here
        Object.entries(matchListD.data).map(([k, v]) => {  // living early toggle
            createCate(k, v)
            Object.entries(v[sport].list).map(([k2, v2]) => { // league toggle
                createLeague(k, k2, v2)
                Object.entries(v2.list).map(([k3, v3]) => {  // fixture card
                    createFixtureCard(k, v2.league_id, v2.league_name, k3, v3)
                })
            })
        })

        // 滾球移到最上面
        let parentNode = $('#indexContainerLeft')
        let livingNode = $('#toggleContent_living')
        livingNode.prependTo(parentNode);

        // 統計
        statistics()
        // loop matchListD to generate html element here
    }
    /* ===== VIEW LAYER ===== */


    function createCate(k, v) {
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

        $('#indexContainerLeft').append(el_toggle)
    }

    function createLeague(k, k2, v2) {
        let league_toggle = $('div[template="leagueToggleTitleTemplate"]').clone()
        let league_toggle_name = league_toggle.find('.legToggleName')
        let league_toggle_count = league_toggle.find('.legToggleCount')
        let league_toggle_dir = league_toggle.find('.legToggleDir')

        league_toggle.attr('id', `seriesWrapperTitle_${k}_${v2.league_id}`)
        league_toggle.attr('onclick', `toggleSeries('${k}_${v2.league_id}')`)
        league_toggle.attr('league_id', v2.league_id)
        league_toggle_name.html(v2.league_name)
        league_toggle_count.attr('id', `seriesWrapperTitle_${k}_${v2.league_id}_count`)
        league_toggle_dir.attr('id', `seriesWrapperTitle_${k}_${v2.league_id}_dir`)

        league_toggle.removeAttr('hidden')
        league_toggle.removeAttr('template')

        let league_toggle_content = $('div[template="leagueToggleContentTemplate"]').clone()
        league_toggle_content.attr('id', `seriesWrapperContent_${k}_${v2.league_id}`)

        league_toggle_content.removeAttr('hidden')
        league_toggle_content.removeAttr('template')

        let el_toggle_content = $(`#catWrapperContent_${k}`)
        el_toggle_content.append(league_toggle)
        el_toggle_content.append(league_toggle_content)
    }

    function createFixtureCard(k, league_id, league_name, k3, v3) {
        let card = $('div[template="fixtureCardTemplate"]').clone()
        let time = card.find('.timer');
        let home_team_info = card.find('[key="homeTeamInfo"]')
        let away_team_info = card.find('[key="awayTeamInfo"]')

        card.attr('id', k3)
        card.attr('cate', k)
        card.attr('status', v3.status)
        card.attr('league_id', league_id)
        time.html(v3.start_time)
        home_team_info.find('.teamSpan').html(v3.home_team_name)
        home_team_info.find('.scoreSpan').html()
        away_team_info.find('.teamSpan').html(v3.away_team_name)
        away_team_info.find('.scoreSpan').html()

        // living score
        if( v3.status === 2 ) {
            home_team_info.find('.scoreSpan').html( v3.scoreboard[1][0] )
            away_team_info.find('.scoreSpan').html( v3.scoreboard[2][0] )
            let timerStr = v3.periods.period + langTrans.mainArea.stage
            v3.periods.Turn === '1' ? timerStr += langTrans.mainArea.lowerStage : timerStr += langTrans.mainArea.upperStage
            time.html(timerStr)
        }
        // ready to start
        if( v3.status === 9 ) {
            time.html(langTrans.mainArea.readyToStart)
        }

        // bet area
        priorityArr.forEach(( i, j ) => {
            let bet_div = $('div[template="betDiv"]').clone()
            let betData = Object.values(v3.list).find(m => m.priority === i)
            bet_div.attr('priority', i)
            bet_label = bet_div.find('.betLabel')
            bet_label.html(gameTitle[j])

            let firstDiv = bet_div.find('div[index=0]')
            let secondDiv = bet_div.find('div[index=1]')
            let thirdDiv = bet_div.find('div[index=2]')
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
                    item.attr('league', league_name)
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
                if( thirdDiv ) {
                    thirdDiv.find('.rate_name').hide()
                    thirdDiv.find('.odd').hide()
                    thirdDiv.find('i').show()
                    thirdDiv.removeAttr('onclick')
                }
            }

            bet_div.removeAttr('hidden')
            bet_div.removeAttr('template')
            card.find('.indexBetCardTable').append(bet_div)
        });

        card.removeAttr('hidden')
        card.removeAttr('template')
        let league_toggle_content = $(`#seriesWrapperContent_${k}_${league_id}`)
        league_toggle_content.append(card)
    }
   
    $(document).ready(function() {
        // ===== DATA LATER =====

        // detest is sport List is ready
        isReadySportInt = setInterval(() => {
            if( isReadyCommon ) {
                callMatchListData.sport_id = sport // default sport
                clearInterval(isReadySportInt)
                caller(matchList_api, callMatchListData, matchListD) // match_list
                setInterval(() => {
                    caller(matchList_api, callMatchListData, matchListD, 1) // update 
                }, 5000);
            }
        }, 100);


        // check if api are all loaded every 500 ms 
        isReadyIndexInt = setInterval(() => {
            if (matchListD.status === 1) { isReadyIndex = true; }
            if( isReadyIndex && isReadyCommon) {
                // game priority and gameTitle
                priorityArr = langTrans['sportBetData'][sport]['priorityArr']
                gameTitle = langTrans['sportBetData'][sport]['gameTitle']

                // soccer has three bet div others only two
                if( sport !== 6046 ) $('div[template="betDiv"] div[index=2]').remove()

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

        // fake data test
        matchListD = {
            "status": 1,
            "data": {
                "early": {
                    "154914": {
                        "sport_id": "154914",
                        "sport_name": "棒球",
                        "list": {
                            "5540": {
                                "league_id": 5540,
                                "league_name": "中華職棒",
                                "list": {
                                    "11405499": {
                                        "fixture_id": 11405499,
                                        "start_time": "2023-09-28 18:35:00",
                                        "order_by": 1695897300,
                                        "status": 1,
                                        "last_update": "2023-09-21 08:02:50",
                                        "home_team_id": 52398469,
                                        "home_team_name": "樂天猿",
                                        "away_team_id": 327587,
                                        "away_team_name": "統一獅",
                                        "periods": null,
                                        "scoreboard": null,
                                        "market_bet_count": 6,
                                        "list": {
                                            "28": {
                                                "market_id": 28,
                                                "market_name": "全場大小",
                                                "priority": 5,
                                                "main_line": "8.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 135975060711405500,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "8.5",
                                                        "price": "1.74",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 16:21:09"
                                                    },
                                                    {
                                                        "market_bet_id": 202554746111405500,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "8.5",
                                                        "price": "2.05",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 16:21:09"
                                                    }
                                                ]
                                            },
                                            "226": {
                                                "market_id": 226,
                                                "market_name": "全場獨贏",
                                                "priority": 1,
                                                "main_line": "",
                                                "list": [
                                                    {
                                                        "market_bet_id": 142542734111405500,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "",
                                                        "price": "1.47",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 15:49:38"
                                                    },
                                                    {
                                                        "market_bet_id": 182871186811405500,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "",
                                                        "price": "2.7",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 15:49:38"
                                                    }
                                                ]
                                            },
                                            "342": {
                                                "market_id": 342,
                                                "market_name": "全場讓分",
                                                "priority": 3,
                                                "main_line": "-1.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 165414276711405500,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "-1.5",
                                                        "price": "1.86",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 16:58:09"
                                                    },
                                                    {
                                                        "market_bet_id": 24882624311405500,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "1.5",
                                                        "price": "1.86",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 16:58:09"
                                                    }
                                                ]
                                            }
                                        }
                                    },
                                    "11405508": {
                                        "fixture_id": 11405508,
                                        "start_time": "2023-09-28 18:35:00",
                                        "order_by": 1695897300,
                                        "status": 1,
                                        "last_update": "2023-09-21 08:02:52",
                                        "home_team_id": 52036807,
                                        "home_team_name": "中信兄弟",
                                        "away_team_id": 52398924,
                                        "away_team_name": "味全龍",
                                        "periods": null,
                                        "scoreboard": null,
                                        "market_bet_count": 6,
                                        "list": {
                                            "28": {
                                                "market_id": 28,
                                                "market_name": "全場大小",
                                                "priority": 5,
                                                "main_line": "5.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 132951728711405500,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "5.5",
                                                        "price": "1.74",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:00:39"
                                                    },
                                                    {
                                                        "market_bet_id": 42600719111405500,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "5.5",
                                                        "price": "2.05",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:00:39"
                                                    }
                                                ]
                                            },
                                            "226": {
                                                "market_id": 226,
                                                "market_name": "全場獨贏",
                                                "priority": 1,
                                                "main_line": "",
                                                "list": [
                                                    {
                                                        "market_bet_id": 54726635611405500,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "",
                                                        "price": "1.71",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 15:58:08"
                                                    },
                                                    {
                                                        "market_bet_id": 95055088311405500,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "",
                                                        "price": "2.1",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 15:58:08"
                                                    }
                                                ]
                                            },
                                            "342": {
                                                "market_id": 342,
                                                "market_name": "全場讓分",
                                                "priority": 3,
                                                "main_line": "-1.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 133407224811405500,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "-1.5",
                                                        "price": "2.9",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:00:39"
                                                    },
                                                    {
                                                        "market_bet_id": 64239727411405500,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "1.5",
                                                        "price": "1.42",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:00:39"
                                                    }
                                                ]
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                },
                "living": {
                    "154914": {
                        "sport_id": "154914",
                        "sport_name": "棒球",
                        "list": {
                            "4146": {
                                "league_id": 4146,
                                "league_name": "日本職業棒球",
                                "list": {
                                    "11447779": {
                                        "fixture_id": 11447779,
                                        "start_time": "2023-09-28 17:00:00",
                                        "order_by": 1695891600,
                                        "status": 2,
                                        "last_update": "2023-09-28 17:29:51",
                                        "home_team_id": 205806,
                                        "home_team_name": "日本火腿鬥士",
                                        "away_team_id": 205807,
                                        "away_team_name": "千葉羅德海洋",
                                        "periods": {
                                            "period": 2,
                                            "Turn": "2",
                                            "Strikes": "2",
                                            "Outs": "1",
                                            "Bases": "0/1/0",
                                            "Balls": "3"
                                        },
                                        "scoreboard": {
                                            "1": [
                                                1,
                                                1,
                                                0
                                            ],
                                            "2": [
                                                1,
                                                1,
                                                0
                                            ]
                                        },
                                        "market_bet_count": 10,
                                        "list": {
                                            "28": {
                                                "market_id": 28,
                                                "market_name": "全場大小",
                                                "priority": 5,
                                                "main_line": "7.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 154624433111447780,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "7.5",
                                                        "price": "1.77",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    },
                                                    {
                                                        "market_bet_id": 58196077111447780,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "7.5",
                                                        "price": "1.91",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    }
                                                ]
                                            },
                                            "226": {
                                                "market_id": 226,
                                                "market_name": "全場獨贏",
                                                "priority": 1,
                                                "main_line": "",
                                                "list": [
                                                    {
                                                        "market_bet_id": 131537925211447780,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "",
                                                        "price": "2.2",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    },
                                                    {
                                                        "market_bet_id": 91209472511447780,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "",
                                                        "price": "1.63",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    }
                                                ]
                                            },
                                            "236": {
                                                "market_id": 236,
                                                "market_name": "上半場大小",
                                                "priority": 6,
                                                "main_line": "4.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 15204692311447780,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "4.5",
                                                        "price": "1.71",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    },
                                                    {
                                                        "market_bet_id": 128592177711447780,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "4.5",
                                                        "price": "2",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    }
                                                ]
                                            },
                                            "281": {
                                                "market_id": 281,
                                                "market_name": "上半場讓分",
                                                "priority": 4,
                                                "main_line": "0.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 187886903811447780,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "0.5",
                                                        "price": "1.77",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    },
                                                    {
                                                        "market_bet_id": 156040799411447780,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "-0.5",
                                                        "price": "1.91",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    }
                                                ]
                                            },
                                            "342": {
                                                "market_id": 342,
                                                "market_name": "全場讓分",
                                                "priority": 3,
                                                "main_line": "0.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 106963186411447780,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "0.5",
                                                        "price": "2.1",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    },
                                                    {
                                                        "market_bet_id": 16648298411447780,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "-0.5",
                                                        "price": "1.67",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    }
                                                ]
                                            }
                                        }
                                    }
                                }
                            },
                            "7807": {
                                "league_id": 7807,
                                "league_name": "韓國職棒聯賽",
                                "list": {
                                    "11447719": {
                                        "fixture_id": 11447719,
                                        "start_time": "2023-09-28 16:00:00",
                                        "order_by": 1695888000,
                                        "status": 2,
                                        "last_update": "2023-09-28 17:29:23",
                                        "home_team_id": 299494,
                                        "home_team_name": "樂天巨人",
                                        "away_team_id": 299489,
                                        "away_team_name": "華老鷹",
                                        "periods": {
                                            "period": 6,
                                            "Balls": "2",
                                            "Outs": "2",
                                            "Bases": "1/1/0",
                                            "Strikes": "0",
                                            "Turn": "2"
                                        },
                                        "scoreboard": {
                                            "1": [
                                                2,
                                                0,
                                                0,
                                                0,
                                                2,
                                                0,
                                                0
                                            ],
                                            "2": [
                                                0,
                                                0,
                                                0,
                                                0,
                                                0,
                                                0,
                                                0
                                            ]
                                        },
                                        "market_bet_count": 10,
                                        "list": {
                                            "28": {
                                                "market_id": 28,
                                                "market_name": "全場大小",
                                                "priority": 5,
                                                "main_line": "5.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 186448931511447700,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "5.5",
                                                        "price": "2",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    },
                                                    {
                                                        "market_bet_id": 206172294511447700,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "5.5",
                                                        "price": "1.71",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    }
                                                ]
                                            },
                                            "226": {
                                                "market_id": 226,
                                                "market_name": "全場獨贏",
                                                "priority": 1,
                                                "main_line": "",
                                                "list": [
                                                    {
                                                        "market_bet_id": 132667217811447710,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "",
                                                        "price": "1.18",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    },
                                                    {
                                                        "market_bet_id": 92338765111447710,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "",
                                                        "price": "4.4",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    }
                                                ]
                                            },
                                            "236": {
                                                "market_id": 236,
                                                "market_name": "上半場大小",
                                                "priority": 6,
                                                "main_line": "0.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 203610815111447700,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "0.5",
                                                        "price": "1.56",
                                                        "status": 3,
                                                        "last_update": "2023-09-28 17:02:32"
                                                    },
                                                    {
                                                        "market_bet_id": 182310110911447700,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "0.5",
                                                        "price": "2.3",
                                                        "status": 3,
                                                        "last_update": "2023-09-28 17:02:32"
                                                    }
                                                ]
                                            },
                                            "281": {
                                                "market_id": 281,
                                                "market_name": "上半場讓分",
                                                "priority": 4,
                                                "main_line": "-0.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 189947826911447700,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "-0.5",
                                                        "price": "2.2",
                                                        "status": 3,
                                                        "last_update": "2023-09-28 17:17:40"
                                                    },
                                                    {
                                                        "market_bet_id": 4369878111447719,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "0.5",
                                                        "price": "1.63",
                                                        "status": 3,
                                                        "last_update": "2023-09-28 17:17:40"
                                                    }
                                                ]
                                            },
                                            "342": {
                                                "market_id": 342,
                                                "market_name": "全場讓分",
                                                "priority": 3,
                                                "main_line": "-1.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 175329073011447700,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "-1.5",
                                                        "price": "1.53",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    },
                                                    {
                                                        "market_bet_id": 191743813211447700,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "1.5",
                                                        "price": "2.4",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:29:51"
                                                    }
                                                ]
                                            }
                                        }
                                    },
                                    "11447720": {
                                        "fixture_id": 11447720,
                                        "start_time": "2023-09-28 16:00:00",
                                        "order_by": 1695888000,
                                        "status": 2,
                                        "last_update": "2023-09-28 17:30:03",
                                        "home_team_id": 299490,
                                        "home_team_name": "NC恐龍",
                                        "away_team_id": 299495,
                                        "away_team_name": "起亞老虎",
                                        "periods": {
                                            "period": 3,
                                            "Balls": "0",
                                            "Turn": "1",
                                            "Bases": "1/1/1",
                                            "Strikes": "0",
                                            "Outs": "0"
                                        },
                                        "scoreboard": {
                                            "1": [
                                                6,
                                                1,
                                                5,
                                                0
                                            ],
                                            "2": [
                                                3,
                                                1,
                                                0,
                                                2
                                            ]
                                        },
                                        "market_bet_count": 10,
                                        "list": {
                                            "28": {
                                                "market_id": 28,
                                                "market_name": "全場大小",
                                                "priority": 5,
                                                "main_line": "16.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 90321605511447710,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "16.5",
                                                        "price": "1.67",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:58"
                                                    },
                                                    {
                                                        "market_bet_id": 186423483311447700,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "16.5",
                                                        "price": "2.1",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:58"
                                                    }
                                                ]
                                            },
                                            "226": {
                                                "market_id": 226,
                                                "market_name": "全場獨贏",
                                                "priority": 1,
                                                "main_line": "",
                                                "list": [
                                                    {
                                                        "market_bet_id": 121121229011447710,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "",
                                                        "price": "1.05",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:58"
                                                    },
                                                    {
                                                        "market_bet_id": 35487165111447720,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "",
                                                        "price": "8.5",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:58"
                                                    }
                                                ]
                                            },
                                            "236": {
                                                "market_id": 236,
                                                "market_name": "上半場大小",
                                                "priority": 6,
                                                "main_line": "13.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 62309955511447720,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "13.5",
                                                        "price": "1.87",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:58"
                                                    },
                                                    {
                                                        "market_bet_id": 124428982311447710,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "13.5",
                                                        "price": "1.8",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:58"
                                                    }
                                                ]
                                            },
                                            "281": {
                                                "market_id": 281,
                                                "market_name": "上半場讓分",
                                                "priority": 4,
                                                "main_line": "-6.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 119818787711447710,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "-6.5",
                                                        "price": "2.7",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:58"
                                                    },
                                                    {
                                                        "market_bet_id": 118898343311447710,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "6.5",
                                                        "price": "1.48",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:58"
                                                    }
                                                ]
                                            },
                                            "342": {
                                                "market_id": 342,
                                                "market_name": "全場讓分",
                                                "priority": 3,
                                                "main_line": "-5.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 177612137011447700,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "-5.5",
                                                        "price": "1.91",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:58"
                                                    },
                                                    {
                                                        "market_bet_id": 50935621611447720,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "5.5",
                                                        "price": "1.77",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:29:58"
                                                    }
                                                ]
                                            }
                                        }
                                    },
                                    "11447721": {
                                        "fixture_id": 11447721,
                                        "start_time": "2023-09-28 16:00:00",
                                        "order_by": 1695888000,
                                        "status": 2,
                                        "last_update": "2023-09-28 17:30:24",
                                        "home_team_id": 52217281,
                                        "home_team_name": "耐克森英雄",
                                        "away_team_id": 52555190,
                                        "away_team_name": "SSG登陸者",
                                        "periods": {
                                            "period": 6,
                                            "Turn": "2",
                                            "Outs": "2",
                                            "Balls": "0",
                                            "Strikes": "0",
                                            "Bases": "1/1/0"
                                        },
                                        "scoreboard": {
                                            "1": [
                                                2,
                                                0,
                                                1,
                                                0,
                                                0,
                                                1,
                                                0
                                            ],
                                            "2": [
                                                1,
                                                0,
                                                0,
                                                0,
                                                0,
                                                1,
                                                0
                                            ]
                                        },
                                        "market_bet_count": 10,
                                        "list": {
                                            "28": {
                                                "market_id": 28,
                                                "market_name": "全場大小",
                                                "priority": 5,
                                                "main_line": "6.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 9097080511447720,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "6.5",
                                                        "price": "1.67",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:30:12"
                                                    },
                                                    {
                                                        "market_bet_id": 39620059911447720,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "6.5",
                                                        "price": "2.1",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:30:12"
                                                    }
                                                ]
                                            },
                                            "226": {
                                                "market_id": 226,
                                                "market_name": "全場獨贏",
                                                "priority": 1,
                                                "main_line": "",
                                                "list": [
                                                    {
                                                        "market_bet_id": 12611224911447720,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "",
                                                        "price": "1.29",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:30:12"
                                                    },
                                                    {
                                                        "market_bet_id": 52939677611447720,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "",
                                                        "price": "3.5",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:30:12"
                                                    }
                                                ]
                                            },
                                            "236": {
                                                "market_id": 236,
                                                "market_name": "上半場大小",
                                                "priority": 6,
                                                "main_line": "1.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 113073524511447730,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "1.5",
                                                        "price": "1.5",
                                                        "status": 3,
                                                        "last_update": "2023-09-28 17:22:13"
                                                    },
                                                    {
                                                        "market_bet_id": 94729695511447730,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "1.5",
                                                        "price": "2.5",
                                                        "status": 3,
                                                        "last_update": "2023-09-28 17:22:13"
                                                    }
                                                ]
                                            },
                                            "281": {
                                                "market_id": 281,
                                                "market_name": "上半場讓分",
                                                "priority": 4,
                                                "main_line": "-1.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 89825427511447730,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "-1.5",
                                                        "price": "2.25",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 16:59:52"
                                                    },
                                                    {
                                                        "market_bet_id": 51780298311447720,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "1.5",
                                                        "price": "1.57",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 16:59:52"
                                                    }
                                                ]
                                            },
                                            "342": {
                                                "market_id": 342,
                                                "market_name": "全場讓分",
                                                "priority": 3,
                                                "main_line": "-1.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 74865054511447730,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "-1.5",
                                                        "price": "1.87",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:30:12"
                                                    },
                                                    {
                                                        "market_bet_id": 104309684711447730,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "1.5",
                                                        "price": "1.8",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:30:12"
                                                    }
                                                ]
                                            }
                                        }
                                    },
                                    "11447722": {
                                        "fixture_id": 11447722,
                                        "start_time": "2023-09-28 16:00:00",
                                        "order_by": 1695888000,
                                        "status": 2,
                                        "last_update": "2023-09-28 17:30:13",
                                        "home_team_id": 299496,
                                        "home_team_name": "LG雙子",
                                        "away_team_id": 299493,
                                        "away_team_name": "三星獅子",
                                        "periods": {
                                            "period": 5,
                                            "Strikes": "1",
                                            "Balls": "0",
                                            "Bases": "0/0/0",
                                            "Outs": "1",
                                            "Turn": "2"
                                        },
                                        "scoreboard": {
                                            "1": [
                                                0,
                                                0,
                                                0,
                                                0,
                                                0,
                                                0
                                            ],
                                            "2": [
                                                9,
                                                2,
                                                0,
                                                0,
                                                7,
                                                0
                                            ]
                                        },
                                        "market_bet_count": 10,
                                        "list": {
                                            "28": {
                                                "market_id": 28,
                                                "market_name": "全場大小",
                                                "priority": 5,
                                                "main_line": "13.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 44874513611447720,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "13.5",
                                                        "price": "1.91",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:30:13"
                                                    },
                                                    {
                                                        "market_bet_id": 201067318411447700,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "13.5",
                                                        "price": "1.77",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:30:13"
                                                    }
                                                ]
                                            },
                                            "226": {
                                                "market_id": 226,
                                                "market_name": "全場獨贏",
                                                "priority": 1,
                                                "main_line": "",
                                                "list": [
                                                    {
                                                        "market_bet_id": 40910592811447720,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "",
                                                        "price": "17",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:30:13"
                                                    },
                                                    {
                                                        "market_bet_id": 582140111447722,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "",
                                                        "price": "1",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:30:13"
                                                    }
                                                ]
                                            },
                                            "236": {
                                                "market_id": 236,
                                                "market_name": "上半場大小",
                                                "priority": 6,
                                                "main_line": "10.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 147014697411447700,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "10.5",
                                                        "price": "2.2",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:21:10"
                                                    },
                                                    {
                                                        "market_bet_id": 149013914411447700,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "10.5",
                                                        "price": "1.63",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:21:10"
                                                    }
                                                ]
                                            },
                                            "281": {
                                                "market_id": 281,
                                                "market_name": "上半場讓分",
                                                "priority": 4,
                                                "main_line": "8.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 205692557411447700,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "8.5",
                                                        "price": "1.83",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:21:10"
                                                    },
                                                    {
                                                        "market_bet_id": 206030549411447700,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "-8.5",
                                                        "price": "1.83",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 17:21:10"
                                                    }
                                                ]
                                            },
                                            "342": {
                                                "market_id": 342,
                                                "market_name": "全場讓分",
                                                "priority": 3,
                                                "main_line": "8.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 66405907611447720,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "8.5",
                                                        "price": "1.71",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:30:13"
                                                    },
                                                    {
                                                        "market_bet_id": 102470968811447730,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "-8.5",
                                                        "price": "2",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:30:13"
                                                    }
                                                ]
                                            }
                                        }
                                    }
                                }
                            },
                            "183": {
                                "league_id": 183,
                                "league_name": "美國職業棒球聯賽",
                                "list": {
                                    "11448572": {
                                        "fixture_id": 11448572,
                                        "start_time": "2023-09-29 02:46:00",
                                        "order_by": 1695926760,
                                        "status": 9,
                                        "last_update": "2023-09-28 16:45:05",
                                        "home_team_id": 77587,
                                        "home_team_name": "底特律老虎",
                                        "away_team_id": 77588,
                                        "away_team_name": "堪薩斯皇家",
                                        "periods": null,
                                        "scoreboard": null,
                                        "market_bet_count": 10,
                                        "list": {
                                            "28": {
                                                "market_id": 28,
                                                "market_name": "全場大小",
                                                "priority": 5,
                                                "main_line": "7.0",
                                                "list": [
                                                    {
                                                        "market_bet_id": 35860972811448572,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "7.0",
                                                        "price": "1.74",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 13:31:28"
                                                    },
                                                    {
                                                        "market_bet_id": 54265856411448580,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "7.0",
                                                        "price": "1.95",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 13:31:28"
                                                    }
                                                ]
                                            },
                                            "226": {
                                                "market_id": 226,
                                                "market_name": "全場獨贏",
                                                "priority": 1,
                                                "main_line": "",
                                                "list": [
                                                    {
                                                        "market_bet_id": 164568195611448580,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "",
                                                        "price": "1.86",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 14:35:00"
                                                    },
                                                    {
                                                        "market_bet_id": 108320139911448580,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "",
                                                        "price": "1.95",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 14:35:00"
                                                    }
                                                ]
                                            },
                                            "236": {
                                                "market_id": 236,
                                                "market_name": "上半場大小",
                                                "priority": 6,
                                                "main_line": "4.0",
                                                "list": [
                                                    {
                                                        "market_bet_id": 140889182011448580,
                                                        "market_bet_name": "大",
                                                        "market_bet_name_en": "Over",
                                                        "line": "4.0",
                                                        "price": "1.9",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:03:02"
                                                    },
                                                    {
                                                        "market_bet_id": 75516805811448580,
                                                        "market_bet_name": "小",
                                                        "market_bet_name_en": "Under",
                                                        "line": "4.0",
                                                        "price": "1.9",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 17:03:02"
                                                    }
                                                ]
                                            },
                                            "281": {
                                                "market_id": 281,
                                                "market_name": "上半場讓分",
                                                "priority": 4,
                                                "main_line": "0.0",
                                                "list": [
                                                    {
                                                        "market_bet_id": 112431120311448580,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "0.0",
                                                        "price": "1.86",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 14:35:00"
                                                    },
                                                    {
                                                        "market_bet_id": 79889284011448580,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "0.0",
                                                        "price": "1.95",
                                                        "status": 2,
                                                        "last_update": "2023-09-28 14:35:00"
                                                    }
                                                ]
                                            },
                                            "342": {
                                                "market_id": 342,
                                                "market_name": "全場讓分",
                                                "priority": 3,
                                                "main_line": "1.5",
                                                "list": [
                                                    {
                                                        "market_bet_id": 90903231511448580,
                                                        "market_bet_name": "主",
                                                        "market_bet_name_en": "1",
                                                        "line": "1.5",
                                                        "price": "1.5",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 16:55:02"
                                                    },
                                                    {
                                                        "market_bet_id": 151362953511448580,
                                                        "market_bet_name": "客",
                                                        "market_bet_name_en": "2",
                                                        "line": "-1.5",
                                                        "price": "2.5",
                                                        "status": 1,
                                                        "last_update": "2023-09-28 16:55:02"
                                                    }
                                                ]
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            },
            "message": "SUCCESS_API_MATCH_INDEX_01",
            "gzip": true
        }





        // fake data test

        Object.entries(matchListD.data).map(([k, v]) => {  // living early toggle
            Object.entries(v[sport].list).map(([k2, v2]) => { // league toggle
                Object.entries(v2.list).map(([k3, v3]) => {  // fixture card
                    let isExist = $(`#${k3}`).length > 0 ? true : false // isExist already
                    let isCateExist = $(`#toggleContent_${k}`).length > 0 ? true : false // is cate exist
                    let isLeagueExist = $(`#seriesWrapperContent_${k}_${v2.league_id}`).length > 0 ? true : false // is league exist 
                    if( isExist ) {
                        let card = $(`#${k3}`) 
                        let time = card.find('.timer');
                        let home_team_info = card.find('[key="homeTeamInfo"]')
                        let away_team_info = card.find('[key="awayTeamInfo"]')
                        let nowStatus = parseInt(card.attr('status'))
                        let isStatusSame = nowStatus === v3.status ? true : false // is status the same
                        let isSwitchCate = !isStatusSame && v3.status !== 1// is changing early to living
                        console.log(k3 + ' isSwitch ->' + isSwitchCate)
                        if( isSwitchCate ) {
                            if( !isCateExist ) createCate(k, v)
                            if( !isLeagueExist ) createLeague(k, k2, v2)
                            let parentNode =$(`#seriesWrapperContent_${k}_${v2.league_id}`)
                            let livingNode = $(`#${k3}`)
                            console.log(parentNode, livingNode)
                            livingNode.prependTo(parentNode); // move to corrsponding cate and league
                            card.attr('cate', k)
                            card.attr('status', v3.status)
                        }   

                        // living
                        if( v3.status === 2 ) {
                            home_team_info.find('.scoreSpan').html( v3.scoreboard[1][0] )
                            away_team_info.find('.scoreSpan').html( v3.scoreboard[2][0] )
                            let timerStr = v3.periods.period + langTrans.mainArea.stage
                            v3.periods.Turn === '1' ? timerStr += langTrans.mainArea.lowerStage : timerStr += langTrans.mainArea.upperStage
                            time.html(timerStr)
                        }
                        // ready to start
                        if( v3.status === 9 ) {
                            time.html(langTrans.mainArea.readyToStart)
                        }

                        priorityArr.forEach(( i, j ) => {
                            let bet_div = $(`#${k3} div[priority=${i}]`)
                            let betData = Object.values(v3.list).find(m => m.priority === i)
                            let firstDiv = bet_div.find('div[index=0]')
                            let secondDiv = bet_div.find('div[index=1]')
                            let thirdDiv = bet_div.find('div[index=2]')
                            let item = null
                            if( betData && Object.keys(betData.list).length > 0 ) {
                                Object.entries(betData.list).map(([k4, v4], s) => { 
                                    item = bet_div.find('.betItemDiv').eq(s)
                                    // old attribute
                                    let market_bet_id = item.attr('market_bet_id')
                                    let price = item.attr('bet_rate')

                                    // 判斷盤口是否有改變
                                    if( market_bet_id.toString() === (v4.market_bet_id).toString() ) {
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
                                    } else {
                                        console.log(item.attr('home') + ' VS ' + item.attr('away'))
                                        console.log('盤口改變:: ' + item.attr('bet_type') + ' ' + item.attr('bet_name') + ' -> ' + v4.market_bet_name + ' ' + v4.line)
                                    }

                                    // set attribute
                                    item.attr('market_bet_id', v4.market_bet_id)
                                    item.attr('bet_rate', v4.price)
                                    item.attr('bet_name', v4.market_bet_name + ' ' + v4.line)
                                    // 賦值
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

                                if( thirdDiv ) {
                                    thirdDiv.find('.rate_name').hide()
                                    thirdDiv.find('.odd').hide()
                                    thirdDiv.find('i').show()
                                    thirdDiv.removeAttr('onclick')
                                }
                            }
                        });
                    } else {
                        // 新的賽事
                        if( !isCateExist ) createCate(k, v)
                        if( !isLeagueExist ) createLeague(k, k2, v2)
                        createFixtureCard(k, v2.league_id, v2.league_name, k3, v3)
                    }
                })
            })
        })

        // 找移除的
        $('#indexContainerLeft .indexEachCard').each(function() {
            let cate = $(this).attr('cate')
            let league_id = $(this).attr('league_id')
            let fixture_id = $(this).attr('id')
            let resultArr = matchListD.data[cate][sport]?.list[league_id]?.list
            let result = null
            if( resultArr ) result = Object.keys(resultArr).map(key => resultArr[key]).find(item => (item.fixture_id).toString() === fixture_id.toString())
            if( !result ) {
                closeFixture(fixture_id)
            } 
        });
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

    // remove fixture
    function closeFixture(id) {
        console.log('closeFixture')
        $(`#${id}`).hide(1000)
        setTimeout(() => {
            $(`#${id}`).remove()
        }, 1000);
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
            if( count === 0 ) $(this).closest('.seriesWrapperTitle').hide()
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