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
            <div class="leftSlideOrderCard row m-0" key='slideOrderCard'>
                <div class="col-12"><span key='bet_type'></span></div>
                <div class="col-8 mb-2 mt-2"><span key='bet_name'></span></div>
                <div class="col-4 mb-2 mt-2 text-right">
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
            <button id="submitOrder" onclick="sendOrder()">{{ trans('index.bet_area.bet') }}</button>
            <button id="cancelOrder">{{ trans('index.bet_area.cancel') }}</button>
        </div>
    </div>
</div>
<div id="leftSlideOrderLoadingContainer" class="hidden">
    <div id="leftSlideOrderLoadingSpinner"><div class="inner-spinner"></div></div>
    <span>{{ trans('index.bet_area.loading') }}</span>
</div>
<div id='searchCondition'>
    {{ trans('common.search_area.search') }}
</div>
<!-- early & living scoreboard-->
<div id="scoreboardContainer">
    <div class="scoreboardCon" style="background-image: url('image/gameBg.jpg');">
        <!-- early fixture -->
        <div class="earlyFixture-container row" template="earlyContainerTemplate" hidden>
            <p class="home_team_name col-3"></p>
            <div class="col-4">
                <p class="league_name"></p>
                <p class="start_time"></p>
            </div>
            <p class="away_team_name col-3"></p>
        </div>
        <!-- living fixture -->
        <div class="livingFixture-container row" key="livingContainerTemplate" hidden>
            <table>
                <thead id="livingtableHead">
                    <tr template="scoreBoardHeadTemplate" hidden></tr>
                </thead>
                <tbody id="livingtableBody">
                    <tr template="scoreBoardBodyTemplate_home" hidden></tr>
                    <tr template="scoreBoardBodyTemplate_away" hidden></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="filterBtnContainer">
    <button class="filterBtn active">{{ trans('game.index.all') }}</button>
    <button class="filterBtn">{{ trans('game.index.hot') }}</button>
</div>
<div id="bettingTypeContainer">
    
</div>

<div class="bettingtype-container" template="bettingTypeContainerTemplate" hidden>
    <div class="marketName">
        <p class="market_name"></p>
    </div>
    <div id="marketRateDataTemp" class="marketBetRateContainer betItemDiv">
        
    </div>
</div>

<div class="market-rate d-flex justify-content-between" key="marketBetRateKey" template="marketBetRateTemplate" hidden style="display:none!important;">
    <div class="">
        <span class="market_bet_name"></span>
    </div>
    <div>
        <span class="market_price odd" style="color:#c79e42;"></span>
        <i class="fa-solid fa-lock" style="display: none;"></i>
        <i class="fa-solid fa-caret-up" style="display: none;"></i>
        <i class="fa-solid fa-caret-down" style="display: none;"></i>
    </div>
</div>

<!-- --- -------------start index page---------- ------ -->
<!-- <div template='fixtureCardTemplate' class="indexEachCard" hidden>
    <div class="indexBetCard">
        <div class="timeSpan" key='not-show-baseCon'>
            <span class="timer"></span>
        </div>
        <div class="baseballSpan" key='show-baseCon'>
            <div class="timer"></div>
            <div class="baseCon row m-0">
                <div class="col-1 h-100 p-0"></div>
                <div class="col-6 h-100 p-0" key='base'>
                    <img alt="base">
                </div>
                <div class="col-3 h-100 p-0" key='balls'>
                    <div key='strike'></div>
                    <div key='ball'></div>
                    <div key='out'></div>
                </div>
            </div>
        </div>
        <div class="indexBetCardInfo">
            <div key='homeTeamInfo' class="w-100" style="display: inline-flex;">
                <div class="textOverFlow teamSpan" style="width: 85%;">
                </div>
                <div class="scoreSpan" style="width: 15%;">
                </div>
            </div>
            <div key='awayTeamInfo' class="w-100" style="display: inline-flex;">
                <div class="textOverFlow teamSpan" style="width: 85%;">
                </div>
                <div class="scoreSpan" style="width: 15%;">
                </div>
            </div>
        </div>
        <div class="indexBetCardTable row m-0 text-center">
        </div>
        <div class="otherBetWay" onclick="navToGame($(this))">
            <i class="fa-solid fa-play"></i>
            <p></p>
        </div>
    </div>
    <div class="indexBetCard" key='basketBallQuaterBet'>
        <div class="timeSpan"></div>
        <div class="indexBetCardInfo">
            <div key='homeTeamInfo2'>
                <div class="teamSpan row m-0">
                    <div class="col text-left p-0"></div>
                    <div class="col text-right"></div>
                </div>
            </div>
            <div key='awayTeamInfo2'>
                <div class="teamSpan row m-0">
                    <div class="col text-left p-0"></div>
                    <div class="col text-right"></div>
                </div>
            </div>
        </div>
        <div class="indexBetCardTable row m-0 text-center">
        </div>
    </div>
