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
                        bet_div.attr('priority', i)
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

        // 假數據測試
        matchListD = {
    "status": 1,
    "data": {
        "early": {
            "154914": {
                "sport_id": "154914",
                "sport_name": "棒球",
                "list": {
                    "183": {
                        "league_id": 183,
                        "league_name": "美國職業棒球聯賽",
                        "list": {
                            "11435158": {
                                "fixture_id": 11435158,
                                "start_time": "2023-09-27 06:10:00",
                                "status": 1,
                                "last_update": "2023-09-25 22:11:13",
                                "home_team_id": 77586,
                                "home_team_name": "克里夫蘭印地安人隊",
                                "away_team_id": 77599,
                                "away_team_name": "辛辛那堤紅人",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "8.0",
                                        "list": {
                                            "128495196111435150": {
                                                "market_bet_id": 128495196111435150,
                                                "market_bet_name": "大",
                                                "line": "8.0",
                                                "price": "2.0484",
                                                "status": 1,
                                                "last_update": "2023-09-26 05:04:03"
                                            },
                                            "147266216711435170": {
                                                "market_bet_id": 147266216711435170,
                                                "market_bet_name": "小",
                                                "line": "8.0",
                                                "price": "1.9538",
                                                "status": 1,
                                                "last_update": "2023-09-26 05:04:03"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "47032326911435160": {
                                                "market_bet_id": 47032326911435160,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:17:19"
                                            },
                                            "87360779611435150": {
                                                "market_bet_id": 87360779611435150,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:17:19"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "4.5",
                                        "list": {
                                            "72762116911435150": {
                                                "market_bet_id": 72762116911435150,
                                                "market_bet_name": "小",
                                                "line": "4.5",
                                                "price": "1.9538",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:37:19"
                                            },
                                            "158978072511435170": {
                                                "market_bet_id": 158978072511435170,
                                                "market_bet_name": "大",
                                                "line": "4.5",
                                                "price": "2.0484",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:37:19"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "22519935211435160": {
                                                "market_bet_id": 22519935211435160,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:17:19"
                                            },
                                            "201733068111435170": {
                                                "market_bet_id": 201733068111435170,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:17:19"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "1.5",
                                        "list": {
                                            "119844740511435150": {
                                                "market_bet_id": 119844740511435150,
                                                "market_bet_name": "主",
                                                "line": "1.5",
                                                "price": "1.5962",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:55:49"
                                            },
                                            "160381660111435170": {
                                                "market_bet_id": 160381660111435170,
                                                "market_bet_name": "客",
                                                "line": "-1.5",
                                                "price": "2.6774",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:55:49"
                                            }
                                        }
                                    }
                                }
                            },
                            "11435174": {
                                "fixture_id": 11435174,
                                "start_time": "2023-09-27 07:10:00",
                                "status": 1,
                                "last_update": "2023-09-25 22:13:13",
                                "home_team_id": 77584,
                                "home_team_name": "波士頓紅襪",
                                "away_team_id": 77593,
                                "away_team_name": "坦帕灣光芒",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "8.5",
                                        "list": {
                                            "75295734811435170": {
                                                "market_bet_id": 75295734811435170,
                                                "market_bet_name": "大",
                                                "line": "8.5",
                                                "price": "2.0484",
                                                "status": 1,
                                                "last_update": "2023-09-26 11:35:32"
                                            },
                                            "151083854811435170": {
                                                "market_bet_id": 151083854811435170,
                                                "market_bet_name": "小",
                                                "line": "8.5",
                                                "price": "1.9538",
                                                "status": 1,
                                                "last_update": "2023-09-26 11:35:32"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "17005711811435174": {
                                                "market_bet_id": 17005711811435174,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "2.1932",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:00:45"
                                            },
                                            "57334164511435176": {
                                                "market_bet_id": 57334164511435176,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "1.8381",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:00:45"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "4.0",
                                        "list": {
                                            "7136870611435174": {
                                                "market_bet_id": 7136870611435174,
                                                "market_bet_name": "大",
                                                "line": "4.0",
                                                "price": "1.878",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:36:34"
                                            },
                                            "188883336011435170": {
                                                "market_bet_id": 188883336011435170,
                                                "market_bet_name": "小",
                                                "line": "4.0",
                                                "price": "2.1389",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:36:34"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "50129248911435176": {
                                                "market_bet_id": 50129248911435176,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "2.1389",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:15:45"
                                            },
                                            "155114477411435170": {
                                                "market_bet_id": 155114477411435170,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "1.878",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:15:45"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "1.5",
                                        "list": {
                                            "23364123911435176": {
                                                "market_bet_id": 23364123911435176,
                                                "market_bet_name": "主",
                                                "line": "1.5",
                                                "price": "1.7467",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:00:45"
                                            },
                                            "198310973311435170": {
                                                "market_bet_id": 198310973311435170,
                                                "market_bet_name": "客",
                                                "line": "-1.5",
                                                "price": "2.3393",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:00:45"
                                            }
                                        }
                                    }
                                }
                            },
                            "11435185": {
                                "fixture_id": 11435185,
                                "start_time": "2023-09-27 07:20:00",
                                "status": 1,
                                "last_update": "2023-09-25 22:16:10",
                                "home_team_id": 77597,
                                "home_team_name": "亞特蘭大勇士",
                                "away_team_id": 77598,
                                "away_team_name": "芝加哥小熊",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "9.0",
                                        "list": {
                                            "1843980811435185": {
                                                "market_bet_id": 1843980811435185,
                                                "market_bet_name": "大",
                                                "line": "9.0",
                                                "price": "1.9263",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:26:05"
                                            },
                                            "90030172411435180": {
                                                "market_bet_id": 90030172411435180,
                                                "market_bet_name": "小",
                                                "line": "9.0",
                                                "price": "2.0795",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:26:05"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "38196123911435180": {
                                                "market_bet_id": 38196123911435180,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "2.2865",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:57:06"
                                            },
                                            "118412270211435180": {
                                                "market_bet_id": 118412270211435180,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "1.7773",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:57:06"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "5.0",
                                        "list": {
                                            "164935661711435200": {
                                                "market_bet_id": 164935661711435200,
                                                "market_bet_name": "小",
                                                "line": "5.0",
                                                "price": "2.0929",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:28:05"
                                            },
                                            "183270197311435200": {
                                                "market_bet_id": 183270197311435200,
                                                "market_bet_name": "大",
                                                "line": "5.0",
                                                "price": "1.915",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:28:05"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "111767140311435180": {
                                                "market_bet_id": 111767140311435180,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "1.8381",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:28:05"
                                            },
                                            "144308976611435200": {
                                                "market_bet_id": 144308976611435200,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "2.1932",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:28:05"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-1.5",
                                        "list": {
                                            "59187046411435180": {
                                                "market_bet_id": 59187046411435180,
                                                "market_bet_name": "客",
                                                "line": "1.5",
                                                "price": "1.6531",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:57:06"
                                            },
                                            "154108201411435200": {
                                                "market_bet_id": 154108201411435200,
                                                "market_bet_name": "主",
                                                "line": "-1.5",
                                                "price": "2.5313",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:57:06"
                                            }
                                        }
                                    }
                                }
                            },
                            "11435186": {
                                "fixture_id": 11435186,
                                "start_time": "2023-09-27 07:40:00",
                                "status": 1,
                                "last_update": "2023-09-25 22:16:15",
                                "home_team_id": 77604,
                                "home_team_name": "密爾瓦基釀酒",
                                "away_team_id": 77611,
                                "away_team_name": "聖路易紅雀",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "8.5",
                                        "list": {
                                            "112038783511435180": {
                                                "market_bet_id": 112038783511435180,
                                                "market_bet_name": "大",
                                                "line": "8.5",
                                                "price": "2.0333",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:25:20"
                                            },
                                            "177700736911435200": {
                                                "market_bet_id": 177700736911435200,
                                                "market_bet_name": "小",
                                                "line": "8.5",
                                                "price": "1.9677",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:25:20"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "20743611411435184": {
                                                "market_bet_id": 20743611411435184,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "2.4815",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:49:21"
                                            },
                                            "177352005511435200": {
                                                "market_bet_id": 177352005511435200,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "1.675",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:49:21"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "4.5",
                                        "list": {
                                            "182047661811435200": {
                                                "market_bet_id": 182047661811435200,
                                                "market_bet_name": "大",
                                                "line": "4.5",
                                                "price": "2.0929",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:27:18"
                                            },
                                            "203922841411435200": {
                                                "market_bet_id": 203922841411435200,
                                                "market_bet_name": "小",
                                                "line": "4.5",
                                                "price": "1.915",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:27:18"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "122509055411435180": {
                                                "market_bet_id": 122509055411435180,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "1.6979",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:27:18"
                                            },
                                            "133125349311435180": {
                                                "market_bet_id": 133125349311435180,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "2.4329",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:27:18"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-1.5",
                                        "list": {
                                            "74248798711435180": {
                                                "market_bet_id": 74248798711435180,
                                                "market_bet_name": "主",
                                                "line": "-1.5",
                                                "price": "2.4329",
                                                "status": 1,
                                                "last_update": "2023-09-26 10:58:48"
                                            },
                                            "83338177711435180": {
                                                "market_bet_id": 83338177711435180,
                                                "market_bet_name": "客",
                                                "line": "1.5",
                                                "price": "1.6979",
                                                "status": 1,
                                                "last_update": "2023-09-26 10:58:48"
                                            }
                                        }
                                    }
                                }
                            },
                            "11435200": {
                                "fixture_id": 11435200,
                                "start_time": "2023-09-27 07:10:00",
                                "status": 1,
                                "last_update": "2023-09-25 22:21:31",
                                "home_team_id": 77606,
                                "home_team_name": "紐約大都會",
                                "away_team_id": 77601,
                                "away_team_name": "邁阿密馬林魚",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "7.5",
                                        "list": {
                                            "112742438111435200": {
                                                "market_bet_id": 112742438111435200,
                                                "market_bet_name": "小",
                                                "line": "7.5",
                                                "price": "2.0333",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:00:30"
                                            },
                                            "144686361711435200": {
                                                "market_bet_id": 144686361711435200,
                                                "market_bet_name": "大",
                                                "line": "7.5",
                                                "price": "1.9677",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:00:30"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "1948482411435200": {
                                                "market_bet_id": 1948482411435200,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "2.1389",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:44:25"
                                            },
                                            "42276935111435200": {
                                                "market_bet_id": 42276935111435200,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "1.878",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:44:25"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "4.0",
                                        "list": {
                                            "124968426411435200": {
                                                "market_bet_id": 124968426411435200,
                                                "market_bet_name": "大",
                                                "line": "4.0",
                                                "price": "1.878",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:17:25"
                                            },
                                            "152025989411435200": {
                                                "market_bet_id": 152025989411435200,
                                                "market_bet_name": "小",
                                                "line": "4.0",
                                                "price": "2.1389",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:17:25"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "87917186811435200": {
                                                "market_bet_id": 87917186811435200,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "1.878",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:50:56"
                                            },
                                            "117326539511435200": {
                                                "market_bet_id": 117326539511435200,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "2.1389",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:50:56"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "1.5",
                                        "list": {
                                            "102996830511435200": {
                                                "market_bet_id": 102996830511435200,
                                                "market_bet_name": "主",
                                                "line": "1.5",
                                                "price": "1.675",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:44:25"
                                            },
                                            "147147943911435200": {
                                                "market_bet_id": 147147943911435200,
                                                "market_bet_name": "客",
                                                "line": "-1.5",
                                                "price": "2.4815",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:44:25"
                                            }
                                        }
                                    }
                                }
                            },
                            "11435201": {
                                "fixture_id": 11435201,
                                "start_time": "2023-09-27 09:45:00",
                                "status": 1,
                                "last_update": "2023-09-25 22:21:37",
                                "home_team_id": 77610,
                                "home_team_name": "舊金山巨人",
                                "away_team_id": 77609,
                                "away_team_name": "聖地牙哥教士",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "8.0",
                                        "list": {
                                            "2365483011435201": {
                                                "market_bet_id": 2365483011435201,
                                                "market_bet_name": "大",
                                                "line": "8.0",
                                                "price": "1.9677",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:28:21"
                                            },
                                            "109221236011435200": {
                                                "market_bet_id": 109221236011435200,
                                                "market_bet_name": "小",
                                                "line": "8.0",
                                                "price": "2.0333",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:28:21"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "81692866911435200": {
                                                "market_bet_id": 81692866911435200,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "2.0929",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:17:21"
                                            },
                                            "191195468611435200": {
                                                "market_bet_id": 191195468611435200,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "1.915",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:17:21"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "4.5",
                                        "list": {
                                            "49567085411435200": {
                                                "market_bet_id": 49567085411435200,
                                                "market_bet_name": "大",
                                                "line": "4.5",
                                                "price": "2.2202",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:42:49"
                                            },
                                            "190183579811435200": {
                                                "market_bet_id": 190183579811435200,
                                                "market_bet_name": "小",
                                                "line": "4.5",
                                                "price": "1.8195",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:42:49"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "193291687811435200": {
                                                "market_bet_id": 193291687811435200,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "2.2356",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:43:25"
                                            },
                                            "203663205511435200": {
                                                "market_bet_id": 203663205511435200,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "1.8093",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:43:25"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "1.5",
                                        "list": {
                                            "45727695011435200": {
                                                "market_bet_id": 45727695011435200,
                                                "market_bet_name": "客",
                                                "line": "-1.5",
                                                "price": "2.4815",
                                                "status": 1,
                                                "last_update": "2023-09-26 05:04:04"
                                            },
                                            "53436254811435200": {
                                                "market_bet_id": 53436254811435200,
                                                "market_bet_name": "主",
                                                "line": "1.5",
                                                "price": "1.675",
                                                "status": 1,
                                                "last_update": "2023-09-26 05:04:04"
                                            }
                                        }
                                    }
                                }
                            },
                            "11435232": {
                                "fixture_id": 11435232,
                                "start_time": "2023-09-27 10:05:00",
                                "status": 1,
                                "last_update": "2023-09-25 22:40:18",
                                "home_team_id": 77592,
                                "home_team_name": "西雅圖水手",
                                "away_team_id": 77602,
                                "away_team_name": "休士頓太空人",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "7.5",
                                        "list": {
                                            "79323870411435230": {
                                                "market_bet_id": 79323870411435230,
                                                "market_bet_name": "小",
                                                "line": "7.5",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:14:50"
                                            },
                                            "89059399411435230": {
                                                "market_bet_id": 89059399411435230,
                                                "market_bet_name": "大",
                                                "line": "7.5",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:14:50"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "7023315211435232": {
                                                "market_bet_id": 7023315211435232,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "2.3393",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:49:46"
                                            },
                                            "163631709311435230": {
                                                "market_bet_id": 163631709311435230,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "1.7467",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:49:46"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "4.5",
                                        "list": {
                                            "2846772411435232": {
                                                "market_bet_id": 2846772411435232,
                                                "market_bet_name": "小",
                                                "line": "4.5",
                                                "price": "1.915",
                                                "status": 1,
                                                "last_update": "2023-09-26 06:07:31"
                                            },
                                            "87857122411435230": {
                                                "market_bet_id": 87857122411435230,
                                                "market_bet_name": "大",
                                                "line": "4.5",
                                                "price": "2.0929",
                                                "status": 1,
                                                "last_update": "2023-09-26 06:07:31"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "33185198811435230": {
                                                "market_bet_id": 33185198811435230,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "1.7467",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:35:45"
                                            },
                                            "43801492711435230": {
                                                "market_bet_id": 43801492711435230,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "2.3393",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:35:45"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-1.5",
                                        "list": {
                                            "16423152911435232": {
                                                "market_bet_id": 16423152911435232,
                                                "market_bet_name": "主",
                                                "line": "-1.5",
                                                "price": "2.5823",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:49:46"
                                            },
                                            "71693188711435230": {
                                                "market_bet_id": 71693188711435230,
                                                "market_bet_name": "客",
                                                "line": "1.5",
                                                "price": "1.632",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:49:46"
                                            }
                                        }
                                    }
                                }
                            },
                            "11436237": {
                                "fixture_id": 11436237,
                                "start_time": "2023-09-27 07:40:00",
                                "status": 1,
                                "last_update": "2023-09-26 01:45:14",
                                "home_team_id": 77585,
                                "home_team_name": "芝加哥白襪",
                                "away_team_id": 77596,
                                "away_team_name": "亞歷桑那響尾蛇",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "9.0",
                                        "list": {
                                            "65922377911436240": {
                                                "market_bet_id": 65922377911436240,
                                                "market_bet_name": "大",
                                                "line": "9.0",
                                                "price": "1.9263",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:04:57"
                                            },
                                            "119901467311436240": {
                                                "market_bet_id": 119901467311436240,
                                                "market_bet_name": "小",
                                                "line": "9.0",
                                                "price": "2.0795",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:04:57"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "133803344511436240": {
                                                "market_bet_id": 133803344511436240,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "2.3393",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:00:24"
                                            },
                                            "174131797211436220": {
                                                "market_bet_id": 174131797211436220,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "1.7467",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:00:24"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "5.0",
                                        "list": {
                                            "38878823211436240": {
                                                "market_bet_id": 38878823211436240,
                                                "market_bet_name": "大",
                                                "line": "5.0",
                                                "price": "1.915",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:04:27"
                                            },
                                            "172850200411436220": {
                                                "market_bet_id": 172850200411436220,
                                                "market_bet_name": "小",
                                                "line": "5.0",
                                                "price": "2.0929",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:04:27"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "78902080711436240": {
                                                "market_bet_id": 78902080711436240,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "1.7773",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:04:27"
                                            },
                                            "148267188011436220": {
                                                "market_bet_id": 148267188011436220,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "2.2865",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:04:27"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "1.5",
                                        "list": {
                                            "66397871811436240": {
                                                "market_bet_id": 66397871811436240,
                                                "market_bet_name": "主",
                                                "line": "1.5",
                                                "price": "1.8381",
                                                "status": 1,
                                                "last_update": "2023-09-26 11:20:51"
                                            },
                                            "74764198411436240": {
                                                "market_bet_id": 74764198411436240,
                                                "market_bet_name": "客",
                                                "line": "-1.5",
                                                "price": "2.1932",
                                                "status": 1,
                                                "last_update": "2023-09-26 11:20:51"
                                            }
                                        }
                                    }
                                }
                            },
                            "11436907": {
                                "fixture_id": 11436907,
                                "start_time": "2023-09-27 07:07:00",
                                "status": 1,
                                "last_update": "2023-09-26 05:08:01",
                                "home_team_id": 77595,
                                "home_team_name": "多倫多藍鳥",
                                "away_team_id": 77590,
                                "away_team_name": "紐約洋基",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "7.0",
                                        "list": {
                                            "6257737111436907": {
                                                "market_bet_id": 6257737111436907,
                                                "market_bet_name": "小",
                                                "line": "7.0",
                                                "price": "2.0484",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:47:46"
                                            },
                                            "20481638711436908": {
                                                "market_bet_id": 20481638711436908,
                                                "market_bet_name": "大",
                                                "line": "7.0",
                                                "price": "1.9538",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:47:46"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "17833538311436908": {
                                                "market_bet_id": 17833538311436908,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "1.675",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:11:16"
                                            },
                                            "58161991011436904": {
                                                "market_bet_id": 58161991011436904,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "2.4815",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:11:16"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "4.0",
                                        "list": {
                                            "11305026911436908": {
                                                "market_bet_id": 11305026911436908,
                                                "market_bet_name": "大",
                                                "line": "4.0",
                                                "price": "2.1389",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:42:42"
                                            },
                                            "122355929711436910": {
                                                "market_bet_id": 122355929711436910,
                                                "market_bet_name": "小",
                                                "line": "4.0",
                                                "price": "1.878",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:42:42"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "88110113911436910": {
                                                "market_bet_id": 88110113911436910,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "2.5313",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:49:13"
                                            },
                                            "114217347011436910": {
                                                "market_bet_id": 114217347011436910,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "1.6531",
                                                "status": 1,
                                                "last_update": "2023-09-26 14:49:13"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-1.5",
                                        "list": {
                                            "68421740511436904": {
                                                "market_bet_id": 68421740511436904,
                                                "market_bet_name": "客",
                                                "line": "1.5",
                                                "price": "1.6531",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:11:16"
                                            },
                                            "121632538311436910": {
                                                "market_bet_id": 121632538311436910,
                                                "market_bet_name": "主",
                                                "line": "-1.5",
                                                "price": "2.5313",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:11:16"
                                            }
                                        }
                                    }
                                }
                            },
                            "11437357": {
                                "fixture_id": 11437357,
                                "start_time": "2023-09-27 06:35:00",
                                "status": 1,
                                "last_update": "2023-09-26 07:43:51",
                                "home_team_id": 77583,
                                "home_team_name": "巴爾地摩金鶯隊",
                                "away_team_id": 100659,
                                "away_team_name": "華盛頓國民",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "8.0",
                                        "list": {
                                            "119549138411437360": {
                                                "market_bet_id": 119549138411437360,
                                                "market_bet_name": "大",
                                                "line": "8.0",
                                                "price": "2.0484",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:49:23"
                                            },
                                            "146571515011437340": {
                                                "market_bet_id": 146571515011437340,
                                                "market_bet_name": "小",
                                                "line": "8.0",
                                                "price": "1.9538",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:49:23"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "99009189611437360": {
                                                "market_bet_id": 99009189611437360,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "3.1277",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:47:22"
                                            },
                                            "173879145911437340": {
                                                "market_bet_id": 173879145911437340,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "1.47",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:47:22"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "4.0",
                                        "list": {
                                            "165504104511437340": {
                                                "market_bet_id": 165504104511437340,
                                                "market_bet_name": "小",
                                                "line": "4.0",
                                                "price": "2.0484",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:45:52"
                                            },
                                            "165582589111437340": {
                                                "market_bet_id": 165582589111437340,
                                                "market_bet_name": "大",
                                                "line": "4.0",
                                                "price": "1.9538",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:45:52"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "161848208111437340": {
                                                "market_bet_id": 161848208111437340,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "2.931",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:45:52"
                                            },
                                            "194390044411437340": {
                                                "market_bet_id": 194390044411437340,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "1.5179",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:45:52"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-1.5",
                                        "list": {
                                            "10739003111437356": {
                                                "market_bet_id": 10739003111437356,
                                                "market_bet_name": "主",
                                                "line": "-1.5",
                                                "price": "1.9538",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:47:22"
                                            },
                                            "49251763111437360": {
                                                "market_bet_id": 49251763111437360,
                                                "market_bet_name": "客",
                                                "line": "1.5",
                                                "price": "2.0484",
                                                "status": 1,
                                                "last_update": "2023-09-26 13:47:22"
                                            }
                                        }
                                    }
                                }
                            },
                            "11438075": {
                                "fixture_id": 11438075,
                                "start_time": "2023-09-27 09:38:00",
                                "status": 1,
                                "last_update": "2023-09-26 11:51:31",
                                "home_team_id": 77582,
                                "home_team_name": "洛杉磯天使",
                                "away_team_id": 77594,
                                "away_team_name": "德州遊騎兵",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "9.5",
                                        "list": {
                                            "60210039411438070": {
                                                "market_bet_id": 60210039411438070,
                                                "market_bet_name": "大",
                                                "line": "9.5",
                                                "price": "2.0484",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:30:45"
                                            },
                                            "131374945611438080": {
                                                "market_bet_id": 131374945611438080,
                                                "market_bet_name": "小",
                                                "line": "9.5",
                                                "price": "1.9538",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:30:45"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "133228816611438080": {
                                                "market_bet_id": 133228816611438080,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "1.5962",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:30:45"
                                            },
                                            "173557269311438080": {
                                                "market_bet_id": 173557269311438080,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "2.6774",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:30:45"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "1.5",
                                        "list": {
                                            "68319609011438070": {
                                                "market_bet_id": 68319609011438070,
                                                "market_bet_name": "客",
                                                "line": "-1.5",
                                                "price": "1.915",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:30:45"
                                            },
                                            "94981982011438080": {
                                                "market_bet_id": 94981982011438080,
                                                "market_bet_name": "主",
                                                "line": "1.5",
                                                "price": "2.0929",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:30:45"
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    },
                    "5540": {
                        "league_id": 5540,
                        "league_name": "中華職棒",
                        "list": {
                            "11393348": {
                                "fixture_id": 11393348,
                                "start_time": "2023-09-26 18:35:00",
                                "status": 1,
                                "last_update": "2023-09-19 08:07:17",
                                "home_team_id": 51994905,
                                "home_team_name": "富邦勇士",
                                "away_team_id": 52398924,
                                "away_team_name": "味全龍",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "7.5",
                                        "list": {
                                            "54488765311393340": {
                                                "market_bet_id": 54488765311393340,
                                                "market_bet_name": "小",
                                                "line": "7.5",
                                                "price": "1.8143",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:54:53"
                                            },
                                            "106524333111393340": {
                                                "market_bet_id": 106524333111393340,
                                                "market_bet_name": "大",
                                                "line": "7.5",
                                                "price": "2.2281",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:54:53"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "4459618911393348": {
                                                "market_bet_id": 4459618911393348,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "1.8488",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:06:53"
                                            },
                                            "44788071611393340": {
                                                "market_bet_id": 44788071611393340,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "2.1782",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:06:53"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "1.5",
                                        "list": {
                                            "121216607511393340": {
                                                "market_bet_id": 121216607511393340,
                                                "market_bet_name": "主",
                                                "line": "1.5",
                                                "price": "1.6809",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:06:53"
                                            },
                                            "161990276911393340": {
                                                "market_bet_id": 161990276911393340,
                                                "market_bet_name": "客",
                                                "line": "-1.5",
                                                "price": "2.4687",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:06:53"
                                            }
                                        }
                                    }
                                }
                            },
                            "11393362": {
                                "fixture_id": 11393362,
                                "start_time": "2023-09-26 18:35:00",
                                "status": 1,
                                "last_update": "2023-09-19 08:07:19",
                                "home_team_id": 52036807,
                                "home_team_name": "中信兄弟",
                                "away_team_id": 52398469,
                                "away_team_name": "樂天猿",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "9.5",
                                        "list": {
                                            "95490506711393360": {
                                                "market_bet_id": 95490506711393360,
                                                "market_bet_name": "大",
                                                "line": "9.5",
                                                "price": "2.1364",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:03:51"
                                            },
                                            "191067188911393380": {
                                                "market_bet_id": 191067188911393380,
                                                "market_bet_name": "小",
                                                "line": "9.5",
                                                "price": "1.88",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:03:51"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "27225271611393360": {
                                                "market_bet_id": 27225271611393360,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "2.2798",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:55:21"
                                            },
                                            "183833665711393380": {
                                                "market_bet_id": 183833665711393380,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "1.7814",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:55:21"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "1.5",
                                        "list": {
                                            "67766558911393360": {
                                                "market_bet_id": 67766558911393360,
                                                "market_bet_name": "主",
                                                "line": "1.5",
                                                "price": "1.8143",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:03:51"
                                            },
                                            "149129221111393380": {
                                                "market_bet_id": 149129221111393380,
                                                "market_bet_name": "客",
                                                "line": "-1.5",
                                                "price": "2.2281",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:03:51"
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    },
                    "7807": {
                        "league_id": 7807,
                        "league_name": "韓國職棒聯賽",
                        "list": {
                            "11434240": {
                                "fixture_id": 11434240,
                                "start_time": "2023-09-26 17:30:00",
                                "status": 1,
                                "last_update": "2023-09-25 19:57:55",
                                "home_team_id": 299490,
                                "home_team_name": "NC恐龍",
                                "away_team_id": 299495,
                                "away_team_name": "起亞老虎",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "9.0",
                                        "list": {
                                            "99564440911434240": {
                                                "market_bet_id": 99564440911434240,
                                                "market_bet_name": "小",
                                                "line": "9.0",
                                                "price": "1.9538",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:06:32"
                                            },
                                            "172024829911434240": {
                                                "market_bet_id": 172024829911434240,
                                                "market_bet_name": "大",
                                                "line": "9.0",
                                                "price": "2.0484",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:06:32"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "5193343711434240": {
                                                "market_bet_id": 5193343711434240,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "1.4381",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:06:32"
                                            },
                                            "45521796411434240": {
                                                "market_bet_id": 45521796411434240,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "3.2826",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:06:32"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "5.0",
                                        "list": {
                                            "69702814811434240": {
                                                "market_bet_id": 69702814811434240,
                                                "market_bet_name": "大",
                                                "line": "5.0",
                                                "price": "2.0929",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:05:02"
                                            },
                                            "201272711611434240": {
                                                "market_bet_id": 201272711611434240,
                                                "market_bet_name": "小",
                                                "line": "5.0",
                                                "price": "1.915",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:05:02"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "24248644811434240": {
                                                "market_bet_id": 24248644811434240,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "1.4",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:05:02"
                                            },
                                            "200004358511434240": {
                                                "market_bet_id": 200004358511434240,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "3.5",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:05:02"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-2.5",
                                        "list": {
                                            "21490507811434240": {
                                                "market_bet_id": 21490507811434240,
                                                "market_bet_name": "客",
                                                "line": "2.5",
                                                "price": "1.9263",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:38:33"
                                            },
                                            "118930328211434240": {
                                                "market_bet_id": 118930328211434240,
                                                "market_bet_name": "主",
                                                "line": "-2.5",
                                                "price": "2.0795",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:38:33"
                                            }
                                        }
                                    }
                                }
                            },
                            "11434241": {
                                "fixture_id": 11434241,
                                "start_time": "2023-09-26 17:30:00",
                                "status": 1,
                                "last_update": "2023-09-25 19:57:55",
                                "home_team_id": 52555190,
                                "home_team_name": "SSG登陸者",
                                "away_team_id": 52325610,
                                "away_team_name": "鬥山熊",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "9.0",
                                        "list": {
                                            "103085646811434240": {
                                                "market_bet_id": 103085646811434240,
                                                "market_bet_name": "小",
                                                "line": "9.0",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:13:04"
                                            },
                                            "203778536011434240": {
                                                "market_bet_id": 203778536011434240,
                                                "market_bet_name": "大",
                                                "line": "9.0",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:13:04"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "84937728211434240": {
                                                "market_bet_id": 84937728211434240,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "1.6979",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:52:34"
                                            },
                                            "187950607311434240": {
                                                "market_bet_id": 187950607311434240,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "2.4329",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:52:34"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "5.0",
                                        "list": {
                                            "145104156111434240": {
                                                "market_bet_id": 145104156111434240,
                                                "market_bet_name": "大",
                                                "line": "5.0",
                                                "price": "2.0929",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:44:34"
                                            },
                                            "150273408711434240": {
                                                "market_bet_id": 150273408711434240,
                                                "market_bet_name": "小",
                                                "line": "5.0",
                                                "price": "1.915",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:44:34"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "51716503511434240": {
                                                "market_bet_id": 51716503511434240,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "1.6979",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:44:34"
                                            },
                                            "84258339811434240": {
                                                "market_bet_id": 84258339811434240,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "2.4329",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:44:34"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-0.5",
                                        "list": {
                                            "135159132311434240": {
                                                "market_bet_id": 135159132311434240,
                                                "market_bet_name": "客",
                                                "line": "0.5",
                                                "price": "2.2651",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:52:34"
                                            },
                                            "168713754711434240": {
                                                "market_bet_id": 168713754711434240,
                                                "market_bet_name": "主",
                                                "line": "-0.5",
                                                "price": "1.7905",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:52:34"
                                            }
                                        }
                                    }
                                }
                            },
                            "11434242": {
                                "fixture_id": 11434242,
                                "start_time": "2023-09-26 17:30:00",
                                "status": 1,
                                "last_update": "2023-09-25 19:57:55",
                                "home_team_id": 299496,
                                "home_team_name": "LG雙子",
                                "away_team_id": 334336,
                                "away_team_name": "KT巫師",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "9.0",
                                        "list": {
                                            "110284086511434240": {
                                                "market_bet_id": 110284086511434240,
                                                "market_bet_name": "大",
                                                "line": "9.0",
                                                "price": "1.855",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:37:21"
                                            },
                                            "194736185311434240": {
                                                "market_bet_id": 194736185311434240,
                                                "market_bet_name": "小",
                                                "line": "9.0",
                                                "price": "2.1696",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:37:21"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "10616771411434242": {
                                                "market_bet_id": 10616771411434242,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "2.1389",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:55:22"
                                            },
                                            "167225165511434240": {
                                                "market_bet_id": 167225165511434240,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "1.878",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:55:22"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "5.0",
                                        "list": {
                                            "40686150611434240": {
                                                "market_bet_id": 40686150611434240,
                                                "market_bet_name": "小",
                                                "line": "5.0",
                                                "price": "2.0929",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:34:50"
                                            },
                                            "186217252211434240": {
                                                "market_bet_id": 186217252211434240,
                                                "market_bet_name": "大",
                                                "line": "5.0",
                                                "price": "1.915",
                                                "status": 1,
                                                "last_update": "2023-09-26 15:34:50"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "30988159411434240": {
                                                "market_bet_id": 30988159411434240,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "1.878",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:59:52"
                                            },
                                            "41604453311434240": {
                                                "market_bet_id": 41604453311434240,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "2.1389",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:59:52"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-0.5",
                                        "list": {
                                            "79537741811434240": {
                                                "market_bet_id": 79537741811434240,
                                                "market_bet_name": "客",
                                                "line": "0.5",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:55:22"
                                            },
                                            "211965293411434240": {
                                                "market_bet_id": 211965293411434240,
                                                "market_bet_name": "主",
                                                "line": "-0.5",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:55:22"
                                            }
                                        }
                                    }
                                }
                            },
                            "11434243": {
                                "fixture_id": 11434243,
                                "start_time": "2023-09-26 17:30:00",
                                "status": 1,
                                "last_update": "2023-09-25 19:57:55",
                                "home_team_id": 299489,
                                "home_team_name": "華老鷹",
                                "away_team_id": 299493,
                                "away_team_name": "三星獅子",
                                "periods": null,
                                "scoreboard": null,
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "7.5",
                                        "list": {
                                            "189756781111434240": {
                                                "market_bet_id": 189756781111434240,
                                                "market_bet_name": "大",
                                                "line": "7.5",
                                                "price": "1.7905",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:06:05"
                                            },
                                            "191214946711434240": {
                                                "market_bet_id": 191214946711434240,
                                                "market_bet_name": "小",
                                                "line": "7.5",
                                                "price": "2.2651",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:06:05"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "182527179611434240": {
                                                "market_bet_id": 182527179611434240,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:07:35"
                                            },
                                            "206641097311434240": {
                                                "market_bet_id": 206641097311434240,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:07:35"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "4.0",
                                        "list": {
                                            "44088582211434240": {
                                                "market_bet_id": 44088582211434240,
                                                "market_bet_name": "小",
                                                "line": "4.0",
                                                "price": "2.0929",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:07:35"
                                            },
                                            "103773508811434240": {
                                                "market_bet_id": 103773508811434240,
                                                "market_bet_name": "大",
                                                "line": "4.0",
                                                "price": "1.915",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:07:35"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "0.0",
                                        "list": {
                                            "44976988911434240": {
                                                "market_bet_id": 44976988911434240,
                                                "market_bet_name": "主",
                                                "line": "0.0",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:07:35"
                                            },
                                            "157350472011434240": {
                                                "market_bet_id": 157350472011434240,
                                                "market_bet_name": "客",
                                                "line": "0.0",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:07:35"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "0.5",
                                        "list": {
                                            "19682734411434244": {
                                                "market_bet_id": 19682734411434244,
                                                "market_bet_name": "客",
                                                "line": "-0.5",
                                                "price": "2.1696",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:51:35"
                                            },
                                            "202136604811434240": {
                                                "market_bet_id": 202136604811434240,
                                                "market_bet_name": "主",
                                                "line": "0.5",
                                                "price": "1.855",
                                                "status": 1,
                                                "last_update": "2023-09-26 16:51:35"
                                            }
                                        }
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
                            "11435035": {
                                "fixture_id": 11435035,
                                "start_time": "2023-09-26 17:00:00",
                                "status": 2,
                                "last_update": "2023-09-26 17:12:42",
                                "home_team_id": 205813,
                                "home_team_name": "廣島鯉魚",
                                "away_team_id": 205812,
                                "away_team_name": "中日龍",
                                "periods": {
                                    "period": 1,
                                    "Strikes": "2",
                                    "Bases": "1/0/1",
                                    "Balls": "2",
                                    "Turn": "1",
                                    "Outs": "0"
                                },
                                "scoreboard": {
                                    "1": [
                                        0,
                                        0
                                    ],
                                    "2": [
                                        0,
                                        0
                                    ]
                                },
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "7.5",
                                        "list": {
                                            "185123559111435040": {
                                                "market_bet_id": 185123559111435040,
                                                "market_bet_name": "大",
                                                "line": "7.5",
                                                "price": "999",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:05"
                                            },
                                            "185408178311435040": {
                                                "market_bet_id": 185408178311435040,
                                                "market_bet_name": "小",
                                                "line": "7.5",
                                                "price": "0.00001",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:05"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "112783995111435040": {
                                                "market_bet_id": 112783995111435040,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "4",
                                                "status": 2,
                                                "last_update": "2023-09-26 17:12:05"
                                            },
                                            "153112447811435040": {
                                                "market_bet_id": 153112447811435040,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "666",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:05"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "4.5",
                                        "list": {
                                            "17225993711435036": {
                                                "market_bet_id": 17225993711435036,
                                                "market_bet_name": "大",
                                                "line": "4.5",
                                                "price": "1.857",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:05"
                                            },
                                            "89688518111435040": {
                                                "market_bet_id": 89688518111435040,
                                                "market_bet_name": "小",
                                                "line": "4.5",
                                                "price": "2.1669",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:05"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "-2.5",
                                        "list": {
                                            "767788111435035": {
                                                "market_bet_id": 767788111435035,
                                                "market_bet_name": "客",
                                                "line": "2.5",
                                                "price": "1.7386",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:43"
                                            },
                                            "120330229511435040": {
                                                "market_bet_id": 120330229511435040,
                                                "market_bet_name": "主",
                                                "line": "-2.5",
                                                "price": "2.3538",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:43"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-2.5",
                                        "list": {
                                            "123": {
                                                "market_bet_id": 123,
                                                "market_bet_name": "主",
                                                "line": "-88888",
                                                "price": "8.888888",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:43"
                                            },
                                            "456": {
                                                "market_bet_id": 456,
                                                "market_bet_name": "客",
                                                "line": "88888",
                                                "price": "0.888888",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:05"
                                            }
                                        }
                                    }
                                }
                            },
                            "11435036": {
                                "fixture_id": 11435036,
                                "start_time": "2023-09-26 17:00:00",
                                "status": 2,
                                "last_update": "2023-09-26 17:11:17",
                                "home_team_id": 205804,
                                "home_team_name": "歐力士猛牛",
                                "away_team_id": 205802,
                                "away_team_name": "埼玉西武獅",
                                "periods": {
                                    "period": 1,
                                    "Turn": "1",
                                    "Outs": "1",
                                    "Balls": "2",
                                    "Strikes": "2",
                                    "Bases": "0/0/0"
                                },
                                "scoreboard": {
                                    "1": [
                                        0,
                                        0
                                    ],
                                    "2": [
                                        0,
                                        0
                                    ]
                                },
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "5.5",
                                        "list": {
                                            "37229880011435040": {
                                                "market_bet_id": 37229880011435040,
                                                "market_bet_name": "小",
                                                "line": "5.5",
                                                "price": "2.1669",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:14"
                                            },
                                            "205783517011435040": {
                                                "market_bet_id": 205783517011435040,
                                                "market_bet_name": "大",
                                                "line": "5.5",
                                                "price": "1.857",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:14"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "48335305811435040": {
                                                "market_bet_id": 48335305811435040,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "2.3538",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:14"
                                            },
                                            "88663758511435040": {
                                                "market_bet_id": 88663758511435040,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "1.7386",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:14"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "2.5",
                                        "list": {
                                            "16844696411435036": {
                                                "market_bet_id": 16844696411435036,
                                                "market_bet_name": "大",
                                                "line": "2.5",
                                                "price": "1.9626",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:14"
                                            },
                                            "55380321211435040": {
                                                "market_bet_id": 55380321211435040,
                                                "market_bet_name": "小",
                                                "line": "2.5",
                                                "price": "2.0389",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:14"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "-0.5",
                                        "list": {
                                            "9074718011435036": {
                                                "market_bet_id": 9074718011435036,
                                                "market_bet_name": "客",
                                                "line": "0.5",
                                                "price": "1.857",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:14"
                                            },
                                            "18990655211435036": {
                                                "market_bet_id": 18990655211435036,
                                                "market_bet_name": "主",
                                                "line": "-0.5",
                                                "price": "2.1669",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:14"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-0.5",
                                        "list": {
                                            "28183689411435036": {
                                                "market_bet_id": 28183689411435036,
                                                "market_bet_name": "主",
                                                "line": "-0.5",
                                                "price": "1.7938",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:14"
                                            },
                                            "138492351011435040": {
                                                "market_bet_id": 138492351011435040,
                                                "market_bet_name": "客",
                                                "line": "0.5",
                                                "price": "2.2597",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:14"
                                            }
                                        }
                                    }
                                }
                            },
                            "11435037": {
                                "fixture_id": 11435037,
                                "start_time": "2023-09-26 17:00:00",
                                "status": 2,
                                "last_update": "2023-09-26 17:12:37",
                                "home_team_id": 205811,
                                "home_team_name": "阪神虎",
                                "away_team_id": 205814,
                                "away_team_name": "東京養樂多燕子",
                                "periods": {
                                    "period": 1,
                                    "Strikes": "2",
                                    "Bases": "0/0/0",
                                    "Balls": "1",
                                    "Turn": "1",
                                    "Outs": "0"
                                },
                                "scoreboard": {
                                    "1": [
                                        0,
                                        0
                                    ],
                                    "2": [
                                        0,
                                        0
                                    ]
                                },
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "6.5",
                                        "list": {
                                            "33708670811435036": {
                                                "market_bet_id": 33708670811435036,
                                                "market_bet_name": "小",
                                                "line": "6.5",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:09:44"
                                            },
                                            "71866119611435040": {
                                                "market_bet_id": 71866119611435040,
                                                "market_bet_name": "大",
                                                "line": "6.5",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:09:44"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "8919374011435037": {
                                                "market_bet_id": 8919374011435037,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "1.7386",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:07"
                                            },
                                            "147689020111435040": {
                                                "market_bet_id": 147689020111435040,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "2.3538",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:07"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "2.5",
                                        "list": {
                                            "99288449511435040": {
                                                "market_bet_id": 99288449511435040,
                                                "market_bet_name": "大",
                                                "line": "2.5",
                                                "price": "1.7386",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:38"
                                            },
                                            "134753579111435040": {
                                                "market_bet_id": 134753579111435040,
                                                "market_bet_name": "小",
                                                "line": "2.5",
                                                "price": "2.3538",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:38"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "-0.5",
                                        "list": {
                                            "68571333711435040": {
                                                "market_bet_id": 68571333711435040,
                                                "market_bet_name": "客",
                                                "line": "0.5",
                                                "price": "1.9267",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:07"
                                            },
                                            "186181001911435040": {
                                                "market_bet_id": 186181001911435040,
                                                "market_bet_name": "主",
                                                "line": "-0.5",
                                                "price": "2.0791",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:07"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-0.5",
                                        "list": {
                                            "21836921311435036": {
                                                "market_bet_id": 21836921311435036,
                                                "market_bet_name": "主",
                                                "line": "-0.5",
                                                "price": "1.7938",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:07"
                                            },
                                            "144908098711435040": {
                                                "market_bet_id": 144908098711435040,
                                                "market_bet_name": "客",
                                                "line": "0.5",
                                                "price": "2.2597",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:07"
                                            }
                                        }
                                    }
                                }
                            },
                            "11435038": {
                                "fixture_id": 11435038,
                                "start_time": "2023-09-26 17:00:00",
                                "status": 2,
                                "last_update": "2023-09-26 17:11:41",
                                "home_team_id": 205815,
                                "home_team_name": "橫濱DeNA灣星",
                                "away_team_id": 205810,
                                "away_team_name": "讀賣巨人",
                                "periods": {
                                    "period": 2,
                                    "Turn": "2",
                                    "Strikes": "0",
                                    "Outs": "3",
                                    "Balls": "0"
                                },
                                "scoreboard": {
                                    "1": [
                                        0,
                                        0,
                                        0
                                    ],
                                    "2": [
                                        0,
                                        0,
                                        0
                                    ]
                                },
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "6.5",
                                        "list": {
                                            "30699848911435040": {
                                                "market_bet_id": 30699848911435040,
                                                "market_bet_name": "小",
                                                "line": "6.5",
                                                "price": "1.857",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:10:48"
                                            },
                                            "157357559711435040": {
                                                "market_bet_id": 157357559711435040,
                                                "market_bet_name": "大",
                                                "line": "6.5",
                                                "price": "2.1669",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:10:48"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "20888976811435040": {
                                                "market_bet_id": 20888976811435040,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "2.0791",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:10:48"
                                            },
                                            "177497370911435040": {
                                                "market_bet_id": 177497370911435040,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "1.9267",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:10:48"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "2.5",
                                        "list": {
                                            "158016421811435040": {
                                                "market_bet_id": 158016421811435040,
                                                "market_bet_name": "大",
                                                "line": "2.5",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:11:34"
                                            },
                                            "181005951411435040": {
                                                "market_bet_id": 181005951411435040,
                                                "market_bet_name": "小",
                                                "line": "2.5",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:11:34"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "-0.5",
                                        "list": {
                                            "147906278611435040": {
                                                "market_bet_id": 147906278611435040,
                                                "market_bet_name": "主",
                                                "line": "-0.5",
                                                "price": "2.4781",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:11:34"
                                            },
                                            "203750006211435040": {
                                                "market_bet_id": 203750006211435040,
                                                "market_bet_name": "客",
                                                "line": "0.5",
                                                "price": "1.6765",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:11:34"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-0.5",
                                        "list": {
                                            "98675844411435040": {
                                                "market_bet_id": 98675844411435040,
                                                "market_bet_name": "客",
                                                "line": "0.5",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:11:34"
                                            },
                                            "126331604811435040": {
                                                "market_bet_id": 126331604811435040,
                                                "market_bet_name": "主",
                                                "line": "-0.5",
                                                "price": "2",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:11:34"
                                            }
                                        }
                                    }
                                }
                            },
                            "11435039": {
                                "fixture_id": 11435039,
                                "start_time": "2023-09-26 17:00:00",
                                "status": 2,
                                "last_update": "2023-09-26 17:12:17",
                                "home_team_id": 205806,
                                "home_team_name": "日本火腿鬥士",
                                "away_team_id": 205807,
                                "away_team_name": "千葉羅德海洋",
                                "periods": {
                                    "period": 1,
                                    "Turn": "1",
                                    "Outs": "1",
                                    "Balls": "0",
                                    "Strikes": "0"
                                },
                                "scoreboard": {
                                    "1": [
                                        1,
                                        1
                                    ],
                                    "2": [
                                        0,
                                        0
                                    ]
                                },
                                "list": {
                                    "28": {
                                        "market_id": 28,
                                        "market_name": "全場大小",
                                        "priority": 5,
                                        "main_line": "6.5",
                                        "list": {
                                            "27178643011435040": {
                                                "market_bet_id": 27178643011435040,
                                                "market_bet_name": "小",
                                                "line": "6.5",
                                                "price": "2.1669",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:19"
                                            },
                                            "44598824211435040": {
                                                "market_bet_id": 44598824211435040,
                                                "market_bet_name": "大",
                                                "line": "6.5",
                                                "price": "1.857",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:19"
                                            }
                                        }
                                    },
                                    "226": {
                                        "market_id": 226,
                                        "market_name": "全場獨贏",
                                        "priority": 1,
                                        "main_line": "",
                                        "list": {
                                            "172254974211435040": {
                                                "market_bet_id": 172254974211435040,
                                                "market_bet_name": "主",
                                                "line": "",
                                                "price": "1.486",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:19"
                                            },
                                            "212583426911435040": {
                                                "market_bet_id": 212583426911435040,
                                                "market_bet_name": "客",
                                                "line": "",
                                                "price": "3.0578",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:19"
                                            }
                                        }
                                    },
                                    "236": {
                                        "market_id": 236,
                                        "market_name": "上半場大小",
                                        "priority": 6,
                                        "main_line": "3.5",
                                        "list": {
                                            "82615077211435040": {
                                                "market_bet_id": 82615077211435040,
                                                "market_bet_name": "大",
                                                "line": "3.5",
                                                "price": "1.857",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:19"
                                            },
                                            "140861389411435040": {
                                                "market_bet_id": 140861389411435040,
                                                "market_bet_name": "小",
                                                "line": "3.5",
                                                "price": "2.1669",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:19"
                                            }
                                        }
                                    },
                                    "281": {
                                        "market_id": 281,
                                        "market_name": "上半場讓分",
                                        "priority": 4,
                                        "main_line": "-1.5",
                                        "list": {
                                            "76077174811435040": {
                                                "market_bet_id": 76077174811435040,
                                                "market_bet_name": "主",
                                                "line": "-1.5",
                                                "price": "2.2597",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:19"
                                            },
                                            "148631772611435040": {
                                                "market_bet_id": 148631772611435040,
                                                "market_bet_name": "客",
                                                "line": "1.5",
                                                "price": "1.7938",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:19"
                                            }
                                        }
                                    },
                                    "342": {
                                        "market_id": 342,
                                        "market_name": "全場讓分",
                                        "priority": 3,
                                        "main_line": "-1.5",
                                        "list": {
                                            "52236388411435040": {
                                                "market_bet_id": 52236388411435040,
                                                "market_bet_name": "客",
                                                "line": "1.5",
                                                "price": "1.7938",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:19"
                                            },
                                            "75969375011435040": {
                                                "market_bet_id": 75969375011435040,
                                                "market_bet_name": "主",
                                                "line": "-1.5",
                                                "price": "2.2597",
                                                "status": 1,
                                                "last_update": "2023-09-26 17:12:19"
                                            }
                                        }
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
    "gzip": false
}





        // 假數據測試

        Object.entries(matchListD.data).map(([k, v]) => {  // living early toggle
            Object.entries(v[sport].list).map(([k2, v2]) => { // league toggle
                Object.entries(v2.list).map(([k3, v3]) => {  // fixture card
                    let isExist = $(`#${k3}`).length > 0 ? true : false
                    if( isExist ) {
                        priorityArr.forEach(( i, j ) => {
                            let bet_div = $(`#${k3} div[priority=${i}]`)
                            let betData = Object.values(v3.list).find(m => m.priority === i)
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
                                    if( market_bet_id.toString() !== (v4.market_bet_id).toString() ) {
                                        console.log('盤口::' + market_bet_id + ' -> ' + v4.market_bet_id)
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
                                        console.log(betData.market_name + ' ' + v4.market_bet_name + ' ' + v4.line + ' -> open')
                                        item.find('.rate_name').show()
                                        item.find('.odd').show()
                                        item.find('i').hide()
                                        item.attr('onclick', 'openCal($(this))')
                                    } else {
                                        console.log(betData.market_name + ' ' + v4.market_bet_name + ' ' + v4.line + ' -> lock')
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