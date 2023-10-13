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
<!-- <div id='searchCondition'>
    {{ trans('common.search_area.search') }}
</div> -->

<div id="scoreboard-con" class="fixture-con" style="color:#ffffff;background-image: url('image/gameBg.jpg');">
    <div class="fixture-card">
        <div id="homeTeamName" template="homeTeamNameTemp"><p class="home_team_name"></p></div>
        <div id="LeagueNameStartTime" template='LeagueNameStartTimeTemp' hidden>
            <p class="leauge_name"></p>
        </div>
        <div id="awayTeamName" template="awayTeamNameTemp"><p class="away_team_name"></p></div>
    </div>
</div>

<div template='fixtureCardTemplate' class="indexEachCard" hidden>
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
    <!-- 籃球單節投注 -->
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
</div>

<div id="indexContainer">
    <div id="indexContainerLeft">
        <!-- no data -->
        <div id="noData" style="display: none;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <p class="mb-0">{{ trans('index.mainArea.nogame') }}</p>
        </div>
    </div>
</div>

<!-- early living toggle template -->
<div class="cateWrapper" template='elToggleTemplate' hidden>
    <div class="catWrapperTitle">
        <span class="elToggleText"></span>
        (<span class="elToggleCount"></span>)
        <span class="elToggleDir" style="float: left;padding-right: 1rem;">
            <i class="fa-solid fa-chevron-down"></i> 
        </span>
    </div>
</div>

<!-- league toggle template -->
<div class="leagueWrapper" template='leagueWrapper' hidden>
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
</div>

<!-- fixture card template -->
<div template='fixtureCardTemplate' class="indexEachCard" hidden>
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
    <!-- 籃球單節投注 -->
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
</div>

<!-- bet div template -->
<div class="col p-0" template='betDiv' hidden>
</div>
<!-- betItem template -->
<div class="betItemDiv row m-0" key='betItemDiv-1' template='betItem-1' hidden>
    <div class="col-7 p-0 text-right">
        <span class="odd"></span>
        <i class="fa-solid fa-lock" style="display: none;"></i>
        <i class="fa-solid fa-caret-up" style="display: none;"></i>
        <i class="fa-solid fa-caret-down" style="display: none;"></i>
    </div>
</div>

<div class="betItemDiv row m-0" key='betItemDiv' template='betItem' hidden>
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
</div>

<!-- no data betItem template -->
<div class="betItemDiv row m-0 text-center" key='betItemDiv-no' template='betItem-no' hidden>
</div>


@endsection



