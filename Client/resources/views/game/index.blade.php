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
                <div class="col-12"><span key='bet_status'></span><span key='bet_type'></span></div>
                <div class="col-8 mb-2 mt-2"><span key='bet_name'></span></div>
                <div class="col-4 mb-2 mt-2 text-right">
                    <span key='odd' class="odd"></span>
                </div>
                <div class="col-12 mb-2">
                    <input class="w-100 text-right" id="moneyInput" autocomplete="off" inputmode="numeric" oninput="this.value = this.value.replace(/\D+/g, '')" placeholder="" >
                </div>
                <div class="col-12 m-0 text-red"><p class="mb-0" id="betPrompt"></p></div>
                <div class="col-6 mb-2">{{ trans('index.bet_area.maxwin') }}</div>
                <div class="col-6 mb-2 text-right" id="maxWinning" style="overflow: hidden;">0.00</div>
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
<!-- early & living scoreboard-->
    <!-- template clone -->
        <!-- early fixture -->
    <div class="template-con" style="display:none;">
        <div class="earlyFixture-container row" template="earlyContainerTemplate" hidden>
            <p class="home_team_name col-3"></p>
            <div class="col-4">
                <p class="league_name"></p>
                <p class="start_time"></p>
            </div>
            <p class="away_team_name col-3"></p>
        </div>
        <!-- living fixture -->
        <div class="livingFixture-container row" template="livingContainerTemplate-con" hidden>
            <table>
                <thead key="livingtableHead-con">
                    <tr template="scoreBoardHeadTemplate-con" hidden></tr>
                </thead>
                <tbody key="livingtableBody-con">
                    <tr template="scoreBoardBodyTemplate_home-con" hidden></tr>
                    <tr template="scoreBoardBodyTemplate_away-con" hidden></tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- template clone -->

<div id="scoreboardContainer">
    <i class="fa-solid fa-arrow-left" id="backIcon" onclick="window.history.back();"></i>
    <div class="scoreboardCon" style="background-image: url('image/gameBg.jpg');">
        <div class="swiper-wrapper  d-flex w-100">
            <div class="early-fixture-con" style="width:100%!important;"></div>
            <div class="living-fixture-con" style="width:100%!important;"></div>
            <div class="navigation-controls">
                <button onclick="prevSlide()" id="prevBTN" class="disabled-btn"><i class="fa-solid fa-chevron-up fa-rotate-270"></i></button>
                <button onclick="nextSlide()" id="nextBTN"><i class="fa-solid fa-chevron-up fa-rotate-90"></i></button>
                <ul class="pagination">
                    <li class="pgntn-bullet-1"><i class="fa-solid fa-circle"></i></li>
                    <li class="pgntn-bullet-2"><i class="fa-solid fa-circle"></i></li>
                    <li class="pgntn-bullet-3"><i class="fa-solid fa-circle"></i></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="filterBtnContainer">
    <button class="filterBtn active" key='all'>{{ trans('game.index.all') }}</button>
    <button class="filterBtn" key='full'>{{ trans('game.index.full') }}</button>
    <button class="filterBtn" key='half'>{{ trans('game.index.half') }}</button>
    <button class="filterBtn" key='1' mark='single' style="display: none;">{{ trans('game.index.1qtr') }}</button>
    <button class="filterBtn" key='2' mark='single' style="display: none;">{{ trans('game.index.2qtr') }}</button>
    <button class="filterBtn" key='3' mark='single' style="display: none;">{{ trans('game.index.3qtr') }}</button>
    <button class="filterBtn" key='4' mark='single' style="display: none;">{{ trans('game.index.4qtr') }}</button>
</div>

<div id="bettingTypeContainer"></div>

<div class="bettingtype-container" template="bettingTypeContainerTemplate" hidden>
    <div class="marketName">
        <p class="market_name"></p>
    </div>
    <div id="marketRateDataTemp" class="marketBetRateContainer betItemDiv"></div>
</div>

<div class="market-rate d-flex" key="marketBetRateKey" template="marketBetRateTemplate" hidden style="display:none!important;">
    <div class="col-8 d-flex">
        <div class="market_bet_name"></div>
        <div class="line"></div>
    </div>
    <div class="col-4 text-right">
        <span class="market_price odd"></span>
        <i class="fa-solid fa-lock" style="display: none;"></i>
        <i class="fa-solid fa-caret-up" style="display: none;"></i>
        <i class="fa-solid fa-caret-down" style="display: none;"></i>
    </div>
</div>

<!-- no data betItem template -->
<div class="betItemDiv row m-0 text-center" key='betItemDiv-no' template='betItem-no' hidden>
</div>


@endsection

@section('styles')
<link href="{{ asset('css/index.css?v=' . $system_config['version']) }}" rel="stylesheet">
<link href="{{ asset('css/game.css?v=' . $system_config['version']) }}" rel="stylesheet">

@push('main_js')

