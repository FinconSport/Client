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
        <div id="early"></div>
        <div id="living"></div>
    </div>

    <div id="indexContainerLeft" style="display:none;">
        @foreach ($match_list as $key => $cat)
            <div id="toggleContent_{{ $key }}">
                <div class="catWrapperTitle" id="catWrapperTitle_{{ $key }}" onclick="toggleCat('{{ $key }}')">
                    <span>{{ trans('index.mainArea')[$key] }}
                        (<span id="catWrapperContent_{{ $key }}_total">{{ count($match_list[$key]) }}</span>)
                    </span>
                    <span id="catWrapperTitle_{{ $key }}_dir" style="float: right;">
                        <i class="fa-solid fa-chevron-right"></i> 
                    </span>
                </div>
                <div class="catWrapperContent" id="catWrapperContent_{{ $key }}" style="display: none;">
                    @foreach ($cat as $key2 => $ser)
                        <div class="seriesWrapperTitle" id="seriesWrapperTitle_{{ $key }}_{{ $key2 }}" onclick="toggleSeries('{{ $key }}_{{ $key2 }}')" series_id="{{ $ser['series']['id'] }}">
                            <span>{{ $ser['series']['name'] }}</span>
                            (<span id="seriesWrapperTitle_{{ $key }}_{{ $key2 }}_count">{{ count($ser['list']) }}</span>)
                            <span id="seriesWrapperTitle_{{ $key }}_{{ $key2 }}_dir" style="float: right;">
                                <i class="fa-solid fa-circle-chevron-down"></i>
                            </span>
                        </div>
                        <div class="seriesWrapperContent" id="seriesWrapperContent_{{ $key }}_{{ $key2 }}">
                        @if(count($match_list[$key]) > 0)
                                @foreach ($ser['list'] as $key3 => $item)
                                    <div class="indexEachCard" key='{{ $item["match_id"] }}' status='{{ $item["status"] }}' style="@if($item['status'] == -1) display: none; @endif">
                                        <div class="indexBetCard">
                                            <div class="indexBetCardInfo">
                                                <div class="timeSpan">{{ trans('index.mainArea.time') }}<span class="timer" series_id="{{ $ser['series']['id'] }}">{{ $item['start_time'] }}</span>
                                                </div>
                                                <div class="w-100" style="display: inline-flex;">
                                                    @foreach ($item['teams'] as $team)
                                                        @if($team['index'] === 1)
                                                            @if(isset($team['team']['name']))
                                                                <div class="textOverFlow teamSpan" style="width: 80%;">
                                                                    {{ $team['team']['name'] }} [{{ trans('index.mainArea.homeTeamTag') }}]
                                                                </div>
                                                            @endif
                                                            @if (isset($team['total_score']))
                                                                <div class="scoreSpan" style="width: 20%;">
                                                                    {{ $team['total_score'] }}
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="w-100" style="display: inline-flex;">
                                                    @foreach ($item['teams'] as $team)
                                                        @if($team['index'] === 2)
                                                            @if(isset($team['team']['name']))
                                                                <div class="textOverFlow teamSpan" style="width: 80%;">
                                                                    {{ $team['team']['name'] }}
                                                                </div>
                                                            @endif
                                                            @if (isset($team['total_score']))
                                                                <div class="scoreSpan" style="width: 20%;">
                                                                    {{ $team['total_score'] }}
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="indexBetCardTable row m-0 text-center">
                                                <!-- 全場獨贏 -->
                                                <div class="col-2 p-0">
                                                    <div class="betLabel">
                                                        {{ trans('index.sportBetData.' . intval($search['sport']) . '.gameTitle.0') }}
                                                    </div>
                                                    @if (isset($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]))
                                                        <!-- 主勝 -->
                                                        @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['rate'])->sortKeys() as $subrate)
                                                            @if($loop->first)
                                                                <div class="betItemDiv" index='{{ $loop->index }}' game_priority="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['game_priority'] }}" onclick="openCal($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}" key='{{ $key }}' key2='{{ $key2 }}' key3='{{ $key3 }}' rate_status="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['status'] }}" rate_item_status="{{ $subrate['status'] }}" risk="{{ $subrate['risk'] }}">
                                                                    <span>{{ trans('index.mainArea.homeWin') }}&ensp;</span>
                                                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                                                    <i class="fa-solid fa-lock"></i>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                        <!-- 客勝 -->
                                                        @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['rate'])->sortKeys() as $subrate)
                                                            @if($loop->last)
                                                                <div class="betItemDiv" index='{{ $loop->index }}' game_priority="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['game_priority'] }}" onclick="openCal($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}" key='{{ $key }}' key2='{{ $key2 }}' key3='{{ $key3 }}' rate_status="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['status'] }}" rate_item_status="{{ $subrate['status'] }}" risk="{{ $subrate['risk'] }}">
                                                                    <span>{{ trans('index.mainArea.awayWin') }}&ensp;</span>
                                                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                                                    <i class="fa-solid fa-lock"></i>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                        <!-- 平局 -->
                                                        @if($search['sport'] == 1)
                                                            @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['rate'])->sortKeys() as $subrate)
                                                                @if($loop->index === 1)
                                                                    <div class="betItemDiv" index='{{ $loop->index }}' game_priority="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['game_priority'] }}" onclick="openCal($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}" key='{{ $key }}' key2='{{ $key2 }}' key3='{{ $key3 }}' rate_status="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['status'] }}" rate_item_status="{{ $subrate['status'] }}" risk="{{ $subrate['risk'] }}">
                                                                        <span>{{ trans('index.mainArea.tie') }}&ensp;</span>
                                                                        <span class="odd">{{ $subrate['rate'] }}</span>
                                                                        <i class="fa-solid fa-lock"></i>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                </div>
                                                <!-- 全場獨贏 -->
                                                <!-- 全場讓球 -->
                                                <div class="col-2 p-0">
                                                    <div class="betLabel">
                                                        {{ trans('index.sportBetData.' . intval($search['sport']) . '.gameTitle.1') }}
                                                    </div>
                                                    @isset($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.1')])
                                                        @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.1')])->sortBy('rate_value')->sortBy('status') as $subrate)
                                                            @foreach (collect($subrate['rate'])->sortKeys() as $ssubrate)
                                                                <div class="betItemDiv" index='{{ $loop->index }}' game_priority="{{ $subrate['game_priority'] }}" onclick="openCal($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $subrate['rate_id'] }}" rate_name="{{ $subrate['name'] }}" rate="{{ $ssubrate['id'] }}" bet_name="{{ $ssubrate['name'] }}" key='{{ $key }}' key2='{{ $key2 }}' key3='{{ $key3 }}' rate_status="{{ $subrate['status'] }}" rate_item_status="{{ $ssubrate['status'] }}" risk="{{ $ssubrate['risk'] }}">
                                                                    <span class="rate_name">{{ $ssubrate['value'] }}</span>&ensp;
                                                                    <span class="odd">{{ $ssubrate['rate'] }}</span>
                                                                    <i class="fa-solid fa-lock"></i>
                                                                </div>
                                                            @endforeach
                                                            @break
                                                        @endforeach
                                                    @endisset
                                                </div>
                                                <!-- 全場讓球 -->
                                                <!-- 全場大小 -->
                                                <div class="col-2 p-0">
                                                    <div class="betLabel">
                                                        {{ trans('index.sportBetData.' . intval($search['sport']) . '.gameTitle.2') }}
                                                    </div>
                                                    @isset($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.2')])
                                                        @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.2')])->sortBy('rate_value')->sortBy('status') as $subrate)
                                                            @foreach (collect($subrate['rate'])->sortKeys() as $ssubrate)
                                                                <div class="betItemDiv" index='{{ $loop->index }}' game_priority="{{ $subrate['game_priority'] }}" onclick="openCal($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $subrate['rate_id'] }}" rate_name="{{ $subrate['name'] }}" rate="{{ $ssubrate['id'] }}" bet_name="{{ $ssubrate['name'] }}" key='{{ $key }}' key2='{{ $key2 }}' key3='{{ $key3 }}' rate_status="{{ $subrate['status'] }}" rate_item_status="{{ $ssubrate['status'] }}" risk="{{ $ssubrate['risk'] }}">
                                                                    <span class="rate_name">{{ $ssubrate['name'] }}</span>&ensp;
                                                                    <span class="odd">{{ $ssubrate['rate'] }}</span>
                                                                    <i class="fa-solid fa-lock"></i>
                                                                </div>
                                                            @endforeach
                                                            @break
                                                        @endforeach
                                                    @endisset
                                                </div>
                                                <!-- 全場大小 -->
                                                <!-- 半場獨贏 -->
                                                <div class="col-2 p-0">
                                                    <div class="betLabel">
                                                        {{ trans('index.sportBetData.' . intval($search['sport']) . '.gameTitle.3') }}
                                                    </div>
                                                    @if (isset($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]))
                                                        <!-- 主勝 -->
                                                        @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['rate'])->sortKeys() as $subrate)
                                                            @if($loop->first)
                                                                <div class="betItemDiv" index='{{ $loop->index }}' game_priority="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['game_priority'] }}" onclick="openCal($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}" key='{{ $key }}' key2='{{ $key2 }}' key3='{{ $key3 }}' rate_status="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['status'] }}" rate_item_status="{{ $subrate['status'] }}" risk="{{ $subrate['risk'] }}">
                                                                    <span>{{ trans('index.mainArea.homeWin') }}&ensp;</span>
                                                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                                                    <i class="fa-solid fa-lock"></i>
                                                                </div>
                                                                @break
                                                            @endif
                                                        @endforeach
                                                        <!-- 客勝 -->
                                                        @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['rate'])->sortKeys() as $subrate)
                                                            @if($loop->last)
                                                                <div class="betItemDiv" index='{{ $loop->index }}' game_priority="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['game_priority'] }}" onclick="openCal($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}" key='{{ $key }}' key2='{{ $key2 }}' key3='{{ $key3 }}' rate_status="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['status'] }}" rate_item_status="{{ $subrate['status'] }}" risk="{{ $subrate['risk'] }}">
                                                                    <span>{{ trans('index.mainArea.awayWin') }}&ensp;</span>
                                                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                                                    <i class="fa-solid fa-lock"></i>
                                                                </div>
                                                                @break
                                                            @endif
                                                        @endforeach
                                                        <!-- 平局 -->
                                                        @if($search['sport'] != 2)
                                                            @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['rate'])->sortKeys() as $subrate)
                                                                @if($loop->index === 1)
                                                                    <div class="betItemDiv" index='{{ $loop->index }}' game_priority="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['game_priority'] }}" onclick="openCal($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}" key='{{ $key }}' key2='{{ $key2 }}' key3='{{ $key3 }}' rate_status="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['status'] }}" rate_item_status="{{ $subrate['status'] }}" risk="{{ $subrate['risk'] }}">
                                                                        <span>{{ trans('index.mainArea.tie') }}&ensp;</span>
                                                                        <span class="odd">{{ $subrate['rate'] }}</span>
                                                                        <i class="fa-solid fa-lock"></i>
                                                                    </div>
                                                                    @break
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                </div>
                                                <!-- 半場獨贏 -->
                                                <!-- 半場讓球 -->
                                                <div class="col-2 p-0">
                                                    <div class="betLabel">
                                                        {{ trans('index.sportBetData.' . intval($search['sport']) . '.gameTitle.4') }}
                                                    </div>
                                                    @isset($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.4')])
                                                        @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.4')])->sortBy('rate_value')->sortBy('status') as $subrate)
                                                            @foreach (collect($subrate['rate'])->sortKeys() as $ssubrate)
                                                                <div class="betItemDiv" index='{{ $loop->index }}' game_priority="{{ $subrate['game_priority'] }}" onclick="openCal($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $subrate['rate_id'] }}" rate_name="{{ $subrate['name'] }}" rate="{{ $ssubrate['id'] }}" bet_name="{{ $ssubrate['name'] }}" key='{{ $key }}' key2='{{ $key2 }}' key3='{{ $key3 }}' rate_status="{{ $subrate['status'] }}" rate_item_status="{{ $ssubrate['status'] }}" risk="{{ $ssubrate['risk'] }}">
                                                                    <span class="rate_name">{{ $ssubrate['value'] }}</span>&ensp;
                                                                    <span class="odd">{{ $ssubrate['rate'] }}</span>
                                                                    <i class="fa-solid fa-lock"></i>
                                                                </div>
                                                            @endforeach
                                                            @break
                                                        @endforeach
                                                        @endisset
                                                </div>
                                                <!-- 半場讓球 -->
                                                <!-- 半場大小 -->
                                                <div class="col-2 p-0">
                                                    <div class="betLabel">
                                                        {{ trans('index.sportBetData.' . intval($search['sport']) . '.gameTitle.5') }}
                                                    </div>
                                                    @isset($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.5')])
                                                        @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.5')])->sortBy('rate_value')->sortBy('status') as $subrate)
                                                            @foreach (collect($subrate['rate'])->sortKeys() as $ssubrate)
                                                                <div class="betItemDiv" index='{{ $loop->index }}' game_priority="{{ $subrate['game_priority'] }}" onclick="openCal($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $subrate['rate_id'] }}" rate_name="{{ $subrate['name'] }}" rate="{{ $ssubrate['id'] }}" bet_name="{{ $ssubrate['name'] }}" key='{{ $key }}' key2='{{ $key2 }}' key3='{{ $key3 }}' rate_status="{{ $subrate['status'] }}" rate_item_status="{{ $ssubrate['status'] }}" risk="{{ $ssubrate['risk'] }}">
                                                                    <span class="rate_name">{{ $ssubrate['name'] }}</span>&ensp;
                                                                    <span class="odd">{{ $ssubrate['rate'] }}</span>
                                                                    <i class="fa-solid fa-lock"></i>
                                                                </div>
                                                            @endforeach
                                                            @break
                                                        @endforeach
                                                    @endisset
                                                </div>
                                                <!-- 半場大小 -->
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <div id="noData" style="display: none;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <p class="mb-0">{{ trans('index.mainArea.nogame') }}</p>
        </div>
    </div>
</div>
@endsection



@section('styles')
<!-- <link href="{{ asset('css/index.css?v=' . $system_config['version']) }}" rel="stylesheet"> -->
<link href="{{ asset('css/index.css?v=' . $current_time) }}" rel="stylesheet">
<link href="{{ asset('css/index_ind.css?v=' . $current_time) }}" rel="stylesheet">
@endSection

@push('main_js')

<script>

    // 
    // const player = @json($player['id']);
    // const token = '12345';


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
    var callMatchListData = { token: token, player: player, sport_id: sport }
    const matchList_api = 'https://sportc.asgame.net/api/v2/match_index'

    // bet limitation data
    var betLimitationD = {}
    var callLimitationData = {}
    const betLimitation_api = ''
    /* ===== DATA LAYER ===== */
    
    /* ===== VIEW LAYER ===== */
    function viewIni() { // view ini

        // put the view ini function here  
        // ex: matchListD html element appedning, textoverflow handle, open the first toggle....

        // loop matchListD to generate html element here
        function createMainDiv(title, fixtureCount) {
            const mainDiv = document.createElement("div");
            mainDiv.className = "main-div";
            mainDiv.innerHTML = `<div class="catWrapperTitle"><p>{{ trans('index.mainArea.living') }}(${fixtureCount})</p><i class="fa-solid fa-chevron-right"></i></div>`;
            return mainDiv;
        }

        function createLeagueDiv(title, fixtureCount) {
            const leagueDiv = document.createElement("div");
            leagueDiv.className = "seriesWrapperTitle";
            leagueDiv.innerHTML = `<div class="catWrapperTitle"><p>{{ trans('index.mainArea.living') }}(${fixtureCount})</p><i class="fa-solid fa-chevron-right"></i></div>`;
            return leagueDiv;
            leagueDiv.appendChild(mainDiv);
        }

        function createListDiv(fixtureData) {
            const listDiv = document.createElement("div");
            listDiv.className = "seriesWrapperContent";

            if (fixtureData) {
                for (const fixtureId in fixtureData) {
                    if (fixtureData.hasOwnProperty(fixtureId)) {
                        const fixture = fixtureData[fixtureId];
                        const fixtureDiv = document.createElement("div");
                        fixtureDiv.className = "fixture";
                        fixtureDiv.innerHTML = `<p>${fixture.home_team_name}  ${fixture.away_team_name}  ${fixture.start_time}</p>`;
                        listDiv.appendChild(fixtureDiv);
                    }
                }
            } else {
                const noDataDiv = document.createElement("div");
                noDataDiv.className = "no-data";
                noDataDiv.textContent = "No data available.";
                listDiv.appendChild(noDataDiv);
            }

            return listDiv;
        }

        const earlyData = matchListD.data.early;
        const livingData = matchListD.data.living;

        const earlyParentDiv = document.getElementById("early");
        const livingParentDiv = document.getElementById("living");
        for (const leagueId in earlyData) {
            if (earlyData.hasOwnProperty(leagueId)) {
                const league = earlyData[leagueId];
                const leagueName = league.list[183] ? league.list[183].league_name : '';
                const fixtureCount = league.list[183] && league.list[183].list
                    ? Object.keys(league.list[183].list).length
                    : 0;
                const leagueDiv = createMainDiv("early", fixtureCount);
                const listDiv = createListDiv(league.list[183] ? league.list[183].list : null);
                leagueDiv.appendChild(listDiv);
                earlyParentDiv.appendChild(leagueDiv);
            }
        }
        for (const leagueId in livingData) {
            if (livingData.hasOwnProperty(leagueId)) {
                const league = livingData[leagueId];
                const leagueName = league.sport_name;
                const fixtureCount = league.list && league.list.length > 0
                    ? Object.keys(league.list).length
                    : 0;
                const leagueDiv = createMainDiv("living", fixtureCount);
                const listDiv = createListDiv(league.list);
                leagueDiv.appendChild(listDiv);
                livingParentDiv.appendChild(leagueDiv);
            }
        }
        
        // loop matchListD to generate html element here

        // open the first
        if($('div[id^=toggleContent_]:visible').length > 0) {
            setTimeout(() => {
                $('.catWrapperTitle:visible').eq(0).click()
            }, 500);
        }
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
    function renderView( ) {
        console.log('renderView')




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
    function raiseOdd(match_id, rate_id, id, updateRate) {
        // 先移除現有樣式
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + ']').removeClass('raiseOdd')
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + '] .fa-caret-up').remove()
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + ']').removeClass('lowerOdd')
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + '] .fa-caret-down').remove()

        // 再加上賠率變化樣式
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + ']').addClass('raiseOdd')
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + '] .odd').html(updateRate)
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + '] .odd').after('<i class="fa-solid fa-caret-up"></i>')

        // 三秒後移除
        setTimeout(() => {
            $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + ']').removeClass('raiseOdd')
            $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + '] .fa-caret-up').remove()
        }, 3000);
    }
    // 賠率下降
    function lowerOdd(match_id, rate_id, id, updateRate) {
        // 先移除現有樣式
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + ']').removeClass('raiseOdd')
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + '] .fa-caret-up').remove()
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + ']').removeClass('lowerOdd')
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + '] .fa-caret-down').remove()

        // 再加上賠率變化樣式
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + ']').addClass('lowerOdd')
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + '] .odd').html(updateRate)
        $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + '] .odd').after('<i class="fa-sharp fa-solid fa-caret-down"></i>')

        // 三秒後移除
        setTimeout(() => {
            $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + ']').removeClass('lowerOdd')
            $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + id + '] .fa-caret-down').remove()
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
    function openCal(e, spanIndex = 0) {
        if (!e.find('.fa-lock').is(':visible')) {
            let match_id = e.attr('match_id')
            let rate_id = e.attr('rate_id')
            let rate = e.attr('rate')
            let odd = e.find('.odd').html()

            let rate_name = e.attr('rate_name')
            let bet_name = e.attr('bet_name')

            let key = e.attr('key')
            let key2 = e.attr('key2')
            let key3 = e.attr('key3')

            let result = match_list[key][key2]['list'][key3]
            let series = result.series.name

            var homeData = result.teams.find(item => item.index === 1)
            var awayData = result.teams.find(item => item.index === 2)
            let homeTeam = homeData.team.name ?? langTrans.mainArea.homeTeam;
            let awayTeam = awayData.team.name ?? langTrans.mainArea.awayTeam;

            sendOrderData = {
                bet_match: match_id,
                bet_type: rate_id,
                bet_type_item: rate,
                bet_rate: odd,
                better_rate: 0,
                game_id: searchData.sport
            }

            $('#leftSlideOrder span[key="rate_name"]').html(rate_name)
            $('#leftSlideOrder span[key="bet_name"]').html(bet_name)
            $('#leftSlideOrder span[key="odd"]').html(odd)
            $('#leftSlideOrder p[key="series"]').html(series)
            $('#leftSlideOrder span[key="home"]').html(homeTeam)
            $('#leftSlideOrder span[key="away"]').html(awayTeam)
            $('#leftSlideOrder div[key="oddContainer"]').attr('match_id', match_id)
            $('#leftSlideOrder div[key="oddContainer"]').attr('rate_id', rate_id)
            $('#leftSlideOrder div[key="oddContainer"]').attr('rate', rate)

            $('#leftSlideOrder').show("slide", {
                direction: "left"
            }, 500);
            $('#mask').fadeIn()

            // 選中樣式
            $('div[match_id=' + match_id + '][rate_id=' + rate_id + '][rate=' + rate + ']').addClass('m_order_on')

            // 判斷滾球or早盤
            const start_time = new Date(result.start_time).getTime();
            const now = new Date().getTime();
            let placeholderStr = langTrans.js.limit

            if (now > start_time) {
                // 滾球
                min = parseInt(limit.living[sport].min)
                max = parseInt(limit.living[sport].max)
            } else {
                // 早盤
                min = parseInt(limit.early[sport].min)
                max = parseInt(limit.early[sport].max)
            }
            placeholderStr += min
            placeholderStr += '-'
            placeholderStr += max
            $('#moneyInput').attr('placeholder', placeholderStr)
            $('#moneyInput').val(min)
            $('#moneyInput').trigger('change')
            $('#moneyInput').focus()
        }
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
        if (inputMoney < min) inputMoney = min
        if (inputMoney > max) inputMoney = max
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
        if (sendOrderData.bet_amount < min) {
            showErrorToast(langTrans.js.tooless_bet_amout + min);
            return;
        }
        if (sendOrderData.bet_amount > max) {
            showErrorToast(langTrans.js.toohigh_bet_amout + max);
            return;
        }

        $.ajax({
            url: '/order/create',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: sendOrderData,
            success: function(response) {
                let res = JSON.parse(response)
                if (res.message === 'SUCCESS_ORDER_CREATE_01') {
                    // 餘額更新
                    $('.balance').html(res.data)
                    showSuccessToast(res.message)
                } else {
                    showErrorToast(res.message)
                }
            },
            error: function(xhr, status, error) {
                console.error('error');
                showErrorToast(xhr)
            }
        });
        // 金額歸零
        $('#moneyInput').val('')
        $('#moneyInput').trigger('change')
        // 隱藏計算機
        closeCal()
    }

    // 餘額
    function refreshBalence() {
        $('#refreshIcon').addClass('rotate-animation')
        $.ajax({
            url: '/account',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                let data = JSON.parse(response)
                let account = data.data.balance
                // 停止旋轉
                $('#refreshIcon').removeClass('rotate-animation')
                showSuccessToast(data.message)
                // 餘額
                $('.balance').html(account)
            },
            error: function(xhr, status, error) {
                console.error('error');
                // 停止旋轉
                $('#refreshIcon').removeClass('rotate-animation')
                showErrorToast(data.message)
            }
        });
    }
</script>
@endpush