</div> -->

<!-- <div id="indexContainer">
    <div id="indexContainerLeft">
        <div id="noData" style="display: none;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <p class="mb-0">{{ trans('index.mainArea.nogame') }}</p>
        </div>
    </div>
</div> -->

<!-- early living toggle template -->
<!-- <div class="cateWrapper" template='elToggleTemplate' hidden>
    <div class="catWrapperTitle">
        <span class="elToggleText"></span>
        (<span class="elToggleCount"></span>)
        <span class="elToggleDir" style="float: left;padding-right: 1rem;">
            <i class="fa-solid fa-chevron-down"></i> 
        </span>
    </div>
</div> -->

<!-- league toggle template -->
<!-- <div class="leagueWrapper" template='leagueWrapper' hidden>
    <div class="seriesWrapperTitle">
        <div style="width: 35%;">
            <span class="legToggleDir" style="padding-right: 1rem;">
                <i class="fa-solid fa-chevron-down"></i> 
            </span>
            <span class="legToggleName"></span>
            (<span class="legToggleCount"></span>)
        </div>
        <div class="betLabelContainer">
        </div>
    </div>
    <div class="seriesWrapperContent">
    </div>
</div> -->

<!-- fixture card template -->
<!-- <div template='fixtureCardTemplate' class="indexEachCard" hidden>
    <div class="indexBetCard">
        <div class="timeSpan" key='not-show-baseCon'>
            <span class="timer"></span>
        </div>
        <div class="baseballSpan" key='show-baseCon'>
            <div class="timer"></div>
            <div class="baseCon row m-0">
                <div class="col-1 h-100 p-0"></div>
                <div class="col-6 h-100 p-0" key='base'>
                    <img alt="base">
                </div>
                <div class="col-3 h-100 p-0" key='balls'>
                    <div key='strike'></div>
                    <div key='ball'></div>
                    <div key='out'></div>
                </div>
            </div>
        </div>
        <div class="indexBetCardInfo">
            <div key='homeTeamInfo' class="w-100" style="display: inline-flex;">
                <div class="textOverFlow teamSpan" style="width: 85%;">
                </div>
                <div class="scoreSpan" style="width: 15%;">
                </div>
            </div>
            <div key='awayTeamInfo' class="w-100" style="display: inline-flex;">
                <div class="textOverFlow teamSpan" style="width: 85%;">
                </div>
                <div class="scoreSpan" style="width: 15%;">
                </div>
            </div>
        </div>
        <div class="indexBetCardTable row m-0 text-center">
        </div>
        <div class="otherBetWay" onclick="navToGame($(this))">
            <i class="fa-solid fa-play"></i>
            <p></p>
        </div>
    </div>

    <div class="indexBetCard" key='basketBallQuaterBet'>
        <div class="timeSpan"></div>
        <div class="indexBetCardInfo">
            <div key='homeTeamInfo2'>
                <div class="teamSpan row m-0">
                    <div class="col text-left p-0"></div>
                    <div class="col text-right"></div>
                </div>
            </div>
            <div key='awayTeamInfo2'>
                <div class="teamSpan row m-0">
                    <div class="col text-left p-0"></div>
                    <div class="col text-right"></div>
                </div>
            </div>
        </div>
        <div class="indexBetCardTable row m-0 text-center">
        </div>
    </div>
</div> -->

<!-- bet div template -->
<!-- <div class="col p-0" template='betDiv' hidden>
</div> -->
<!-- betItem template -->
<!-- <div class="betItemDiv row m-0" key='betItemDiv-1' template='betItem-1' hidden>
    <div class="col-7 p-0 text-right">
        <span class="odd"></span>
        <i class="fa-solid fa-lock" style="display: none;"></i>
        <i class="fa-solid fa-caret-up" style="display: none;"></i>
        <i class="fa-solid fa-caret-down" style="display: none;"></i>
    </div>
</div> -->