<script>
    // 語系
    const langTrans = @json(trans('index'));
    const commonLangTrans = @json(trans('common'));
    const gameLangTrans = @json(trans('game'));

    // websocket用
    const messageQueue = []; // queue to store the package (FIFO)
    var renderInter = null // timer for refresh view layer
    var socket_status = false;
    var ws = null

    
    // 獨贏系列
    const allWinArr = commonLangTrans.priorityArr.allwin // 獨贏系列
    // 讓球系列
    const hcapArr = commonLangTrans.priorityArr.hcap // 獨贏系列
    // 需要把bet_name替換成主客隊名的priority (獨贏讓球)
    const convertTeamPriArr = allWinArr.concat(hcapArr)


    // detect ini ajax
    var isReadyIndexInt = null
    var isReadyIndex = false
    var isReadySportInt = null


    // match list data
    var matchListD = {}
    var fixtureData = {}
    var fixture_id = null
    var league_id = null
    var league_name = null
    var callMatchListData = { token: token, player: player, sport_id: sport, fixture_id: fixture_id}
    const matchList_api = '/api/v2/game_index_b'

    // bet limitation data
    var betLimitationD = {}

    // game priority and gameTitle
    var mainPriorityArr = null
    var stagePriorityArr = null
    var gameTitle = null

    function setBettypeColor(status) {
        status === 2 ? $('.marketName').css('background', '#ffcb9c') : $('.marketName').css('background', '#b8d6d4')
    }

    function viewIni() { // view ini 
        fixture_id = parseInt(searchData.fixture_id)
        fixtureData = matchListD.data.list[searchData.fixture_id]
        league_id = matchListD.data['league_id']
        league_name = matchListD.data['league_name']
        setBettypeColor(fixtureData.status)
        createScoreBoard(fixtureData);

        // ===== 玩法排序 (全場->半場->單節) =====
        const catePriority = gameLangTrans.catePriority
        Object.entries(fixtureData.list).map(([marketk, market]) => {
            if( catePriority.full.indexOf(market.priority) !== -1 ) market.cateOrder = 1
            if( catePriority.half.indexOf(market.priority) !== -1 ) market.cateOrder = 2
            if( catePriority.full.indexOf(market.priority) === -1 && catePriority.half.indexOf(market.priority) === -1 ) market.cateOrder = 3
        })
        
        // ===== 玩法排序 (全場->半場->單節) =====

        Object.entries(fixtureData.list).sort(([, marketA], [, marketB]) => marketA.cateOrder - marketB.cateOrder).map(([k, v]) => {
            // 冰球 美足 略過 單雙
            if( sport === 35232 && v.priority === 304 || sport === 35232 && v.priority === 308 ) return;
            if( sport === 131506 && v.priority === 407 || sport === 131506 && v.priority === 408 ) return;

            createMarketContainer(k, v);
            if (v.list) {
                const sortedKeys = Object.keys(v.list)
                // 遍历排序后的数组
                sortedKeys.forEach((key) => {
                    v.list[key].forEach((v3) => {
                        createNewElement(v, v3, v.list[key].length, key);
                    });
                });
            }
        });

        if (Object.keys(fixtureData.list).length === 0) {
            noData();
        }

        // 沒有盤口的tab隱藏
        let fullCounting = 0
        gameLangTrans.catePriority.full.map( v => { 
            fullCounting += $(`.bettingtype-container[priority=${v}]`).length
        })
        fullCounting === 0 ? $('.filterBtn[key="full"]').hide() : $('.filterBtn[key="full"]').show()

        let halfCounting = 0
        gameLangTrans.catePriority.half.map( v => { 
            halfCounting += $(`.bettingtype-container[priority=${v}]`).length
        })
        halfCounting === 0 ? $('.filterBtn[key="half"]').hide() : $('.filterBtn[key="half"]').show()


        // 籃球 單節tab篩選
        if(sport === 48242) {
            for (const [key, value] of Object.entries(gameLangTrans.catePriority.single[sport])) {
                for (const subValue of value) {
                    if ($(`.bettingtype-container[priority=${subValue}]`).length > 0) {
                        $(`.filterBtn[key=${key}]`).show();
                    }
                }
            }
        }
    }

    // ajax update
    function renderView() {
        fixtureData = matchListD.data.list[searchData.fixture_id]
        setBettypeColor(fixtureData.status)
        createScoreBoard(fixtureData);

        let cate = fixtureData.status === 1 ? 'early' : 'living'

        // if refresh no data    
        if (Object.keys(fixtureData.list).length === 0) {
            noData();
            return;
        }

        // nodata
        $('#bettingTypeContainer .noDataContainer').remove()

        // update content
        // check exist bet type content is still exist in the data
        $('#bettingTypeContainer .bettingtype-container').each(function() {
            let priority = parseInt($(this).attr('priority'))
            let result = null
            result = Object.entries(fixtureData?.list)?.find(item => item.priority === priority);
            if( !result ) {
                $(this).remove()
            }
        });

        // check exist bet item is still exist in the data
        $('#bettingTypeContainer div[key="marketBetRateKey"]').each(function() {
            const priority = parseInt($(this).attr('priority'));
            const line = $(this).attr('line')
            const market_bet_id = parseInt($(this).attr('market_bet_id'))
            const resultArr = Object.entries(fixtureData?.list)?.find(item => item.priority === priority);

            // 遍历 market_bet 属性
            var result = Object.values(resultArr.list).find(marketBets => {
                // 在每个 market_bet 数组中查找匹配的 market_bet_id
                return marketBets.find(item => item.market_bet_id === market_bet_id);
            });
            
            if (!result) {
                $(this).remove();
            }
        });

        // ===== 玩法排序 (全場->半場->單節) =====
        const catePriority = gameLangTrans.catePriority
        Object.entries(fixtureData.list).map(([marketk, market]) => {
            if( catePriority.full.indexOf(market.priority) !== -1 ) market.cateOrder = 1
            if( catePriority.half.indexOf(market.priority) !== -1 ) market.cateOrder = 2
            if( catePriority.full.indexOf(market.priority) === -1 && catePriority.half.indexOf(market.priority) === -1 ) market.cateOrder = 3
        })
        // ===== 玩法排序 (全場->半場->單節) =====
        Object.entries(fixtureData.list).sort(([, marketA], [, marketB]) => marketA.cateOrder - marketB.cateOrder).map(([k, v]) => {
            // 冰球 美足 略過 單雙
            if( sport === 35232 && v.priority === 304 || sport === 35232 && v.priority === 308 ) return;
            if( sport === 131506 && v.priority === 407 || sport === 131506 && v.priority === 408 ) return;

            let bet_div = $(`.bettingtype-container[priority=${v.priority}]`)

            // if not exist -> create
            if( bet_div.length === 0 ) createMarketContainer(k, v);
            
            if (v.list) {
                const sortedKeys = Object.keys(v.list)
                // 遍历排序后的数组
                sortedKeys.forEach((key, p) => {
                    v.list[key].forEach((v3, s) => {
                        let bet_item = $(`div[key="marketBetRateKey"][priority="${v.priority}"][market_bet_id="${v3.market_bet_id}"]`)
                        // if not exist -> create / if exists -> update
                        if( bet_item.length === 0 ) {
                            let line = null
                            if( s === 0) {
                                if( p-1 >= 0) {
                                    line = sortedKeys[p-1]
                                } else {
                                    line = 'first'
                                }
                            }
                            if( s > 0 ) {
                                line = key
                            }
                            createNewElement(v, v3, v.list[key].length, key, line);
                        } else {
                            let oldRate = parseFloat(bet_item.attr('bet_rate'))
                            let newRate = parseFloat(v3.price)

                            // rate compare
                            if( oldRate > newRate ) lowerOdd(v.priority, v3.market_bet_id)
                            if( oldRate < newRate ) raiseOdd(v.priority, v3.market_bet_id)

                            // status
                            if( v3.status === 1 ) {
                                bet_item.find('.fa-lock').hide()
                                bet_item.attr('onclick', 'openCal($(this))')
                            } else {
                                bet_item.find('.fa-lock').show()
                                bet_item.removeAttr('onclick')
                            }

                            // set new attribute
                            bet_item.attr('bet_rate', v3.price);
                            bet_item.attr('bet_type', v.market_name);
                            bet_item.attr('bet_name', v3.market_bet_name + ' ' + v3.line);
                            bet_item.attr('bet_name_en', v3.market_bet_name_en);
                            bet_item.attr('line', v3.line);

                            let isSelected = bet_item.hasClass('m_order_on')

                            // 左邊投注區塊
                            if( isSelected ) {
                                $('div[key="slideOrderCard"]').attr('market_id', v.market_id)
                                $('div[key="slideOrderCard"]').attr('market_bet_id', v3.market_bet_id)

                                let calBetNameStr = ''
                                let home = bet_item.attr('home')
                                let away = bet_item.attr('away')
                                if( convertTeamPriArr.indexOf(v.priority) === -1 ) {
                                    calBetNameStr = v3.market_bet_name + ' ' + v3.line
                                } else {
                                    switch (parseInt(v3.market_bet_name_en)) {
                                        case 1:
                                            calBetNameStr = home 
                                            break;
                                        case 2:
                                            calBetNameStr = away
                                            break;
                                        default:
                                            calBetNameStr = v3.market_bet_name
                                            break;
                                    }
                                    calBetNameStr += ' ' + v3.line
                                }

                                $(`div[key="slideOrderCard"][fixture_id="${fixture_id}"][market_id="${v.market_id}"][market_bet_id="${v3.market_bet_id}"] span[key="bet_name"]`).html(calBetNameStr)
                                $(`div[key="slideOrderCard"][fixture_id="${fixture_id}"][market_id="${v.market_id}"][market_bet_id="${v3.market_bet_id}"] span[key="bet_status"]`).html(cate === 'early' ? commonLangTrans.sport_menu.early : commonLangTrans.sport_menu.living)

                                $(`div[key="slideOrderCard"][fixture_id="${fixture_id}"][market_id="${v.market_id}"][market_bet_id="${v3.market_bet_id}"] span[key="odd"]`).html(v3.price)

                                $('#moneyInput').trigger('change') // 最高可贏金額
                            }


                            // new rate
                            bet_item.find('.odd').text(v3.price)
                            let pri = v.priority
                            switch (true) {
                                case commonLangTrans.priorityArr.bd.indexOf(v.priority) !== -1:
                                case commonLangTrans.priorityArr.size.indexOf(v.priority) !== -1:
                                    bet_item.find('.market_bet_name').html(`${v3.market_bet_name}`)
                                    bet_item.find('.line').html(`${v3.line}`)
                                    break;
                                case commonLangTrans.priorityArr.oddeven.indexOf(v.priority) !== -1:
                                    bet_item.find('.market_bet_name').html(`${v3.market_bet_name}`)
                                    bet_item.find('.line').html('')
                                    break;
                                case commonLangTrans.priorityArr.allwin.indexOf(v.priority) !== -1:
                                case commonLangTrans.priorityArr.hcap.indexOf(v.priority) !== -1:
                                    if (v3.market_bet_name_en == 1) {
                                        bet_item.find('.market_bet_name').html(`${matchListD.data.list.home_team_name}`)
                                        bet_item.find('.line').html(`${v3.line}`)
                                    } else if (v3.market_bet_name_en == 2) {
                                        bet_item.find('.market_bet_name').html(`${matchListD.data.list.away_team_name}`)
                                        bet_item.find('.line').html(`${v3.line}`)
                                    } else if (v3.market_bet_name_en == 'X') {
                                        bet_item.find('.market_bet_name').html('{{ trans("game.index.tie") }}')
                                        bet_item.find('.line').html('')
                                    }
                                    break;
                                default:
                                    break;
                            }
                        }
                    });
                });
            }
        });

       

        // 沒有盤口的tab隱藏
        let fullCounting = 0
        gameLangTrans.catePriority.full.map( v => { 
            fullCounting += $(`.bettingtype-container[priority=${v}]`).length
        })
        if( fullCounting === 0 ) {
            $('.filterBtn[key="full"]').hide();
            $('.filterBtn[key="full"]').removeClass('active')
        } else {
            $('.filterBtn[key="full"]').show()
        }

        let halfCounting = 0
        gameLangTrans.catePriority.half.map( v => { 
            halfCounting += $(`.bettingtype-container[priority=${v}]`).length
        })
        if( halfCounting === 0 ) {
            $('.filterBtn[key="half"]').hide();
            $('.filterBtn[key="half"]').removeClass('active')
        } else {
            $('.filterBtn[key="half"]').show()
        }

        // 籃球 單節tab篩選
        if(sport === 48242) {
            let r = $('.filterBtn.active[mark="single"]').attr('key')
            $('.filterBtn[mark="single"]').hide()
            $('.filterBtn[mark="single"]').removeClass('active')
            for (const [key, value] of Object.entries(gameLangTrans.catePriority.single[sport])) {
                for (const subValue of value) {
                    if ($(`.bettingtype-container[priority=${subValue}]`).length > 0) {
                        $(`.filterBtn[key=${key}]`).show();
                        if( key == r ) $(`.filterBtn[key=${key}]`).addClass('active')
                    }
                }
            }
        }

        if( $('.filterBtn.active').length === 0 ) {
            $('.filterBtn[key="all"]').addClass('active')
        } 

        // tab (show corresponding bet)
        $('.filterBtn.active').click()
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
                    renderView();
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

        marketNameElement.html(`<i class="fa-sharp fa-solid fa-star" style="color: #415a5b; margin-right: 0.5rem;"></i> ${v.market_name}`);
        $('#bettingTypeContainer').append(bettingTypeContainerTemp);
    }
    

    function createNewElement(v, v3, len, key, line=null) {
        const marketBetRateTemp = $('div[template="marketBetRateTemplate"]').clone();
        // col setting
        commonLangTrans.priorityArr.bd.indexOf(v.priority) !== -1 ? len = 2 : null
        marketBetRateTemp.addClass(`col-${12/len}`)

        marketBetRateTemp.removeAttr('hidden').removeAttr('template').removeAttr('style');
        
        let bet_div = $(`.bettingtype-container[market_id=${v.market_id}][priority=${v.priority}]`)
        marketBetRateTemp.attr('priority', v.priority);
        marketBetRateTemp.attr('fixture_id', fixture_id);
        marketBetRateTemp.attr('market_id', v.market_id);
        marketBetRateTemp.attr('market_bet_id', v3.market_bet_id);
        marketBetRateTemp.attr('bet_rate', v3.price);
        marketBetRateTemp.attr('bet_type', v.market_name);
        marketBetRateTemp.attr('bet_name', v3.market_bet_name + ' ' + v3.line);
        marketBetRateTemp.attr('bet_name_en', v3.market_bet_name_en);
        marketBetRateTemp.attr('line', v3.line);
        marketBetRateTemp.attr('linekey', key)
        marketBetRateTemp.attr('league', league_name);
        marketBetRateTemp.attr('home', fixtureData.home_team_name);
        marketBetRateTemp.attr('away', fixtureData.away_team_name);

        marketBetRateTemp.find('.odd').text(v3.price)
        let pri = v.priority
        switch (true) {
            case commonLangTrans.priorityArr.bd.indexOf(v.priority) !== -1:
            case commonLangTrans.priorityArr.size.indexOf(v.priority) !== -1:
                marketBetRateTemp.find('.market_bet_name').html(`${v3.market_bet_name}`)
                marketBetRateTemp.find('.line').html(`${v3.line}`)
                break;
            case commonLangTrans.priorityArr.oddeven.indexOf(v.priority) !== -1:
                marketBetRateTemp.find('.market_bet_name').html(`${v3.market_bet_name}`)
                marketBetRateTemp.find('.line').html('')
                break;
            case commonLangTrans.priorityArr.allwin.indexOf(v.priority) !== -1:
            case commonLangTrans.priorityArr.hcap.indexOf(v.priority) !== -1:
                if (v3.market_bet_name_en == 1) {
                    marketBetRateTemp.find('.market_bet_name').html(`${fixtureData.home_team_name}`)
                    marketBetRateTemp.find('.line').html(`${v3.line}`)
                } else if (v3.market_bet_name_en == 2) {
                    marketBetRateTemp.find('.market_bet_name').html(`${fixtureData.away_team_name}`)
                    marketBetRateTemp.find('.line').html(`${v3.line}`)
                } else if (v3.market_bet_name_en == 'X') {
                    marketBetRateTemp.find('.market_bet_name').html('{{ trans("game.index.tie") }}')
                    marketBetRateTemp.find('.line').html('')
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


        // 足球 平局 -> 主平客
        if( sport === 6046 && allWinArr.indexOf(v.priority) !== -1 && v3.market_bet_name_en === 'X' ) {
            bet_div.find(`div[priority=${v.priority}][bet_name_en="1"]`).after(marketBetRateTemp);
        } else {
            if( line !== null) {
                if( line === 'first' ) {
                    bet_div.find('.marketBetRateContainer').prepend(marketBetRateTemp)
                } else {
                    bet_div.find(`[key="marketBetRateKey"][linekey="${line}"]`).eq(-1).after(marketBetRateTemp)
                }
            } else {
                bet_div.find('.marketBetRateContainer').append(marketBetRateTemp);
            }
           
        }
    }

    // ------- game page scoreboard slider function-----------
    let currentSlide = [];
    let lastslide = [];
    let isLastSlide = false; 
    var getSlide3Count;

    function showSlide(slideIndex) {
        // Hide all slides
        document.querySelectorAll('.slider-bsbll').forEach(slide => {
            slide.style.display = 'none';
        });

        // Show the slides with the corresponding class
        document.querySelectorAll(`.slide-${slideIndex}`).forEach(slide => {
            slide.style.display = 'table-cell';
            if (slideIndex === 3) {
                // Get the length of elements with class .slide-3
                const slideLength = document.querySelectorAll('.slide-3').length - 6;
                const calculatedWidth =  60 / slideLength;
                slide.style.width = `${calculatedWidth}%`;
            } else {
                slide.style.width = `10%`;
            }
        });

        //set the currentslide to local when every refresh will get the current display slide
        localStorage.setItem('currentSlide', slideIndex);

        // Update the current slide index
        currentSlide = slideIndex;
        updateButtonClasses();
        updatePaginationActiveClass();

        // Check if it's the last slide
        isLastSlide = currentSlide === lastslide;
    }

    function nextSlide() {
        if (currentSlide < lastslide) currentSlide++, isLastSlide = currentSlide === lastslide, showSlide(currentSlide);
    }

    function prevSlide() {
        if (currentSlide > 1) currentSlide--, isLastSlide = false, showSlide(currentSlide);
    }

    function updateButtonClasses() {
        const prevBTN = document.getElementById('prevBTN');
        const nextBTN = document.getElementById('nextBTN');
        
        // Update Previous/Next button class
        prevBTN.classList.toggle('disabled-btn', currentSlide === 1); 
        nextBTN.classList.toggle('disabled-btn', isLastSlide);

        // disbale next button if lastslide is same of currentslide
        if (currentSlide === lastslide) nextBTN.classList.add('disabled-btn');
    }

    function updatePaginationActiveClass() {
        const paginationItems = document.querySelectorAll('.pagination li');
        paginationItems.forEach((item, index) => {
            item.classList.toggle('active', index + 1 === currentSlide);
        });
    }

    function createScoreBoard(data) {
        const earlyContainerTemp = $('div[template="earlyContainerTemplate"]').clone();
        if ((data.status == 2 || data.status == 9) && data.scoreboard) {
            if (sport === 154914) {
                const scbLen = data?.scoreboard[1].length - 1;
                $(".early-fixture-con").addClass("d-none");

                createScoreBoardTemplate(sport, data, [0, 1, 2, 3, 4, 5, 6, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
                $('.isBsbll:not(.isBsbll_0)').addClass("slider-bsbll");

                $(".isBsbll_1, .isBsbll_2, .isBsbll_3, .isBsbll_4, .isBsbll_5, .isBsbll_6").addClass("slide-1");

                if (scbLen >= 6) {
                    $(".isBsbll_4, .isBsbll_5, .isBsbll_6, .isBsbll_7, .isBsbll_8, .isBsbll_9").addClass("slide-2");
                } else {
                    $(".pgntn-bullet-2").addClass("d-none");
                }
                if (scbLen > 9) {
                    $(".isBsbll_7, .isBsbll_8, .isBsbll_9").addClass("slide-3");
                } else {
                    $(".pgntn-bullet-3").addClass("d-none");
                }

                // Load the current slide even after refresh
                const storedSlide = localStorage.getItem('currentSlide');
                if (storedSlide) {
                    currentSlide = parseInt(storedSlide);
                } else {
                    if (scbLen < 6) {
                        lastslide = 1;
                        currentSlide = 1;
                    } else if (scbLen >= 6 && scbLen <= 9) {
                        lastslide = 2;
                        currentSlide = 2;
                    } else {
                        lastslide = 3;
                        currentSlide = 3;
                    }
                }

                showSlide(currentSlide, lastslide, isLastSlide);

            } else {
                $(".early-fixture-con").addClass("d-none");
                $(".navigation-controls").addClass("d-none");
                createScoreBoardTemplate(sport, data, [0, 1, 2, 3, 4, 5, 6]);
            }
        } else {
            $(".living-fixture-con").addClass("d-none");
            $(".navigation-controls").addClass("d-none");
            const leagueID = league_id;
            $(`div[id="${leagueID}"]`).remove();
            earlyContainerTemp.removeAttr('hidden').removeAttr('template');
            earlyContainerTemp.attr('id', league_id);
            earlyContainerTemp.find('.home_team_name').text(data.home_team_name);
            earlyContainerTemp.find('.league_name').text(data.league_name);
            earlyContainerTemp.find('.start_time').html(formatDateTime(data.start_time));
            earlyContainerTemp.find('.away_team_name').text(data.away_team_name);
            $('.early-fixture-con').append(earlyContainerTemp);
        }
    }

    function createScoreBoardTemplate(sport, data, baseballShowStage) {
        const livingContainerTemp = $(`div[template="livingContainerTemplate-con"]`).clone();
        const scoreBoardHeadTemp = $(`tr[template="scoreBoardHeadTemplate-con"]`).clone();
        const scoreBoardBodyTemp_home = $(`tr[template="scoreBoardBodyTemplate_home-con"]`).clone();
        const scoreBoardBodyTemp_away = $(`tr[template="scoreBoardBodyTemplate_away-con"]`).clone();

        livingContainerTemp.removeAttr('hidden').removeAttr('template');

        var scorehome = data?.scoreboard[1];
        var scoreaway = data?.scoreboard[2];

        const randomInt = Math.floor(Math.random() * 100) + 1;

        const mainCon = fixture_id + '_1'; 
        const existingMainCon = $(`div[id="${mainCon}"]`); 
        const headTr = fixture_id + '_' + randomInt + '_head'; 
        const existingHeadTr = $(`tr[id="${headTr}"]`);
        const bodyTr = fixture_id + '_' + randomInt + '_body';
        const existingBodyTr = $(`tr[id="${bodyTr}"]`);

        $(`div.living-fixture-con`).empty();
        livingContainerTemp.find(`thead[key="livingtableHead-con"]`).empty();
        livingContainerTemp.find(`tbody[key="livingtableBody-con"]`).empty();

        scoreBoardHeadTemp.removeAttr('hidden').removeAttr('template');
        scoreBoardBodyTemp_home.removeAttr('hidden').removeAttr('template');  
        scoreBoardBodyTemp_away.removeAttr('hidden').removeAttr('template'); 

        livingContainerTemp.attr('id', mainCon);
        scoreBoardHeadTemp.attr('id', headTr);
        scoreBoardBodyTemp_home.attr('id', bodyTr);
        scoreBoardBodyTemp_away.attr('id', bodyTr);

        const gameTitle = gameLangTrans.scoreBoard.gameTitle[sport];
        // Thead data game title
        let stageStr = '';
        if (sport === 154914 && data?.periods?.period < 10) {
            data.periods.Turn === '1' ? (stageStr = gameLangTrans.scoreBoard.lowerStage) : (stageStr = gameLangTrans.scoreBoard.upperStage);
        }

        var stageText = formatDateTime(data.start_time);
        if (data.status == 2) {
            if (data.periods.period !== -1) stageText = commonLangTrans.stageArr[sport][data.periods.period];
        } else {
            stageText = gameLangTrans.scoreBoard.ready;
        }

        const TeamNameHead = $(`<th style="width: 25%; text-align: left;color:#ffffff;"><div class="setHeightDiv">${stageText} ${stageStr}</div></th>`);
        scoreBoardHeadTemp.append(TeamNameHead);

        let baseballShowStageTemp = baseballShowStage;

        for (let i = 0; i < gameTitle.length; i++) {
            if (sport === 154914) {
                const scbLen = data?.scoreboard[1].length - 1;
                baseballShowStageTemp = baseballShowStage;
                if (baseballShowStageTemp.indexOf(i) !== -1) {
                    scoreBoardHeadTemp.append($(`<th class="isBsbll isBsbll_${i}" style="width:10%;text-align:center;"><div class="setHeightDiv">`).text(gameTitle[i]));
                }
            } else {
                scoreBoardHeadTemp.append($(`<th style="width:10%;text-align:center;"><div class="setHeightDiv">`).text(gameTitle[i]));
            }
        }

        livingContainerTemp.find(`thead[key="livingtableHead-con"]`).append(scoreBoardHeadTemp);

        // Home team
        const homeTeamName = $(`<th style="width:25%;text-align:left;color:#ffffff;"><div class="textOverflowCon">${data.home_team_name}</div></th>`);
        scoreBoardBodyTemp_home.append(homeTeamName);
        for (let i = 0; i < gameTitle.length; i++) {
            const scoreValue = Array.from(Object.values(scorehome))[i];
            const thHome = $(`<td class="${sport === 154914 ? 'isBsbll isBsbll_' + i : ''}" style="width:10%;text-align:center;">`).text(scoreValue !== undefined ? scoreValue : '-');
            if (!(sport === 154914 && baseballShowStage.indexOf(i) === -1)) {
                scoreBoardBodyTemp_home.append(thHome);
            }
        }

        livingContainerTemp.find(`tbody[key="livingtableBody-con"]`).append(scoreBoardBodyTemp_home);

        // Away team
        const awayTeamName = $(`<th style="width:25%;text-align:left;color:#ffffff;"><div class="textOverflowCon">${data.away_team_name}</div></th>`);
        scoreBoardBodyTemp_away.append(awayTeamName);
        for (let i = 0; i < gameTitle.length; i++) {
            const scoreValue = Array.from(Object.values(scoreaway))[i];
            const thAway = $(`<td class="${sport === 154914 ? 'isBsbll isBsbll_' + i : ''}" style="width:10%;text-align:center;">`).text(scoreValue !== undefined ? scoreValue : '-');
            if (!(sport === 154914 && baseballShowStage.indexOf(i) === -1)) {
                scoreBoardBodyTemp_away.append(thAway);
            }
        }

        // Append away team after home team to table
        scoreBoardBodyTemp_home.after(scoreBoardBodyTemp_away);
        $(`.living-fixture-con`).append(livingContainerTemp);
    }

    // Clear localStorage('currentSlide') when the page is unloaded (hard refresh or leaving the page)
    window.addEventListener('beforeunload', function () {
        localStorage.removeItem('currentSlide');
    });

    function noData() {
        var noDataElement = document.createElement('div');
        noDataElement.classList.add('noDataContainer');
        noDataElement.innerHTML = "{{ trans('match.main.nomoredata') }}";
        $('#bettingTypeContainer').empty();
        $('#bettingTypeContainer').append(noDataElement);
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
        let leftTarget = $(`div[key="slideOrderCard"][market_bet_id="${market_bet_id}"]`)
        // 先移除現有樣式
        target.removeClass('raiseOdd')
        target.removeClass('lowerOdd')
        leftTarget.removeClass('raiseOdd')
        leftTarget.removeClass('lowerOdd')
        target.find('.fa-caret-up').hide()
        target.find('.fa-caret-down').hide()

        // 再加上賠率變化樣式
        target.addClass('raiseOdd')
        leftTarget.addClass('raiseOdd')
        target.find('.fa-caret-up').show()

        // 三秒後移除
        setTimeout(() => {
            target.removeClass('raiseOdd')
            leftTarget.removeClass('raiseOdd')
            target.find('.fa-caret-up').hide()
        }, 3000);
    }
    // 賠率下降
    function lowerOdd(priority, market_bet_id) {
        let target = $(`div[key="marketBetRateKey"][priority="${priority}"][market_bet_id="${market_bet_id}"]`)
        let leftTarget = $(`div[key="slideOrderCard"][market_bet_id="${market_bet_id}"]`)

        // 先移除現有樣式
        target.removeClass('raiseOdd')
        target.removeClass('lowerOdd')
        leftTarget.removeClass('raiseOdd')
        leftTarget.removeClass('lowerOdd')
        target.find('.fa-caret-up').hide()
        target.find('.fa-caret-down').hide()

        // 再加上賠率變化樣式
        target.addClass('lowerOdd')
        leftTarget.addClass('lowerOdd')
        target.find('.fa-caret-down').show()

        // 三秒後移除
        setTimeout(() => {
            leftTarget.removeClass('raiseOdd')
            target.removeClass('lowerOdd')
            target.find('.fa-caret-down').hide()
        }, 3000);
    }



    // 打開投注計算機
    var sendOrderData = {}
    function openCal(e) {
        $('#betPrompt').html('')
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
        let cate = fixtureData.status === 1 ? 'early' : 'living'

        $('#leftSlideOrder span[key="bet_type"]').html(bet_type)
        $('#leftSlideOrder span[key="bet_status"]').html( cate === 'early' ? commonLangTrans.sport_menu.early : commonLangTrans.sport_menu.living)
        if( convertTeamPriArr.indexOf(priority) === -1 ) {
            $('#leftSlideOrder span[key="bet_name"]').html(bet_name)
        } else {
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

        // 限額
        betLimitationD = accountD.data.limit[cate][sport]
        $('#submitOrder').attr('min', betLimitationD.min)
        $('#submitOrder').attr('max', betLimitationD.max)
        $('#moneyInput').attr('placeholder', `${langTrans.js.limit} ${betLimitationD.min}-${betLimitationD.max}`)
        // $('#moneyInput').val(betLimitationD.min)
        $('#moneyInput').trigger('change')
        $('#moneyInput').focus()
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
        // let min = parseInt($('#submitOrder').attr('min'))
        // let max = parseInt($('#submitOrder').attr('max'))
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

        // limit
        let min = parseInt($('#submitOrder').attr('min'))
        let max = parseInt($('#submitOrder').attr('max'))

        if (sendOrderData.bet_amount < min) {
            $('#betPrompt').html(langTrans.js.tooless_bet_amout + min)
            $('#moneyInput').val(min)
            $('#moneyInput').trigger('change')
            return;
        }
        if (sendOrderData.bet_amount > max) {
            $('#betPrompt').html(langTrans.js.toohigh_bet_amout + max)
            $('#moneyInput').val(max)
            $('#moneyInput').trigger('change')
            return;
        }


        $('#betPrompt').html('')
        // Show loading spinner while submitting
        showLoading();

        $.ajax({
            url: '/api/v2/game_bet',
            method: 'POST',
            data: sendOrderData,
            success: function(response) {
                let res = JSON.parse(response)
                if(res.status === 1) {
                    calInter = setTimeout(function() {
                        hideLoading();
                        closeCal();
                        refreshBalence();
                    }, 10000);
                } else {
                    showErrorToast(res.message);
                    hideLoading();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('error');
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
    
    formatDateTime = (dateTimeString) => {
        const dateTime = new Date(dateTimeString);
        const month = (dateTime.getMonth() + 1).toString().padStart(2, '0'); // Get month (0-based index), add 1, and pad with '0' if needed
        const day = dateTime.getDate().toString().padStart(2, '0'); // Get day and pad with '0' if needed
        const hour = dateTime.getHours().toString().padStart(2, '0'); // Get hours and pad with '0' if needed
        const minute = dateTime.getMinutes().toString().padStart(2, '0'); // Get minutes and pad with '0' if needed
        return `${month}-${day} ${hour}:${minute}`;
    }

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
    $('.filterBtn').click(function(event){
		if (event.originalEvent) document.getElementById('bettingTypeContainer').scrollTo({top: 0}); // totop first
        $('.filterBtn').removeClass('active')
        $(this).addClass('active')
        let tab = $(this).attr('key')
        switch (tab) {
            case 'all':
                $('.bettingtype-container').show()
                break;
            case 'full':
                $('.bettingtype-container').each(function(){
                    let k = parseInt($(this).attr('priority'))
                    if(gameLangTrans.catePriority.full.indexOf(k) !== -1) {
                        $(this).show()
                    } else {
                        $(this).hide()
                    }
                })
                break;
            case 'half':
                $('.bettingtype-container').each(function(){
                    let k = parseInt($(this).attr('priority'))
                    if(gameLangTrans.catePriority.half.indexOf(k) !== -1) {
                        $(this).show()
                    } else {
                        $(this).hide()
                    }
                })
                break;
            default:
                $('.bettingtype-container').hide()
                gameLangTrans.catePriority.single[sport][tab].map(v => {
                    $(`.bettingtype-container[priority=${v}]`).show()
                })
                break;
        }
    })
</script>
@endpush