@section('styles')
<link href="{{ asset('css/index.css?v=' . $system_config['version']) }}" rel="stylesheet">
<link href="{{ asset('css/game.css?v=' . $system_config['version']) }}" rel="stylesheet">
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

 

    
    /* ===== DATA LAYER ===== */
    
    /* ===== VIEW LAYER ===== */
    function viewIni() { // view ini
        // loop matchListD to generate html element here
        Object.entries(matchListD.data.list.market).map(([k, v]) => {  // living early toggle
            console.log(k, v)
        })
    }
    /* ===== VIEW LAYER ===== */

    function createFixtureCard(k, league_id, league_name, k3, v3) {
        let card = $('div[template="fixtureCardTemplate"]').clone()
        // 壘包 好壞球 只有 滾球 棒球有
        if( sport === 154914 && v3.status === 2 ) {
            card.find('[key="not-show-baseCon"]').hide()
            card.find('[key="show-baseCon"]').show()
        } else {
            card.find('[key="not-show-baseCon"]').show()
            card.find('[key="show-baseCon"]').hide()
        }

        // 單節選項 只有 滾球 籃球有
        sport === 48242 && v3.status === 2 && v3.periods && v3.periods.period !== 80 ? card.find('div[key="basketBallQuaterBet"]').show() : card.find('div[key="basketBallQuaterBet"]').hide()

        let time = card.find('.timer');
        let home_team_info = card.find('[key="homeTeamInfo"]')
        let away_team_info = card.find('[key="awayTeamInfo"]')
        let market_count = card.find('.otherBetWay p')

        // 跳轉獨立遊戲頁面
        card.find('.otherBetWay').attr('sport_id', sport)
        card.find('.otherBetWay').attr('fixture_id', k3)

        card.attr('id', k3)
        card.attr('cate', k)
        card.attr('status', v3.status)
        card.attr('league_id', league_id)
        time.html(formatDateTime(v3.start_time))
        market_count.html('+' + v3.market_bet_count)
        
        home_team_info.find('.teamSpan').html(v3.home_team_name)
        home_team_info.find('.scoreSpan').html('')
        away_team_info.find('.teamSpan').html(v3.away_team_name)
        away_team_info.find('.scoreSpan').html('')

        // bet area
        createBetArea(mainPriorityArr, v3, k3, league_name, 0, card)

        // ready to start
        if( v3.status === 9 ) {
            time.html(langTrans.mainArea.readyToStart)
        }

        // living
        if( v3.status === 2 ) {
            // score
            if( v3.scoreboard ) {
                home_team_info.find('.scoreSpan').html( v3.scoreboard[1][0] )
                away_team_info.find('.scoreSpan').html( v3.scoreboard[2][0] )
            }
            
            let timerStr = null
            if( v3.periods ) {
                // stage
                timerStr = langTrans.mainArea.stageArr[sport][v3.periods.period]
                // exception baseball
                if( sport === 154914 ) {
                    // stage
                    v3.periods.Turn === '1' ? timerStr += langTrans.mainArea.lowerStage : timerStr += langTrans.mainArea.upperStage

                    // base
                    let baseCont = card.find('img[alt="base"]')
                    let baseText = v3.periods.Bases ? v3.periods.Bases.replaceAll('/','') : '000'
                    baseCont.attr('src', `/image/base/${baseText}.png`)


                    // balls
                    let strike = card.find('div[key="strike"]')
                    let strikeText = v3.periods.Strikes ? v3.periods.Strikes : '0'
                    strike.css('background-image', `url(/image/balls/s${strikeText}.png)`)
                    let ball = card.find('div[key="ball"]')
                    let ballText = v3.periods.Balls ? v3.periods.Balls : '0'
                    ball.css('background-image', `url(/image/balls/b${ballText}.png)`)
                    let out = card.find('div[key="out"]')
                    let outText = v3.periods.Outs ? v3.periods.Outs : '0'
                    out.css('background-image', `url(/image/balls/o${outText}.png)`)
                }
                // stage text
                time.html(timerStr)
            }
            

            if( sport === 48242 ) {
                let card2 = card.find('[key="basketBallQuaterBet"]')
                let home_team_info2 = card2.find('[key="homeTeamInfo2"]')
                let away_team_info2 = card2.find('[key="awayTeamInfo2"]')
                home_team_info2.find('.teamSpan div').eq(0).html(v3.home_team_name)
                home_team_info2.find('.teamSpan div').eq(1).html(timerStr)
                away_team_info2.find('.teamSpan div').eq(0).html(v3.away_team_name)
                away_team_info2.find('.teamSpan div').eq(1).html(timerStr)

                // bet area
                if( v3.periods ) {
                    stagePriorityArr = langTrans['sportBetData'][sport]['stagePriorityArr'][v3.periods.period]
                    if(stagePriorityArr) createBetArea(stagePriorityArr, v3, k3, league_name, 1, card, 1)
                }

            }
        }

        card.removeAttr('hidden')
        card.removeAttr('template')
        let league_toggle_content = $(`#seriesWrapperContent_${k}_${league_id}`)
        league_toggle_content.append(card)
    }

    function createBetArea(priorityArr, v3, k3, league_name, s, card, stageBet = 0) {
        priorityArr.forEach(( i, j ) => {
            let bet_div = $('div[template="betDiv"]').clone()
            let betData = Object.values(v3.list).find(m => m.priority === i)
            bet_div.attr('priority', i)
            if( betData && Object.keys(betData.list).length > 0 ) {
                // 是否有讓方
                let isHcapTeam = null
                // 讓分的priority && line不同 && 有盤口
                j === 1 && (parseFloat(betData.list[0].line) !== parseFloat(betData.list[1].line)) ? isHcapTeam = true : isHcapTeam = false

                Object.entries(betData.list).map(([k4, v4], s) => { 
                    // 判定讓方 -> line值為負
                    if( isHcapTeam && parseFloat(v4.line) < 0 ) {
                        if( stageBet === 0 ) {
                            let index = parseInt(v4.market_bet_name_en) - 1
                            card.find('.teamSpan').eq(index).addClass('hcapTeam') 
                        } else {
                            let index = parseInt(v4.market_bet_name_en) + 1
                            card.find('.teamSpan').eq(index).find('div').eq(0).addClass('hcapTeam') 
                        }
                    }

                    let item = null
                    if (allWinArr.indexOf(i) !== -1 ) {
                        item = $(`div[template="betItem-1"]`).clone()
                    } else {
                        item = $(`div[template="betItem"]`).clone()
                        // 四格的時候調整寬度
                        if( priorityArr.length === 4 ) {
                            item.find('div[key="changeCol"] .col').eq(0).addClass('col-4').removeClass('col')
                        }
                    }

                    // set attribute
                    item.attr('priority', i)
                    item.attr('fixture_id', k3)
                    item.attr('market_id', betData.market_id)
                    item.attr('market_bet_id', v4.market_bet_id)
                    item.attr('bet_rate', v4.price)
                    item.attr('bet_type', betData.market_name)
                    item.attr('bet_name', v4.market_bet_name + ' ' + v4.line)
                    item.attr('bet_name_en', v4.market_bet_name_en)
                    item.attr('line', v4.line)
                    item.attr('league', league_name)
                    item.attr('home', v3.home_team_name)
                    item.attr('away', v3.away_team_name)

                    // rate
                    item.find('.odd').html(v4.price)
                    // 按照不同體育種類、玩法 顯示相對應內容
                    switch ( i ) {
                        case 3:case 203:case 204:case 103:case 104:case 110:case 114:case 118:case 122:  // 讓球
                            item.find('.bet_name').html( v4.line )
                            break;
                        case 5:case 205:case 206:case 105:case 106:case 111:case 115:case 119:case 123: // 大小
                            item.find('.bet_name').html(v4.market_bet_name + '  ' + v4.line)
                            break;
                        case 7:case 107:case 112:case 116:case 120:case 124: // 單雙
                            item.find('.bet_name').html( v4.market_bet_name )
                            break;
                        default: // 獨贏
                            break;
                    }

                    if( v4.status === 1 ) {
                        item.find('.fa-lock').hide()
                        item.attr('onclick', 'openCal($(this))')
                    } else {
                        item.find('.fa-lock').show()
                        item.removeAttr('onclick')
                    }

                    item.removeAttr('hidden')
                    item.removeAttr('template')
                    bet_div.append(item)

                })
            } else {
                for (let j = 0; j < 2; j++) {
                    let item = null
                    if (allWinArr.indexOf(i) !== -1 ) {
                        item = $(`div[template="betItem-1"]`).clone()
                    } else {
                        item = $(`div[template="betItem"]`).clone()
                        // 四格的時候調整寬度
                        if( priorityArr.length === 4 ) {
                            item.find('div[key="changeCol"] .col').eq(0).addClass('col-4').removeClass('col')
                        }
                    }

                    item.find('.fa-lock').show()
                    item.removeAttr('onclick')

                    item.removeAttr('hidden')
                    item.removeAttr('template')
                    bet_div.append(item)
                }
            }

            // 足球 讓球、大小 補空格
            if( sport === 6046 && allWinArr.indexOf(i) === -1 ) {
                let item = $('div[template="betItem-no"]').clone()
                item.removeAttr('hidden')
                item.removeAttr('template')
                bet_div.append(item)
            }

            bet_div.removeAttr('hidden')
            bet_div.removeAttr('template')
            card.find('.indexBetCardTable').eq(s).append(bet_div)
        });
    }
   
    $(document).ready(function() {
        // ===== DATA LATER =====

        // detest is sport List is ready
        isReadySportInt = setInterval(() => {
            if( isReadyCommon ) {
                callMatchListData.sport_id = sport // default sport
				callMatchListData.fixture_id = searchData.fixture_id // default fixture
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
                mainPriorityArr = langTrans['sportBetData'][sport]['mainPriorityArr']
                gameTitle = langTrans['sportBetData'][sport]['gameTitle']

                $('#dimmer').dimmer('hide'); // hide loading
                $('#wrap').css('opacity', 1); // show the main content
                viewIni(); // ini data
                renderInter = setInterval(() => { // then refresh every 5 sec
                    // renderView()
                }, 5000);
                clearInterval(isReadyIndexInt); // stop checking


                // websocket -> mark now
                WebSocketDemo(); // ws connection
                setInterval(reconnent, 5000); // detect ws connetion state
                processMessageQueueAsync(); // detect if there's pkg in messageQueue

                renderViewV2()
            }
        }, 500);

        
        // ===== DATA LATER =====
    });

    // render view layer here
    function renderViewV2() {
		console.log(matchListD)
        Object.entries(matchListD.data.series).map(([sk, sv]) => { 
            createScoreBoard(sk, sv)
        })
    }

    function createScoreBoard(sk, sv) {
        console.log("render data")
        console.log(sk, sv)
        let LeagueNameStartTime = $('div[template="LeagueNameStartTimeTemp"]').clone()

        LeagueNameStartTime.removeAttr('hidden')
        LeagueNameStartTime.removeAttr('template')
        LeagueNameStartTime.find('.leauge_name').html(sk.sv)

        $(`#homeTeamName`).after(LeagueNameStartTime)
    }

    // function createScoreBoard(sk) {
    //     let LeagueNameStartTime = $('div[template="LeagueNameStartTimeTemp"]').clone()
    //     let homeTeamName = $('div[template="homeTeamNameTemp"]').clone()
    //     let awayTeamName = $('div[template="awayTeamNameTemp"]').clone()

    //     LeagueNamSeStartTime.removeAttr('hidden')
    //     LeagueNameStartTime.removeAttr('template')
    //     homeTeamName.removeAttr('hidden')
    //     homeTeamName.removeAttr('template')
    //     awayTeamName.removeAttr('hidden')
    //     awayTeamName.removeAttr('template')

    //     let leauge_name_startTime = LeagueNameStartTime.find('[key="LeagueNameStartTimeCard"]')

    //     // leauge_name_startTime.find('.leauge_name').html(sv.name)
    //     leauge_name_startTime.find('.start_time').html(sv.start_time)

    //     homeTeamName.find('.home_team_name').html(sv.home_team_name)
    //     awayTeamName.find('.away_team_name').html(sv.away_team_name)

    //     $(`#LeagueNameStartTime`).before(homeTeamName)
    //     $(`#homeTeamName`).after(LeagueNameStartTime)
    //     $(`#LeagueNameStartTime`).after(awayTeamName)
    // }



    // 跳轉獨立賽事頁
    function navToGame(e) {
        let sport_id = e.attr('sport_id')   
        let fixture_id = e.attr('fixture_id')
        

        const queryParams = {};
        queryParams.sport_id = sport_id
        queryParams.fixture_id = fixture_id

        const queryString = new URLSearchParams(queryParams).toString();
        const urlWithQuery = `/game?${queryString}`;
        console.log(urlWithQuery)
        window.location.href = urlWithQuery
    }

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
    
    // render view layer here
    function renderView() {
		console.log(matchListD)
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
                        if( isSwitchCate ) {
                            if( !isCateExist ) createCate(k, v)
                            if( !isLeagueExist ) createLeague(k, k2, v2)
                            let parentNode =$(`#seriesWrapperContent_${k}_${v2.league_id}`)
                            let livingNode = $(`#${k3}`)
                            livingNode.prependTo(parentNode); // move to corrsponding cate and league
                            card.attr('cate', k)
                            card.attr('status', v3.status)
                        }   

                        // 玩法統計
                        card.find('.otherBetWay p').html('+' + v3.market_bet_count)

                        // 壘包 好壞球 只有 滾球 棒球有
                        if( sport === 154914 && v3.status === 2 ) {
                            card.find('[key="not-show-baseCon"]').hide()
                            card.find('[key="show-baseCon"]').show()
                        } else {
                            card.find('[key="not-show-baseCon"]').show()
                            card.find('[key="show-baseCon"]').hide()
                        }

                        // 單節選項 只有 滾球 籃球有
                        sport === 48242 && v3.status === 2 && v3.periods && v3.periods.period !== 80 ? card.find('div[key="basketBallQuaterBet"]').show() : card.find('div[key="basketBallQuaterBet"]').hide()

                        // ready to start
                        if( v3.status === 9 ) time.html(langTrans.mainArea.readyToStart)

                        // living
                        if( v3.status === 2 ) {
                            // score
                            if( v3.scoreboard ) {
                                let homeScore = home_team_info.find('.scoreSpan')
                                let awayScore = away_team_info.find('.scoreSpan')
                                let nowHomeScore = parseInt(homeScore.html())
                                let nowAwayScore = parseInt(awayScore.html())
                                let updateHome = parseInt(v3.scoreboard[1][0])
                                let updateAway = parseInt(v3.scoreboard[2][0])
                                if( updateHome > nowHomeScore ) homeScore.addClass('raiseScore')
                                if( updateAway > nowAwayScore ) awayScore.addClass('raiseScore')

                                setTimeout(() => {
                                    homeScore.removeClass('raiseScore')
                                    awayScore.removeClass('raiseScore')
                                }, 3000);

                                homeScore.html( v3.scoreboard[1][0] )
                                awayScore.html( v3.scoreboard[2][0] )
                            }

                            // stage
                            let timerStr = null
                            if( v3.periods ) {
                                timerStr = langTrans.mainArea.stageArr[sport][v3.periods.period]
                                // bet data
                                renderBetArea(mainPriorityArr, v3, k3)
                                // exception baseball
                                if( sport === 154914 ) {
                                    v3.periods.Turn === '1' ? timerStr += langTrans.mainArea.lowerStage : timerStr += langTrans.mainArea.upperStage

                                    // base
                                    let baseCont = card.find('img[alt="base"]')
                                    let baseText = v3.periods.Bases ? v3.periods.Bases.replaceAll('/','') : '000'
                                    baseCont.attr('src', `/image/base/${baseText}.png`)

                                    // balls
                                    let strike = card.find('div[key="strike"]')
                                    let strikeText = v3.periods.Strikes ? v3.periods.Strikes : '0'
                                    strike.css('background-image', `url(/image/balls/s${strikeText}.png)`)
                                    let ball = card.find('div[key="ball"]')
                                    let ballText = v3.periods.Balls ? v3.periods.Balls : '0'
                                    ball.css('background-image', `url(/image/balls/b${ballText}.png)`)
                                    let out = card.find('div[key="out"]')
                                    let outText = v3.periods.Outs ? v3.periods.Outs : '0'
                                    out.css('background-image', `url(/image/balls/o${outText}.png)`)
                                }

                                time.html(timerStr)
                            }

                            // exception basketball
                            if( sport === 48242 ) {
                                let card2 = card.find('[key="basketBallQuaterBet"]')

                                // 換節了 重新渲染單節投注區塊
                                if( v3.periods ) {
                                    newStagePriorityArr = langTrans['sportBetData'][sport]['stagePriorityArr'][v3.periods.period]

                                    if( newStagePriorityArr && !stagePriorityArr.every((value, index) => value === newStagePriorityArr[index]) ) {
                                        stagePriorityArr = newStagePriorityArr
                                        card.find('.indexBetCardTable').eq(1).html('')
                                        createBetArea(stagePriorityArr, v3, k3, v2.league_name, 1, card)
                                    }
                                }

                                let home_team_info2 = card2.find('[key="homeTeamInfo2"]')
                                let away_team_info2 = card2.find('[key="awayTeamInfo2"]')

                                home_team_info2.find('.teamSpan div').eq(0).html(v3.home_team_name)
                                home_team_info2.find('.teamSpan div').eq(1).html(timerStr)
                                away_team_info2.find('.teamSpan div').eq(0).html(v3.away_team_name)
                                away_team_info2.find('.teamSpan div').eq(1).html(timerStr)


                                if( stagePriorityArr ) renderBetArea(stagePriorityArr, v3, k3, 1)
                            }
                            
                        }
                       
                        function renderBetArea(priorityArr, v3, k3, stageBet = 0) {
                            console.log(priorityArr)
                            priorityArr.forEach(( i, j ) => {
                                let bet_div = $(`#${k3} div[priority=${i}]`)
                                let betData = Object.values(v3.list).find(m => m.priority === i)
                                let item = null
                                if( betData && Object.keys(betData.list).length > 0 ) {
                                    // 是否有讓方
                                    let isHcapTeam = null
                                    // 讓分的priority && line不同 && 有盤口
                                    j === 1 && (parseFloat(betData.list[0].line) !== parseFloat(betData.list[1].line)) ? isHcapTeam = true : isHcapTeam = false

                                    Object.entries(betData.list).map(([k4, v4], s) => { 
                                        // 先取消樣式
                                        bet_div.find('div').removeClass('hcapTeam')
                                        // 判定讓方 -> line值為負
                                        if( isHcapTeam && parseFloat(v4.line) < 0 ) {
                                            if( stageBet === 0 ) {
                                                let index = parseInt(v4.market_bet_name_en) - 1
                                                bet_div.find('.teamSpan').eq(index).addClass('hcapTeam') 
                                            } else {
                                                let index = parseInt(v4.market_bet_name_en) + 1
                                                bet_div.find('.teamSpan').eq(index).find('div').eq(0).addClass('hcapTeam') 
                                            }
                                        }

                                        item = bet_div.find('.betItemDiv').eq(s)
                                        // old attribute
                                        let market_bet_id = item.attr('market_bet_id')
                                        let price = item.attr('bet_rate')
                                        let isSelected = item.hasClass('m_order_on')

                                        // 判斷盤口存在+是否有改變且狀態為1
                                        if( market_bet_id && market_bet_id.toString() === (v4.market_bet_id).toString() && v4.status === 1 ) {
                                            // 判斷賠率是否有改變
                                            if( parseFloat(price) > parseFloat(v4.price) ) {
                                                // 賠率下降
                                                lowerOdd(k3, betData.market_id, v4.market_bet_id)
                                            }
                                            if( parseFloat(price) < parseFloat(v4.price) ) {
                                                // 賠率上升
                                                raiseOdd(k3, betData.market_id, v4.market_bet_id)
                                            }
                                        } 

                                        // set attribute
                                        if( isSelected ) $('div[key="slideOrderCard"]').attr('market_bet_id', v4.market_bet_id)
                                        item.attr('priority', i)
                                        item.attr('fixture_id', k3)
                                        item.attr('market_id', betData.market_id)
                                        item.attr('market_bet_id', v4.market_bet_id)
                                        item.attr('bet_rate', v4.price)
                                        item.attr('bet_type', betData.market_name)
                                        item.attr('bet_name', v4.market_bet_name + ' ' + v4.line)
                                        item.attr('bet_name_en', v4.market_bet_name_en)
                                        item.attr('line', v4.line)
                                        item.attr('league', v2.league_name)
                                        item.attr('home', v3.home_team_name)
                                        item.attr('away', v3.away_team_name)

                                        // rate
                                        item.find('.odd').html(v4.price)
                                        // 賦值
                                        switch ( i ) {
                                            case 3:case 203:case 204:case 103:case 104:case 110:case 114:case 118:case 122:  // 讓球
                                                item.find('.bet_name').html( v4.line )
                                                break;
                                            case 5:case 205:case 206:case 105:case 106:case 111:case 115:case 119:case 123: // 大小
                                                item.find('.bet_name').html(v4.market_bet_name + '  ' + v4.line)
                                                break;
                                            case 7:case 107:case 112:case 116:case 120:case 124: // 單雙
                                                item.find('.bet_name').html( v4.market_bet_name )
                                                break;
                                            default: // 獨贏
                                                break;
                                        }

                                        // 左邊投注區塊
                                        let calBetNameStr = ''
                                        let home = item.attr('home')
                                        let away = item.attr('away')
                                        if( convertTeamPriArr.indexOf(i) === -1 ) {
                                            calBetNameStr = v4.market_bet_name + ' ' + v4.line
                                        } else {
                                            calBetNameStr = v4.market_bet_name_en == 1 ? home + ' ' + v4.line : away + ' ' + v4.line
                                        }
                                        $(`div[key="slideOrderCard"][fixture_id="${k3}"][market_bet_id="${v4.market_bet_id}"] span[key="bet_name"]`).html(calBetNameStr)

                                        // 狀態 鎖頭
                                        if( v4.status === 1 ) {
                                            item.find('.fa-lock').hide()
                                            item.attr('onclick', 'openCal($(this))')

                                            // 左邊選中的剛好鎖起來了 -> 復原
                                            if( $(`div[key="slideOrderCard"][fixture_id="${k3}"][market_bet_id="${v4.market_bet_id}"]`).length > 0 ) {
                                                $('#submitOrder').html(langTrans.bet_area.bet)
                                                $('#submitOrder').removeClass('disabled')
                                                $('#submitOrder').removeAttr('disabled')
                                            }
                                        } else {
                                            item.find('.fa-lock').show()
                                            item.removeAttr('onclick')

                                            // 左邊選中的剛好鎖起來了
                                            if( $(`div[key="slideOrderCard"][fixture_id="${k3}"][market_bet_id="${v4.market_bet_id}"]`).length > 0 ) {
                                                $('#submitOrder').html(langTrans.bet_area.disabled)
                                                $('#submitOrder').addClass('disabled')
                                                $('#submitOrder').attr('disabled', true)
                                            }
                                        }
                                    })
                                } else {
                                    let k = 2
                                    if( sport === 6046 ) k = 3
                                    for (let j = 0; j < k; j++) {
                                        let item = bet_div.find('.betItemDiv').eq(j)

                                        item.find('.fa-lock').show()
                                        item.removeAttr('onclick')
                                    }
                                }
                            });
                        }
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

    // remove fixture
    function closeFixture(id) {
        $(`#${id}`).hide(1000)
        setTimeout(() => {
            $(`#${id}`).remove()
        }, 1000);
    }


    // 大分類收合
    function toggleCat(key) {
        var $toggleContent = $(`#toggleContent_${key}`);
        var $icon = $(`#toggleContent_${key} #catWrapperTitle_${key}_dir i`);
        
        // 获取当前高度
        var currentHeight = $toggleContent.height().toFixed(2);
        if (currentHeight == 37.8) {
            // 如果高度为 49px，则展开
            $toggleContent.css('overflow', 'auto');
            $toggleContent.animate({ height: $toggleContent[0].scrollHeight }, 700, function() {
                // 动画完成后，将高度设置为 'auto'
                $toggleContent.removeAttr('style')
            });
        } else {
            // 如果高度不是 49px，则收起
            $toggleContent.css('overflow', 'hidden');
            $toggleContent.animate({ height: '37.8px' }, 700);
        }

        // 切换图标方向
        if ($icon.hasClass('fa-chevron-down')) {
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        } else {
            $icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        }
    }



    // 聯賽分類收合
    function toggleSeries( key ) {
        $('#seriesWrapperContent_' + key).slideToggle( 700 );
        if($('#seriesWrapperTitle_' + key + '_dir i').hasClass('fa-chevron-down')) {
            $('#seriesWrapperTitle_' + key + '_dir i').removeClass('fa-chevron-down')
            $('#seriesWrapperTitle_' + key + '_dir i').addClass('fa-chevron-right')
            $('#seriesWrapperTitle_' + key + ' .betLabelContainer').hide()
        } else {
            $('#seriesWrapperTitle_' + key + '_dir i').addClass('fa-chevron-down')
            $('#seriesWrapperTitle_' + key + '_dir i').removeClass('fa-chevron-right')
            $('#seriesWrapperTitle_' + key + ' .betLabelContainer').show()
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
        // 先移除現有樣式
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').removeClass('raiseOdd')
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').removeClass('lowerOdd')
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-up').hide()
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-down').hide()

        // 再加上賠率變化樣式
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').addClass('raiseOdd')
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-up').show()
        
        // 三秒後移除
        setTimeout(() => {
            $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').removeClass('raiseOdd')
            $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-up').hide()
        }, 3000);
    }
    // 賠率下降
    function lowerOdd(fixture_id, market_id, market_bet_id) {
        // console.log('lowerOdd')
        // 先移除現有樣式
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').removeClass('raiseOdd')
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').removeClass('lowerOdd')
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-up').hide()
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-down').hide()


        // 再加上賠率變化樣式
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').addClass('lowerOdd')
        $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-down').show()

        // 三秒後移除
        setTimeout(() => {
            $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + ']').removeClass('lowerOdd')
            $('div[fixture_id=' + fixture_id + '][market_id=' + market_id + '][market_bet_id=' + market_bet_id + '] .fa-caret-down').hide()
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
        // 先移除樣式
        $('.leftSlideOrderCard').removeClass('raiseOdd')
        $('.leftSlideOrderCard .fa-caret-up').remove()
        $('.leftSlideOrderCard').removeClass('lowerOdd')
        $('.leftSlideOrderCard .fa-caret-down').remove()


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
            let str = bet_name_en == 1 ? home : away
            str += ' ' + bet_name_line
            $('#leftSlideOrder span[key="bet_name"]').html(str)
        }

        $('#leftSlideOrder span[key="bet_type"]').html(bet_type)
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

    // 統計
    function statistics() {
        $('#indexContainer .elToggleCount').each(function() {
            let id = $(this).attr('id').split('_')[1]
            let count = $('#toggleContent_' + id).find('.indexEachCard').length
            $(this).html(count)
            if( count === 0 ) $(this).closest('.cateWrapper').hide()
        })

        $('#indexContainer .legToggleCount').each(function() {
            let idArr = $(this).attr('id').split('_')
            let id = `seriesWrapperContent_${idArr[1]}_${idArr[2]}` 
            let count = $('#' + id).find('.indexEachCard').length
            $(this).html(count)
            if( count === 0 ) $(this).closest('.leagueWrapper').remove()
        })

        // is no data
        if( $('#indexContainer .indexEachCard').length === 0 ) {
            $('#noData').show()
        } else {
            $('#noData').hide()
        }
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

    formatDateTime = (dateTimeString) => {
        const dateTime = new Date(dateTimeString);
        const month = (dateTime.getMonth() + 1).toString().padStart(2, '0'); // Get month (0-based index), add 1, and pad with '0' if needed
        const day = dateTime.getDate().toString().padStart(2, '0'); // Get day and pad with '0' if needed
        const hour = dateTime.getHours().toString().padStart(2, '0'); // Get hours and pad with '0' if needed
        const minute = dateTime.getMinutes().toString().padStart(2, '0'); // Get minutes and pad with '0' if needed
        return `${month}-${day} ${hour}:${minute}`;
    }

</script>
@endpush