<!-- <div class="betItemDiv row m-0" key='betItemDiv' template='betItem' hidden>
    <div class="col text-right p-0">
        <span class="bet_name"></span>
    </div>
    <div class="col m-0 row text-right p-0" key='changeCol'>
        <div class="odd col p-0"></div>
        <div class="col text-left p-0">
            <i class="fa-solid fa-lock" style="display: none;"></i>
            <i class="fa-solid fa-caret-up" style="display: none;"></i>
            <i class="fa-solid fa-caret-down" style="display: none;"></i>
        </div>
    </div>
</div> -->
<!-- --- -------------end index page---------- ------ -->

<!-- no data betItem template -->
<div class="betItemDiv row m-0 text-center" key='betItemDiv-no' template='betItem-no' hidden>
</div>


@endsection

@section('styles')
<link href="{{ asset('css/index.css?v=' . $system_config['version']) }}" rel="stylesheet">
<link href="{{ asset('css/game.css?v=' . $current_time) }}" rel="stylesheet">
<!-- <link href="{{ asset('css/game.css?v=' . $system_config['version']) }}" rel="stylesheet"> -->
@endSection

@push('main_js')

<script>
    // 語系
    const langTrans = @json(trans('index'));
    const langTrans2 = @json(trans('game'));

    // websocket用
    const messageQueue = []; // queue to store the package (FIFO)
    var renderInter = null // timer for refresh view layer
    var socket_status = false;
    var ws = null

    
    // 獨贏系列
    const allWinArr = langTrans.priorityArr.allwin // 獨贏系列
    // 讓球系列
    const hcapArr = langTrans.priorityArr.hcap // 獨贏系列
    // 需要把bet_name替換成主客隊名的priority (獨贏讓球)
    const convertTeamPriArr = allWinArr.concat(hcapArr)


    // detect ini ajax
    var isReadyIndexInt = null
    var isReadyIndex = false

    var isReadySportInt = null

	// fixture
	var fixture = null

    // match list data
    var matchListD = {}
    var callMatchListData = { token: token, player: player, sport_id: sport, fixture_id: fixture}
    const matchList_api = '/api/v2/game_index'

    // bet limitation data
    var betLimitationD = {}
    var callLimitationData = {}
    const betLimitation_api = ''

    // game priority and gameTitle
    var mainPriorityArr = null
    var stagePriorityArr = null
    var gameTitle = null


    function setBettypeColor(status) {
        if (status === 1) {
            $('#bettingTypeContainer').css('height', 'calc(100% - 15.5rem)');
            $('.marketName').css('background', '#b8d6d4');
        } else if (status === 2) {
            $('#bettingTypeContainer').css('height', 'calc(100% - 18.5rem)');
            $('.marketName').css('background', '#ffcb9c');
        } else {
            $('#bettingTypeContainer').css('height', 'calc(100% - 7rem)');
        }
    }

    
    function viewIni() { // view ini
        setBettypeColor(matchListD.data.list.status)
        createScoreBoard(matchListD.data);
        Object.entries(matchListD.data.list.market).map(([k, v]) => {
            createMarketContainer(k, v);
            if (v.market_bet) {
                Object.entries(v.market_bet).map((v2, k2) => {
                    v2[1].map((v3, k3) => {
                        createNewElement(v, v3);
                    });
                });
            }
        });
    }

    // ajax update
    function renderView() {
        // update scoreboard home team and away team
        createScoreBoard(matchListD.data);
        // set color of bet title update
        setBettypeColor(matchListD.data.list.status);

        // update content
        Object.entries(matchListD.data.list.market).map(([k, v]) => {
            let bet_div = $(`.bettingtype-container[market_id=${v.market_id}][priority=${v.priority}]`)
            // if not exist -> create
            if( !bet_div ) createMarketContainer(k, v);
            
            if (v.market_bet) {
                v.market_bet.map((v2, k2) => {
                    let bet_item = $(`div[key="marketBetRateKey"][priority="${v.priority}"][market_bet_id="${v2.market_bet_id}"]`)

                    // if not exist -> create / if exists -> update
                    if( !bet_item ) {
                        createNewElement(v, k2, v2);
                    } else {
                        let oldRate = parseFloat(bet_item.attr('bet_rate'))
                        let newRate = parseFloat(v2.price)

                        // rate compare
                        if( oldRate > newRate ) lowerOdd(v.priority, v2.market_bet_id)
                        if( oldRate < newRate ) raiseOdd(v.priority, v2.market_bet_id)

                        // status
                        if( v2.status === 1 ) {
                            bet_item.find('.fa-lock').hide()
                            bet_item.attr('onclick', 'openCal($(this))')
                        } else {
                            bet_item.find('.fa-lock').show()
                            bet_item.removeAttr('onclick')
                        }

                        // set new attribute
                        bet_item.attr('bet_rate', v2.price);
                        bet_item.attr('bet_type', v.market_name);
                        bet_item.attr('bet_name', v2.market_bet_name + ' ' + v2.line);
                        bet_item.attr('bet_name_en', v2.market_bet_name_en);
                        bet_item.attr('line', v2.line);

                        // new rate
                        bet_item.find('.odd').text(v2.price)

                        // market_bet_name
                        switch (v.priority) {
                            case 3: case 203: case 204: case 103: case 104: case 110: case 114: case 118: case 122:
                                bet_item.find('.market_bet_name').text(v2.line);
                                break;
                            case 5: case 6: case 205: case 206: case 105: case 106: case 111: case 115: case 119: case 123:
                                bet_item.find('.market_bet_name').text(v2.market_bet_name + ' ' + v2.line);
                                break;
                            case 7: case 8: case 107: case 108: case 112: case 116: case 120: case 124: case 207: case 208:
                                bet_item.find('.market_bet_name').text(v2.market_bet_name);
                                break;
                            case 1: case 2: case 4: case 101: case 102: case 109: case 113: case 117: case 121: case 201: case 202:
                                if (v2.market_bet_name_en == 1) {
                                    bet_item.find('.market_bet_name').text(matchListD.data.list.home_team_name);
                                } else if (v2.market_bet_name_en == 2) {
                                    bet_item.find('.market_bet_name').text(matchListD.data.list.away_team_name);
                                } else if (v2.market_bet_name_en == 'X') {
                                    bet_item.find('.market_bet_name').text("{{ trans('game.index.tie') }}");
                                }
                                break;
                            default:
                                break;
                        }

                    }
                });
            }
        });


        // check exist bet type content is still exist in the data
        $('#bettingTypeContainer .bettingtype-container').each(function() {
            let priority = parseInt($(this).attr('priority'))
            let result = null
            result = matchListD.data?.list?.market.find(item => (item.priority) === priority)
            if( !result ) $(this).remove()
        });

        // check exist bet item is still exist in the data
        $('#bettingTypeContainer div[key="marketBetRateKey"]').each(function() {
            let priority = parseInt($(this).attr('priority'))
            let market_bet_id = parseInt($(this).attr('market_bet_id'))
            let result = null
            let resultArr = matchListD.data?.list?.market.find(item => (item.priority) === priority)
            if( resultArr.market_bet ) result = resultArr.market_bet.find(item => (item.market_bet_id) === market_bet_id)
            if( !result ) $(this).remove()
        });

    }
   
   
    $(document).ready(function() {
        // ===== DATA LATER =====

        // detest is sport List is ready
        isReadySportInt = setInterval(() => {
            if( isReadyCommon ) {
                sport = parseInt(searchData.sport_id)
                callMatchListData.sport_id = searchData.sport_id // default sport
				callMatchListData.fixture_id = searchData.fixture_id // default fixture
                clearInterval(isReadySportInt)
                caller(matchList_api, callMatchListData, matchListD) // match_list
                setInterval(() => {
                    caller(matchList_api, callMatchListData, matchListD, 1) // update 
                }, 5000);
            }
        }, 100);
        // ===== DATA LATER =====


        // ===== VIEW LATER =====
        // check if api are all loaded every 500 ms 
        isReadyIndexInt = setInterval(() => {
            if (matchListD.status === 1) { isReadyIndex = true; }
            if( isReadyIndex && isReadyCommon) {
                // game priority and gameTitle
                mainPriorityArr = langTrans['sportBetData'][sport]['mainPriorityArr']
                gameTitle = langTrans['sportBetData'][sport]['gameTitle']

                $('#dimmer').dimmer('hide'); // hide loading
                $('#wrap').css('opacity', 1); // show the main content
                viewIni(); // ini data
                renderInter = setInterval(() => { // then refresh every 5 sec
                    // renderView();
                }, 5000);
                clearInterval(isReadyIndexInt); // stop checking


                // websocket -> mark now
                WebSocketDemo(); // ws connection
                setInterval(reconnent, 5000); // detect ws connetion state
                processMessageQueueAsync(); // detect if there's pkg in messageQueue
            }
        }, 500);
        // ===== VIEW LATER =====
    });

    ///game bet loading
    function showLoading() {
        document.getElementById("leftSlideOrderLoadingContainer").classList.remove("hidden");
    }

    function hideLoading() {
        document.getElementById("leftSlideOrderLoadingContainer").classList.add("hidden");
    }

    // websocket
    function WebSocketDemo() {
        console.log('WebSocketDemo')
        if ("WebSocket" in window) {
            try {
                let ws_url = 'wss://broadcast.asgame.net/ws'
                ws = new WebSocket(ws_url); // websocket 連線
                ws.onopen = function() {
                    wsRegisterMatch() // 註冊id
                    socket_status = true; // for reconnection
                };

                // websocket is closed
                ws.onclose = function(event) {
                    console.log('Connection closed with code: ', event.code);
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

    // 重連機制
    function reconnent() {
        if (socket_status === false) {
            WebSocketDemo();
        }
    }


    // ------- game page create market data parent container-----------
    function createMarketContainer(k, v) {
        const bettingTypeContainerTemp = $('div[template="bettingTypeContainerTemplate"]').clone();
        bettingTypeContainerTemp.removeAttr('hidden').removeAttr('template');
        bettingTypeContainerTemp.attr('market_id', v.market_id);
        bettingTypeContainerTemp.attr('priority', v.priority);

        const marketNameElement = bettingTypeContainerTemp.find('.market_name');
        var priority = v.priority;

        marketNameElement.html(`<i class="fa-sharp fa-solid fa-star" style="color: #415a5b; margin-right: 0.5rem;"></i> ${langTrans2.game_priority[sport][priority]}`);
        $('#bettingTypeContainer').append(bettingTypeContainerTemp);
    }
    

    function createNewElement(v, v3) {
        console.log(v, v3)
        const marketBetRateTemp = $('div[template="marketBetRateTemplate"]').clone();
        marketBetRateTemp.removeAttr('hidden').removeAttr('template').removeAttr('style');
        let bet_div = $(`.bettingtype-container[market_id=${v.market_id}][priority=${v.priority}]`)

        marketBetRateTemp.attr('priority', v.priority);
        marketBetRateTemp.attr('fixture_id', matchListD.data.list.fixture_id);
        marketBetRateTemp.attr('market_id', v.market_id);
        marketBetRateTemp.attr('market_bet_id', v3.market_bet_id);
        marketBetRateTemp.attr('bet_rate', v3.price);
        marketBetRateTemp.attr('bet_type', v.market_name);
        marketBetRateTemp.attr('bet_name', v3.market_bet_name + ' ' + v3.line);
        marketBetRateTemp.attr('bet_name_en', v3.market_bet_name_en);
        marketBetRateTemp.attr('line', v3.line);
        marketBetRateTemp.attr('league', matchListD.data.list.league_name);
        marketBetRateTemp.attr('home', matchListD.data.list.home_team_name);
        marketBetRateTemp.attr('away', matchListD.data.list.away_team_name);

        marketBetRateTemp.find('.odd').text(v3.price)

        let pri = parseInt(v.priority)
        console.log(langTrans2.betTypePriority.hcapPriority, langTrans2.betTypePriority.hcapPriority.indexOf(pri))
        switch (pri) {
            case langTrans2.betTypePriority.hcapPriority.indexOf(pri) !== -1:
                marketBetRateTemp.find('.market_bet_name').text(v3.line);
                break;
            case langTrans2.betTypePriority.sizePriority.indexOf(pri) !== -1:
                marketBetRateTemp.find('.market_bet_name').text(v3.market_bet_name + ' ' + v3.line);
                break;
            case langTrans2.betTypePriority.oddEvenPriority.indexOf(pri) !== -1:
                marketBetRateTemp.find('.market_bet_name').text(v3.market_bet_name);
                break;
            case langTrans2.betTypePriority.allWinPriority.indexOf(pri) !== -1:
                if (v3.market_bet_name_en == 1) {
                    marketBetRateTemp.find('.market_bet_name').text(matchListD.data.list.home_team_name + ' ' + v3.line);
                } else if (v3.market_bet_name_en == 2) {
                    marketBetRateTemp.find('.market_bet_name').text(matchListD.data.list.away_team_name + ' ' + v3.line);
                } else if (v3.market_bet_name_en == 'X') {
                    marketBetRateTemp.find('.market_bet_name').text("{{ trans('game.index.tie') }}");
                }
                break;
            default:
                break;
        }

        if (v3.status == 1) {
            marketBetRateTemp.find('.fa-lock').hide();
            marketBetRateTemp.attr('onclick', 'openCal($(this))');
        } else {
            marketBetRateTemp.find('.fa-lock').show();
            marketBetRateTemp.removeAttr('onclick');
        }

        // Append the new element to the correct container
        bet_div.find('.marketBetRateContainer').append(marketBetRateTemp);

        // createdElementKeys.add(marketBetRateId);
    }

    // ------- game page scoreboard function-----------
    function createScoreBoard(data) {
        const earlyContainerTemp = $('div[template="earlyContainerTemplate"]').clone();
        const livingContainerTemp = $('div[template="livingContainerTemplate"]').clone();

        const scoreBoardHeadTemp = $('tr[template="scoreBoardHeadTemplate"]').clone();
        const scoreBoardBodyTemp_home = $('tr[template="scoreBoardBodyTemplate_home"]').clone();
        const scoreBoardBodyTemp_away = $('tr[template="scoreBoardBodyTemplate_away"]').clone();

        // Early fixture (status == 1)
        if (data.list.status == 1) {
            const leagueID = data.list.league_id;
            $(`div[id="${leagueID}"]`).remove();

            earlyContainerTemp.removeAttr('hidden').removeAttr('template');
            earlyContainerTemp.attr('id', data.list.league_id);
            earlyContainerTemp.find('.home_team_name').text(data.list.home_team_name);
            earlyContainerTemp.find('.league_name').text(data.list.league_name);
            earlyContainerTemp.find('.start_time').html(formatDateTimeV2(data.list.start_time));
            earlyContainerTemp.find('.away_team_name').text(data.list.away_team_name);
            $('.scoreboardCon').append(earlyContainerTemp);
        }
        // Living fixture (status == 2)
        if (data.list.status == 2) {
            livingContainerTemp.removeAttr('hidden').removeAttr('template');
            $('div[key="livingContainerTemplate"]').removeAttr('hidden');
            var scorehome = data.list?.scoreboard[1]
            var scoreaway = data.list?.scoreboard[2]

            const headTr = data.list.fixture_id + '_head';
            const bodyTr = data.list.fixture_id + '_body';
            $(`tr[id="${headTr}"]`).remove();
            $(`tr[id="${bodyTr}"]`).remove();

            scoreBoardHeadTemp.removeAttr('hidden').removeAttr('template');
            scoreBoardBodyTemp_home.removeAttr('hidden').removeAttr('template');  
            scoreBoardBodyTemp_away.removeAttr('hidden').removeAttr('template'); 

            scoreBoardHeadTemp.attr('id', headTr);
            scoreBoardBodyTemp_home.attr('id', bodyTr);
            scoreBoardBodyTemp_away.attr('id', bodyTr);

            const gameTitle = langTrans2.scoreBoard.gameTitle[sport]

            // Thead data game title
            let stageStr = ''
            if( sport === 154914 ) {
                data.list.periods.Turn === '1' ? stageStr = langTrans2.scoreBoard.lowerStage : stageStr = langTrans2.scoreBoard.upperStage
            }
            const TeamNameHead = $(`<th style="width:25%;text-align:left;"><div class="setHeightDiv">${langTrans.mainArea.stageArr[sport][data.list.periods.period]}${stageStr}</div></th>`);
            scoreBoardHeadTemp.append(TeamNameHead);

            let baseballShowStage = []
            for (let i = 0; i < gameTitle.length; i++) {
                if( sport === 154914 ) {
                    const scbLen = data.list?.scoreboard[1].length - 1;
                    switch (true) {
                        case scbLen < 6:
                            baseballShowStage = [0, 1, 2, 3, 4, 5, 6];
                        break;
                        case scbLen >= 6 && scbLen <= 9:
                            baseballShowStage = [0, 4, 5, 6, 7, 8, 9];
                        break;
                        case scbLen > 9:
                            baseballShowStage = [0, 7, 8, 9, 10, 11, 12];
                        break;
                        default:
                        break;
                    }

                    if(baseballShowStage.indexOf(i) !== -1) {
                        scoreBoardHeadTemp.append($('<td style="width:10%;text-align:center;"><div class="setHeightDiv">').text(gameTitle[i]));
                    }
                } else {
                    scoreBoardHeadTemp.append($('<td style="width:10%;text-align:center;"><div class="setHeightDiv">').text(gameTitle[i]));
                }
                
            }

            $('#livingtableHead').append(scoreBoardHeadTemp);

            // Home team
            const homeTeamName = $(`<th style="width:25%;text-align:left;"><div class="textOverflowCon">${data.list.home_team_name}</div></th>`);
            scoreBoardBodyTemp_home.append(homeTeamName);

            for (let i = 0; i < gameTitle.length; i++) {
                const scoreValue = scorehome[i];
                const thHome = $('<td style="width:10%;text-align:center;">').text(scoreValue !== undefined ? scoreValue : '-');
                if( !(sport === 154914 && baseballShowStage.indexOf(i) === -1) ) {
                    scoreBoardBodyTemp_home.append(thHome);
                }
            }

            $('#livingtableBody').append(scoreBoardBodyTemp_home);

            // Away team
            const awayTeamName = $(`<th style="width:25%;text-align:left;"><div class="textOverflowCon">${data.list.away_team_name}</div></th>`);
            scoreBoardBodyTemp_away.append(awayTeamName);

            for (let i = 0; i < gameTitle.length; i++) {
                const scoreValue = scoreaway[i];
                const thAway = $('<td style="width:10%;text-align:center;">').text(scoreValue !== undefined ? scoreValue : '-');
                if( !(sport === 154914 && baseballShowStage.indexOf(i) === -1) ) {
                    scoreBoardBodyTemp_away.append(thAway);
                }
            }

            // Append away team after home team to table
            scoreBoardBodyTemp_home.after(scoreBoardBodyTemp_away);

            $('.scoreboardCon').append(livingContainerTemp);
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
        const msg = JSON.parse(message.data); // convert to JSON
        console.log(msg);
        
        // delay_order
        if (msg.action === 'delay_order') {
            clearTimeout(calInter)
            hideLoading();
            closeCal();
            showSuccessToast(msg.order_id);
            refreshBalence();
        }
        // delay_order
    }

    $('#mask, #cancelOrder').click(function() {
        closeCal();
    })

    // 註冊賽事id
    function wsRegisterMatch() {
        const wsMsg = {
            "action": "register",
            "sport_id": sport,
            "player": player,
        }
        console.log('ws match send -> ')
        console.log(wsMsg)
        ws.send(JSON.stringify(wsMsg));
    }



    // 賠率上升
    function raiseOdd(priority, market_bet_id) {
        let target = $(`div[key="marketBetRateKey"][priority="${priority}"][market_bet_id="${market_bet_id}"]`)
        // 先移除現有樣式
        target.removeClass('raiseOdd')
        target.removeClass('lowerOdd')
        target.find('.fa-caret-up').hide()
        target.find('.fa-caret-down').hide()

        // 再加上賠率變化樣式
        target.addClass('raiseOdd')
        target.find('.fa-caret-up').show()

        // 三秒後移除
        setTimeout(() => {
            target.removeClass('raiseOdd')
            target.find('.fa-caret-up').hide()
        }, 3000);
    }
    // 賠率下降
    function lowerOdd(priority, market_bet_id) {
        let target = $(`div[key="marketBetRateKey"][priority="${priority}"][market_bet_id="${market_bet_id}"]`)
        // 先移除現有樣式
        target.removeClass('raiseOdd')
        target.removeClass('lowerOdd')
        target.find('.fa-caret-up').hide()
        target.find('.fa-caret-down').hide()

        // 再加上賠率變化樣式
        target.addClass('lowerOdd')
        target.find('.fa-caret-down').show()

        // 三秒後移除
        setTimeout(() => {
            target.removeClass('lowerOdd')
            target.find('.fa-caret-down').hide()
        }, 3000);
    }



    // 打開投注計算機
    var sendOrderData = {}
    function openCal(e) {
        // 先移除樣式
        $('.leftSlideOrderCard').removeClass('raiseOdd')
        $('.leftSlideOrderCard .fa-caret-up').remove()
        $('.leftSlideOrderCard').removeClass('lowerOdd')
        $('.leftSlideOrderCard .fa-caret-down').remove()

        e.addClass('clickedBet'); 

        let fixture_id = e.attr('fixture_id')
        let market_id = e.attr('market_id')
        let market_bet_id = e.attr('market_bet_id')
        let bet_rate = e.attr('bet_rate')
        let bet_type = e.attr('bet_type')
        let bet_name = e.attr('bet_name')
        let bet_name_en = e.attr('bet_name_en')
        let bet_name_line = e.attr('line')
        let league = e.attr('league')
        let home = e.attr('home')
        let away = e.attr('away')
        let priority = parseInt(e.attr('priority'))

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

        if( convertTeamPriArr.indexOf(priority) === -1 ) {
            $('#leftSlideOrder span[key="bet_name"]').html(bet_name)
        } else {
            // let str = bet_name_en == 1 ? home : away
            // str += ' ' + bet_name_line
            
            let str;
            if (bet_name_en == 1) {
                str = home+= ' ' + bet_name_line;
            } else if (bet_name_en == 'X') {
                str = "{{ trans('game.index.tie') }}";
            } else {
                str = away+= ' ' + bet_name_line;
            }

            $('#leftSlideOrder span[key="bet_name"]').html(str)

        }
        

        $('#leftSlideOrder span[key="odd"]').html(bet_rate)
        $('#leftSlideOrder p[key="series"]').html(league)
        $('#leftSlideOrder span[key="home"]').html(home)
        $('#leftSlideOrder span[key="away"]').html(away)
        $('#leftSlideOrder div[key="slideOrderCard"]').attr('fixture_id', fixture_id)
        $('#leftSlideOrder div[key="slideOrderCard"]').attr('market_id', market_id)
        $('#leftSlideOrder div[key="slideOrderCard"]').attr('market_bet_id', market_bet_id)

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
        closeCal();
    })

    function closeCal() {
        $('#leftSlideOrder').hide("slide", {
            direction: "left"
        }, 500);
        $('#mask').fadeOut()
        // 金額歸零
        $('#moneyInput').val('')
        $('#moneyInput').trigger('change')
        // 移除所有選中樣式
        $('div').removeClass('m_order_on')

        // 左邊選中的剛好鎖起來了 -> 復原
        $('#submitOrder').html(langTrans.bet_area.bet)
        $('#submitOrder').removeClass('disabled')
        $('#submitOrder').removeAttr('disabled')

        $('.market-rate').removeClass('clickedBet');
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
        if (isNaN(inputMoney)) inputMoney = ''
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

    let calInter = null
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

        // Show loading spinner while submitting
        showLoading();

        $.ajax({
            url: '/api/v2/game_bet',
            method: 'POST',
            data: sendOrderData,
            success: function(response) {
                let res = JSON.parse(response)
                calInter = setTimeout(function() {
                    hideLoading();
                    closeCal();
                }, 10000);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('error');
                // hideLoading();
                showErrorToast(jqXHR)
            }
        });
    }


    // 餘額
    async function refreshBalence() {
        $('#refreshIcon').addClass('rotate-animation');
        try {
            await caller(account_api, commonCallData, accountD);
            $('.balance').html(accountD.data.balance);
        } catch (error) {
            console.error('Error:', error);
            // 处理错误情况
        } finally {
            $('#refreshIcon').removeClass('rotate-animation');
        }
    }

  
    // formatedate display month name
    const translations = {
        dateTimezone: @json(trans('game.index.dateTimezone')),
        th: @json(trans('game.index.th')),
    };
    
    const formatDateTimeV2 = (dateTimeString) => {
        const dateTime = new Date(dateTimeString);
        const month = new Intl.DateTimeFormat(translations.dateTimezone, { month: 'long' }).format(dateTime);
        const day = dateTime.getDate();
        const suffix = getDaySuffix(day);
        const hour = dateTime.getHours().toString().padStart(2, '0');
        const minute = dateTime.getMinutes().toString().padStart(2, '0');
        const formattedDate = `${month} ${day}${suffix}`;
        const formattedTime = `${hour}:${minute}`;
        
        return `<span>${formattedDate}</span><span>${formattedTime}</span>`;
    };

    const getDaySuffix = (day) => {
        if (day >= 11 && day <= 13) {
            return translations.th;
        }
        switch (day % 10) {
            case 1:
                return translations.th;
            case 2:
                return translations.th;
            case 3:
                return translations.th;
            default:
                return translations.th;
        }
    };

    // Function to handle filter button clicks
    const filterButtonContainer = document.querySelector('.filterBtnContainer');
    function handleFilterButtonClick(event) {
      if (event.target.classList.contains('filterBtn')) {

        const buttons = filterButtonContainer.querySelectorAll('.filterBtn');

        buttons.forEach(button => button.classList.remove('active')); // Remove the "active" class from all buttons
        event.target.classList.add('active'); // Add the "active" class to the clicked button

      }
    }

    filterButtonContainer.addEventListener('click', handleFilterButtonClick); // Add a click event listener to the container
    

</script>
@endpush