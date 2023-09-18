@extends('layout.app')

@section('content')
<!-- 投注計算機 -->
<div id='mask' style="display: none;"></div>
<div id="leftSlideOrder" style="display: none;">
    <div class="row m-0">
        <div class="col-6">{{ trans('index.bet_area.hi') }} <span class="player">{{ $player['account'] }}</span></div>
        <div class="col-6 text-right" onclick="refreshBalence()">
            <span class="text-orange balance">{{ $player['balance'] }}</span>
            <i id="refreshIcon" class="fa-solid fa-arrows-rotate ml-1"></i>
        </div>
        <div class="col-12">
            <p class="fs-4 mb-0 mb-2 mt-4" key='series'></p>
        </div>
        <div class="col-12">
            <p class="mb-3">
                <img key='homeLogo' src="" class="teamLogo" onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'">
                <span key='home'></span>
                <span style="font-style:italic;">&ensp;VS&ensp;</span>
                <img key='awayLogo' src="" class="teamLogo" onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'">
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
                            <img src="{{ $ser['series']['logo'] }}" class="serieslogo" onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'">
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
                                        <!-- <div class="indexBetCardLabel">
                                            @if(isset($item['series']['name']))
                                                <div class="indexBetCardSpan textOverFlow">{{ $item['series']['name'] }}</div>
                                            @endif
                                        </div> -->
                                        <div class="indexBetCard">
                                            <div class="indexBetCardInfo" onclick="changeTab('{{ $key }}', {{ $key2 }}, {{ $key3 }})">
                                                <div class="timeSpan">{{ trans('index.mainArea.time') }}<span class="timer" series_id="{{ $ser['series']['id'] }}">{{ $item['start_time'] }}</span>
                                                </div>
                                                <div class="w-100" style="display: inline-flex;">
                                                    @foreach ($item['teams'] as $team)
                                                        @if($team['index'] === 1)
                                                            @if(isset($team['team']['logo']))
                                                                <div style="width: 20%;">
                                                                    <img src="{{ $team['team']['logo'] }}" class="teamlogo" onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'">
                                                                </div>
                                                            @endif
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
                                                            @if(isset($team['team']['logo']))
                                                                <div style="width: 20%;">
                                                                    <img src="{{ $team['team']['logo'] }}" class="teamlogo" onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'">
                                                                </div>
                                                            @endif
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
                                            <!-- 其他玩法 -->
                                            @if (isset($item['rate']))
                                                <!-- 判斷有沒有其他玩法 -->
                                                <div class="otherbet">
                                                    @foreach ($item['rate'] as $key4 => $rate)
                                                        @if (!in_array($key4, trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr')))
                                                            <div class='toggleOtherBtn' id="{{ $item['match_id'] }}_{{ $key4 }}" onclick="toggleOther('{{ $item['match_id'] }}_{{ $key4 }}')">
                                                                <span>{{ $rate[0]['name'] }}</span>
                                                                <span id="dir_{{ $item['match_id'] }}_{{ $key4 }}">▸</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                @foreach ($item['rate'] as $key5 => $rate)
                                                    @if (!in_array($key5, trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr')))
                                                        <div id="otherBet_{{ $item['match_id'] }}_{{ $key5 }}" class="otherBetArea">
                                                            <div class="indexBetCardTable row text-center" sport="{{ $search['sport'] }}">
                                                                <div class="indexBetCardInfo">
                                                                    <div class="fw-600 ml-1 w-100">{{ $rate[0]['name'] }}</div>
                                                                </div>
                                                                <div style="width: 72%;" class="m-0 row p-0 otherbet_item_container">
                                                                    @if($key5 === 7 || $key5 === 8 )
                                                                        @foreach ($rate as $subrate)
                                                                            @for ($i = 0; $i < 3; $i ++ ) 
                                                                                <div class="col-2 p-0">
                                                                                    <div>
                                                                                        @switch($i)
                                                                                            @case(0)
                                                                                                {{ trans('index.mainArea.homeWin') }}
                                                                                            @break
                                                                                            @case(1)
                                                                                                {{ trans('index.mainArea.tie') }}
                                                                                            @break
                                                                                            @case(2)
                                                                                                {{ trans('index.mainArea.awayWin') }}
                                                                                            @break
                                                                                        @endswitch
                                                                                    </div>
                                                                                        @foreach ($subrate['rate'][$i] as $sssubrate)
                                                                                            <div class="betItemDiv" game_priority="{{ $subrate['game_priority'] }}" onclick="openCal($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $subrate['rate_id'] }}" rate_name="{{ $subrate['name'] }}" rate="{{ $sssubrate['id'] }}" bet_name="{{ $sssubrate['name'] }}" key='{{ $key }}' key2='{{ $key2 }}' key3='{{ $key3 }}' rate_status="{{ $subrate['status'] }}" rate_item_status="{{ $sssubrate['status'] }}" risk="{{ $sssubrate['risk'] }}">
                                                                                                <span>{{ $sssubrate['value'] }}</span>&ensp;
                                                                                                <span class="odd">{{ $sssubrate['rate'] }}</span>
                                                                                                <i class="fa-solid fa-lock"></i>
                                                                                            </div>
                                                                                        @endforeach
                                                                                </div>
                                                                            @endfor
                                                                        @endforeach
                                                                    @else
                                                                        @foreach ($rate as $subrate)
                                                                            <div class="col-2 p-0 otherbet_col">
                                                                                <div class="col-12 p-0">
                                                                                    {{ $subrate['name'] }}
                                                                                </div>
                                                                                @foreach (collect($subrate['rate'])->sortKeys() as $ssubrate)
                                                                                    <div class="col-12 p-0">
                                                                                        <div class="betItemDiv" index='{{ $loop->index }}' game_priority="{{ $subrate['game_priority'] }}" onclick="openCal($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $subrate['rate_id'] }}" rate_name="{{ $subrate['name'] }}" rate="{{ $ssubrate['id'] }}" bet_name="{{ $ssubrate['name'] }}" class="h-100" key='{{ $key }}' key2='{{ $key2 }}' key3='{{ $key3 }}' rate_status="{{ $subrate['status'] }}" rate_item_status="{{ $ssubrate['status'] }}" risk="{{ $ssubrate['risk'] }}">
                                                                                            @if (in_array($key5, trans('index.priorityArr.1')))
                                                                                                <div class="row m-0">
                                                                                                    <div class="col-8 p-0 textOverFlow">
                                                                                                        {{ $ssubrate['name'] }}
                                                                                                    </div>
                                                                                                    <div class="col-4 p-0">
                                                                                                        <span class="odd">{{ $ssubrate['rate'] }}</span>
                                                                                                        <i class="fa-solid fa-lock"></i>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @else
                                                                                                <span>
                                                                                                    @if($ssubrate['value'] !== '')
                                                                                                        {{ $ssubrate['value'] }}
                                                                                                    @else
                                                                                                        {{ $ssubrate['name'] }}
                                                                                                    @endif
                                                                                                </span>&ensp;
                                                                                                <span class="odd">{{ $ssubrate['rate'] }}</span>
                                                                                                <i class="fa-solid fa-lock"></i>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                            <!-- 其他玩法 -->
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
        <div id="loader" style="display: none">
            <div class="loading loading04">
                <span>L</span>
                <span>O</span>
                <span>A</span>
                <span>D</span>
                <span>I</span>
                <span>N</span>
                <span>G</span>
                <span>.</span>
                <span>.</span>
                <span>.</span>
            </div>
        </div>
        <div id="noMoreData" style="display: none">
            <p class="mb-0">{{ trans('index.mainArea.nomredata') }}</p>
        </div>
    </div>
</div>
@endsection



@section('styles')
<link href="{{ asset('css/index.css?v=' . $system_config['version']) }}" rel="stylesheet">
<link href="{{ asset('css/index_ind.css?v=' . $current_time) }}" rel="stylesheet">
@endSection

@push('main_js')

<!-- 解壓縮 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pako/2.0.3/pako.min.js"></script> 
<script>
    // 目前賽事列表
    var match_list = @json($match_list);
    // 紀錄右邊tab現在是哪場賽事 -> 如果該賽事被關閉 則自動顯示目前第一場比賽
    var indexRightMatchId = null
    // websocket用
    var socket_status = false;
    var ws = null
    const sport = parseInt(searchData.sport)

    // 投注限額
    const limit = JSON.parse(@json(session('player.limit_data')));
    var betTimer = null
    var min = null
    var max = null

    // 監聽比賽時間
    var matchStartTime = {}
    var matchTimerInter = null

    // 語系
    var langTrans = @json(trans('index'));

    console.log("system.config");
    console.log(@json($system_config));

    console.log("menu_count");
    console.log(@json($menu_count));
    
    console.log("sport_list");
    console.log(@json($sport_list));

    console.log("series_list");
    console.log(@json($series_list));

    console.log("match_list");
    console.log(@json($match_list));

    console.log("match_status");
    console.log(@json($match_status));

    console.log("notice_list");
    console.log(@json($notice_list));

    console.log("search");
    console.log(@json($search));

    console.log("lang");
    console.log(@json($lang));

    console.log("limit_data");
    console.log(@json(session('player.limit_data')));

    // 其他玩法 太多顯示不完就隱藏
    var otherbetCountLimit = 0
    if (window.innerWidth <= 1600) {
        otherbetCountLimit = 6
    } else {
        otherbetCountLimit = 8
    }


    /* ===== DATA LAYER ===== */
    /*  
        1. 現在大部份資料都api化，通過call ajax來loading
        2. 在所有所需的api被call完之前，要添加頁面loading樣式，等全部都call好了才顯示頁面
        
            有那些需要call api?
            1. match_list
            2. account data
            3. sport list
            4. bet limitation?

            有哪些沿用laravel映射?
            1. player
    
        3. 資料接收機制
            1. ws -> push to queue -> update the globe data
            2. ajax -> update the globe data
    */

    // ===== 測試 =====

    // player and sport_id
    const player = 8
    const token = 12345
    const sport_id = 1

    const apiWaitCount = 2 // ready中有幾個api要call
    var apiCalledCount = 0 // 現在幾個call好了
    var isReady = false

    // 列表
    var matchList = {}
    var callMatchListData = { token: token, player: player, sport_id: sport_id }
    const matchList_api = 'https://sportc.asgame.net/api/v1/match_index'
   
    // 帳號
    var account = {}
    var callAccountData = { token: token, player: player }
    const account_api = 'https://sportc.asgame.net/api/v1/common_account'

    // ===== 測試 =====
    

    /* ===== DATA LAYER ===== */


    function caller( url, data, obj ) {
        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(data) {
                const json = JSON.parse(data); 
                // 先判定要不要解壓縮
                if(json.gzip === 1) {
                    // 將字符串轉換成 ArrayBuffer
                    const str = json.data;
                    const bytes = atob(str).split('').map(char => char.charCodeAt(0));
                    const buffer = new Uint8Array(bytes).buffer;
                    // 解壓縮 ArrayBuffer
                    const uncompressed = JSON.parse(pako.inflate(buffer, { to: 'string' }));
                    json.data = uncompressed
                }
                obj = json
                apiCalledCount++
                if(isReady === false && apiCalledCount === apiWaitCount) {
                    $('#dimmer').dimmer('show');
                    $('#wrap').removeAttr('hidden');
                    isReady = true;
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // 处理错误
                console.error('Ajax error:', textStatus, errorThrown);
            }
        });
    }


    $(document).ready(function() {

        // ajaxTest

        caller(matchList_baseApi, callMatchListData, matchList )

        $.ajax({
            url: matchList_baseApi,
            method: 'POST',
            data: {
                token: token,
                player: player,
                sport_id: sport_id
            },
            success: function(data) {
                const json = JSON.parse(data); 
                // 先判定要不要解壓縮
                if(json.gzip === 1) {
                    // 將字符串轉換成 ArrayBuffer
                    const str = json.data;
                    const bytes = atob(str).split('').map(char => char.charCodeAt(0));
                    const buffer = new Uint8Array(bytes).buffer;
                    // 解壓縮 ArrayBuffer
                    const uncompressed = JSON.parse(pako.inflate(buffer, { to: 'string' }));
                    json.data = uncompressed
                }
                console.log(json)
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // 处理错误
                console.error('Ajax error:', textStatus, errorThrown);
            }
        });


        // ajaxTest



        // 若有滾球  移到最上面
        if ( match_list['living'] !== undefined ) {
            let parentNode = $('#indexContainerLeft')
            let livingNode = $('#toggleContent_living')
            livingNode.prependTo(parentNode);
            $('#indexContainerRightInfo').css("display","none")
            $('#indexContainerRightLiving').css("display","block")

        }

        // 若數量為0 隱藏
        $('.catWrapperTitle').each(function(){
            let count = parseInt($(this).find('span[id^="catWrapperContent"]').html())
            if(count === 0) $(this).hide()
        })

        // 打開第一個
        if($('div[id^=toggleContent_]:visible').length > 0) {
            setTimeout(() => {
                $('.catWrapperTitle:visible').eq(0).click()
            }, 500);
        }

        // 右邊 -> 預設第一比賽事
        $('.indexBetCardInfo').eq(0).click()
        // 判斷status:  -1 hide / 1 open / 2 lock / 4 5 other remove
        rateStatusJudge(0, 1, 0, 1)
        // 判斷局數
        stageJudge()
        // 其他玩法 -> 如果status全部不符合顯示條件 移除按鈕及投注區塊 
        isOtherBetEmpty()
        // 右邊 -> 如果status全部不符合顯示條件 移除title 
        // clearUnusedRight() 
        // 文字太長處理
        fixTextOverflow()
        // tab初始化
        $('.menu .item').tab();
        // 排版補空
        fillEmpty()
        
        // websocket
        WebSocketDemo(); // 連線
        setInterval(reconnent, 5000); // 監聽連線狀態

        $('.otherbet').each(function() {
            $(this).find('.toggleOtherBtn').each(function(index) {
                if (index >= otherbetCountLimit) {
                    let id = $(this).attr('id')
                    $('#otherBet_' + id).remove()
                    $(this).remove()
                }
            });
        });

        // 統計
        statistics()
    });

    function isOtherBetEmpty() {
        $('.otherBetArea').each(function(){
            let count = $(this).find('.betItemDiv').length
            if(count === 0) {
                let btnIdArr = $(this).attr('id').split('_')
                let btnId = btnIdArr[1] + '_' + btnIdArr[2]
                $(this).remove()
                $('#' + btnId).remove()
            }
        })
    }

    // 判斷局數
    function stageJudge() {
        for (let key in match_list) {
            let val = match_list[key];
            for (let k = 0; k < val.length; k++) {
                let v = val[k];
                for (let k1 = 0; k1 < v.list.length; k1++) {
                    let v1 = v.list[k1];
                    let start_time = new Date(v1.start_time).getTime();
                    let current_time = new Date().getTime();
                    let stage = v1?.teams[0]?.scores.length
                    if ( !stage ) stage = 0
                    if ( current_time > start_time && stage > 0 ) {
                        $('.indexEachCard[key="' + v1.match_id + '"] .timeSpan').html( langTrans['sportBetData'][sport]['stage'][stage-1] )
                    }
                    v1.stageStr = langTrans['sportBetData'][sport]['stage'][stage-1]
                }
            }
        }
    }

    // 判斷賽是status狀態
    function matchStatusJudge(match_id, updateMatchStatus, series_id) {
        switch (updateMatchStatus) {
            case -1:
                $(this).hide(1000)
                break;
            case 1:
                $(this).show(1000)
                break;
            case 2:
                // 移到滾球中
                var isSeriesExi = false  // 滾球中是否已經有這個聯賽
                if($('#catWrapperContent_living .seriesWrapperTitle[series_id="' + series_id + '"]').length > 0 ) {
                    isSeriesExi = true
                    var insertContainerId = $('#catWrapperContent_living .seriesWrapperTitle[series_id="' + series_id + '"]').attr('id')
                    insertContainerId = insertContainerId.replace('seriesWrapperTitle', 'seriesWrapperContent')
                } else {
                    var parentContainer = $('.indexEachCard[key="' + match_id + '"]').closest('.seriesWrapperContent').clone()
                    var parentTitle = $('.indexEachCard[key="' + match_id + '"]').closest('.seriesWrapperContent').prev().clone()
                    parentContainer.attr('id', parentContainer.attr('id') + '_living')
                    let newTitleId = parentTitle.attr('id') + '_living'
                    parentTitle.attr('id', newTitleId) // 新id (因為移上去之後有可能重複)
                    parentTitle.find('span').eq(1).attr('id', newTitleId + '_count') // 統計
                    parentTitle.find('span').eq(2).attr('id', newTitleId + '_dir') // 箭頭
                    let toggleId = parentTitle.attr('id').replace('seriesWrapperTitle_', '')
                    parentTitle.attr('onclick', "toggleSeries('" + toggleId + "')")
                    parentContainer.find('.indexEachCard').each(function(){
                        if(parseInt($(this).attr('key')) !== parseInt(match_id)) $(this).remove()
                    })
                }

                // 插入滾球中
                if(isSeriesExi) {
                    $('.indexEachCard[key="' + match_id + '"]').appendTo($('#' + insertContainerId))
                    // 製造展開效果
                    $('.indexEachCard[key="' + match_id + '"]').hide()
                    $('.indexEachCard[key="' + match_id + '"]').show(1000)
                } else {
                    parentTitle.appendTo('#catWrapperContent_living')
                    parentContainer.appendTo('#catWrapperContent_living')
                    // 製造展開效果
                    parentTitle.hide()
                    parentContainer.hide()
                    parentTitle.show(1000)
                    parentContainer.show(1000)
                    // 移除原本的
                    closeMatch($('.indexEachCard[key="' + match_id + '"]').eq(1).closest('.seriesWrapperContent').attr('id'), match_id)
                }
                break;
            default:
                closeMatch($('.indexEachCard[key="' + match_id + '"]').closest('.seriesWrapperContent').attr('id'), match_id)
                break;
        }

        // 統計
        setTimeout(() => {
            statistics()
        }, 1000);
    }

   // 判斷賠率status狀態
    function rateStatusJudge( match = 0, center = 0, right = 0, bottom = 0 ) {
        var selector = null
        if( match === 0 ) {
            selector = $('.betItemDiv')
        } else {
            selector = $('#indexContainerRight .betItemDiv, .indexEachCard[key="' + match + '"] .betItemDiv')
        }
        selector.each(function() {
            // 定位玩法資料
            let rate_status = parseInt($(this).attr('rate_status'));
            let rate_item_status = parseInt($(this).attr('rate_item_status'));
            let rate_id = parseInt($(this).attr('rate_id'))
            let rate_item_id = parseInt($(this).attr('rate'))
            let match_id = parseInt($(this).attr('match_id'))
            let risk = parseInt($(this).attr('risk'))
            let key = $(this).attr('key')
            let key2 = $(this).attr('key2')
            let key3 = $(this).attr('key3')
            let game_priority = parseInt($(this).attr('game_priority'))
            let result = match_list[key]?.[key2]?.['list']?.[key3]?.['rate']?.[game_priority]
            let result2 = match_list[key]?.[key2]?.['list']?.[key3]?.['rate']?.[game_priority]

            // 有效盤口 規則 
            // rate status === 1 && rate_item_status === 1 -> open
            // rate status === 1 && rate_item_status !== 1 -> close
            // rate status !== 1 -> close

            // 右邊
            if ( $(this).closest('.segmentContainer').length !== 0 && right === 1 ) {
                switch (rate_status) {
                    case -1:
                        // 右邊
                        $(this).hide() 
                        $('#' + rate_item_id + '_label').hide()
                        break;
                    case 1:    
                        // 右邊
                        $(this).show() 
                        $('#' + rate_item_id + '_label').show()
                        if( rate_item_status == 1 && risk === 0 ) { // open
                            $(this).find('i').hide();
                            $(this).find('.odd').show();
                        } else {
                            $(this).find('i').show();
                            $(this).find('.odd').hide();
                        }
                        break;
                    case 2:
                        // 右邊
                        $(this).show() 
                        $('#' + rate_item_id + '_label').show()

                        $(this).find('i').show();
                        $(this).find('.odd').hide();
                        break;
                    default:
                        result = result.find( item => item.rate_id === rate_id )
                        // 判断是否所有数据的status都大于2
                        const allStatusGreaterThan2 = Object.values(result.rate).every(rate => rate.status > 2);
                        if ( rate_id > 2 && allStatusGreaterThan2 ) {
                            // 右邊
                            $(this).remove() 
                            $('#' + rate_item_id + '_label').remove()
                        } else {
                            // 右邊
                            $(this).show() 
                            $('#' + rate_item_id + '_label').show()

                            $(this).find('i').show();
                            $(this).find('.odd').hide();
                        }
                        break;
                }
            }

            // 下面
            if ( $(this).closest('.otherBetArea').length !== 0 && bottom === 1) {
                switch (rate_status) {
                    case -1:
                        $(this).closest('.otherbet_col').hide() // 下面
                        break;
                    case 1:    
                        $(this).closest('.otherbet_col').show() // 下面
                        if( rate_item_status == 1 && risk === 0 ) { // open
                            $(this).find('i').hide();
                            $(this).find('.odd').show();
                        } else {
                            $(this).find('i').show();
                            $(this).find('.odd').hide();
                        }
                        break;
                    case 2:
                        $(this).closest('.otherbet_col').show() // 下面
                        $(this).find('i').show();
                        $(this).find('.odd').hide();
                        break;
                    default:
                        result = result.find( item => item.rate_id === rate_id )
                        // 判断是否所有数据的status都大于2
                        const allStatusGreaterThan2 = Object.values(result.rate).every(rate => rate.status > 2);

                        if ( rate_id > 2 && allStatusGreaterThan2 ) {
                            $(this).closest('.otherbet_col').remove() // 下面
                        } else {
                            $(this).closest('.otherbet_col').show() // 下面
                            $(this).find('i').show();
                            $(this).find('.odd').hide();
                        }
                        break;
                }
            }

            // 中間那六格固定的
            if ($(this).closest('.indexBetCard').length !== 0 && $(this).closest('.otherBetArea').length === 0 && center === 1) {
                var betData = null
                var index = parseInt($(this).attr('index'))
                if ( index !== 0 ) return;
                if ( !result ) return;
                
                // 尋找有效盤口
                result = result.filter(item => item.status == 1) // rate_status === 1
                if( result.length > 0 ) {
                    result.forEach(item => {
                        const rateStatus1Count = Object.values(item.rate).filter(rateItem => rateItem.status === 1).length;
                        item.rateStatus1Count = rateStatus1Count;
                    });
                    result = result.filter(item => item.rateStatus1Count > 0) // rate_item_status 至少有一個為1

                    if( result.length > 0 ) {
                        // 按照时间戳从大到小排序
                        result = result.sort((a, b) => b.updated_at - a.updated_at);
                        // 按照rate_id排序
                        result = result.sort((a, b) => {
                            if (b.updated_at === a.updated_at) {
                                return b.rate_id - a.rate_id;
                            }
                            return b.updated_at - a.updated_at;
                        });
                        // 移除添加的临时字段
                        result.forEach(item => {
                            delete item.rateStatus1Count;
                        });
                    }
                }

                betData = result[0]
                
                if ( !betData ){
                    result2 = result2.sort((a, b) => b.rate_id - a.rate_id);
                    betData = result2[0]
                } 

                var siblingsCount = $(this).siblings('.betItemDiv').length + 1
                var loop = Array.from({ length: siblingsCount }, (_, index) => index)
                // 足球棒球  獨贏 主客平 -> 主平客
                if( ((game_priority == 5 || game_priority == 6 ) && sport === 1) || (game_priority == 28) && sport === 3 ) {
                    loop = [0, 2, 1]
                }

                var parentNode = $(this).closest('.col-2')
                loop.forEach(function(item, i) {
                    var isOpenBet = false
                    let betItemData = Object.values(betData.rate)[item]
                    $(parentNode).find('.betItemDiv').eq(i).attr('rate_id', betData.rate_id)
                    $(parentNode).find('.betItemDiv').eq(i).attr('bet_name', betItemData.name)
                    $(parentNode).find('.betItemDiv').eq(i).attr('rate', betItemData.id)
                    $(parentNode).find('.betItemDiv').eq(i).attr('rate_item_status', betItemData.status)
                    $(parentNode).find('.betItemDiv').eq(i).attr('risk', betItemData.risk)
                    $(parentNode).find('.betItemDiv').eq(i).attr('rate_status', betData.status)
                    $(parentNode).find('.betItemDiv').eq(i).find('.odd').html(betItemData.rate)
                    
                    if (langTrans.priorityArr[2].indexOf(game_priority.toString()) !== -1) {
                        // 大小系列
                        $(parentNode).find('.betItemDiv').eq(i).find('.rate_name').html(betItemData.name)
                    } else {
                        $(parentNode).find('.betItemDiv').eq(i).find('.rate_name').html(betItemData.value)
                    }


                    // 判斷選項開關
                    if ( betData?.status === 1 && Object.values(betData?.rate)[i]?.status === 1 && Object.values(betData?.rate)[i]?.risk === 0 ) {
                        isOpenBet = true
                    }

                    if( isOpenBet ) {
                        $(parentNode).find('.betItemDiv').eq(i).find('i').hide();
                        $(parentNode).find('.betItemDiv').eq(i).find('.odd').show();
                    } else {
                        $(parentNode).find('.betItemDiv').eq(i).find('i').show();
                        $(parentNode).find('.betItemDiv').eq(i).find('.odd').hide();
                    }
                });
            }
        });
    }

    // 統計 比賽總數
    function statistics() {
        $('.seriesWrapperContent').each(function(){
            let count = $(this).find('.indexEachCard').length
            let titelId = $(this).attr('id').replace('seriesWrapperContent', 'seriesWrapperTitle')
            titelId += '_count'
            $('#' + titelId).html(count)
            if(count === 0) {
                $(this).prev().remove() // title
                $(this).remove() // content
            }
        })
        $('div[id^="catWrapperContent_"]').each(function() {
            let totalSpanId = $(this).attr('id') + '_total'
            let total = $(this).find('.indexEachCard[status!=-1]').length
            $('#' + totalSpanId).html(total)
        })

        let allPageTotal = $('#indexContainerLeft').find('.indexEachCard[status!=-1]').length
        $('.menuStatistics_' + sport).html(allPageTotal)


        if( allPageTotal === 0) {
            $('#noData').show()
        }
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

    // $('.menu .item').click(function(){
    //     clearUnusedRight()
    // })

    // 右邊 -> 如果status全部不符合顯示條件 移除title 
    function clearUnusedRight(){
        // $('.eachGroup:visible').each(function(){
        //     let count = $(this).find('.betItemDiv').length
        //     let lockCount = $(this).find('i:visible').length
        //     if(count === lockCount) {
        //         $(this).remove()
        //     }
        // })
        $('.tabCardContent').each(function(){
            let count = $(this).find('.betItemDiv').length
            if(count === 0) {
                $(this).prev().remove()
                $(this).html('')
                $(this).css('margin-bottom', 0)
            }
        })

        $('.segmentContainer').each(function(){
            let count = $(this).find('.betItemDiv').length
            let nogameCount = $(this).find('.nogameitem').length
            if(count === 0 && nogameCount === 0 ) {
                $(this).html('')
                $(this).append('<div class="nogameitem"><i class="fa-solid fa-circle-exclamation"></i><p class="mb-0">' + langTrans.mainArea.nogameitem + '</p></div>')
            }
        })
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

    // 註冊賽事id
    function wsRegisterMatch() {
        // 要註冊給ws的id陣列 ( 聯賽id )
        var registerId = $('.seriesWrapperTitle').map(function() {
            return parseInt($(this).attr('series_id'));
        }).get();

        const wsMsg = {
            "action": "register",
            "channel": 'match',
            "player": player_id,
            "game_id": parseInt(searchData.sport),
            "series": registerId // 要註冊的賽事
        }
        console.log('ws match send -> ')
        console.log(wsMsg)
        ws.send(JSON.stringify(wsMsg));
    }

    var testStatus = 1
    var testRate = 10
    function addStatus() {
        testStatus += 1
        $('#testStatus').html(testStatus)
    }
    function delStatus() {
        testStatus -= 1
        $('#testStatus').html(testStatus)
    }
    function addRate() {
        testRate += 5
        $('#testRate').html(testRate)
    }
    function delRate() {
        testRate -= 5
        $('#testRate').html(testRate)
    }
    // websocket
    var heartbeatTimer = null
    function WebSocketDemo() {
        console.log('WebSocketDemo')
        if ("WebSocket" in window) {
            try {
                var sport_id = @json($search['sport']);
                var ws_url = langTrans['sportBetData'][sport]['ws']

                ws = new WebSocket(ws_url); // websocket 連線
                ws.onopen = function() {
                    socket_status = true; // 重連機制
                    wsRegisterMatch() // 註冊id
                    heartbeatTimer = setInterval(() => { // 心跳 
                        const heartbeat = {
                            "action": "heartbeat",
                        }
                        // console.log('前端send msg ->')
                        console.log(heartbeat)
                        ws.send(JSON.stringify(heartbeat));
                    }, 10000);
                };
                // websocket 關閉了
                ws.onclose = function() {
                    console.log('Connection closed with code: ', event.code);
                    socket_status = false;
                    clearInterval(heartbeatTimer) // 移除心跳timer
                };
                // websocket收到訊息
                ws.onmessage = function(message) {
                    var msg = JSON.parse(message.data);
                    // console.log(msg)
                    // 定位舊資料
                    var originalData = null;
                    var key1 = null
                    var key2 = null
                    var key3 = null
                    // 波膽記錄用
                    var key4 = null
                    var key5 = null
                    var key6 = null

                    var match = msg.match_id
                    if( msg.channel === 'risk' ) match = msg.data.match_id

                    
                    for (let key in match_list) {
                        key1 = key
                        let val = match_list[key];
                        for (let k = 0; k < val.length; k++) {
                            key2 = k
                            let v = val[k];
                            for (let k1 = 0; k1 < v.list.length; k1++) {
                                key3 = k1
                                let v1 = v.list[k1];
                                if (v1.match_id === match) {
                                    originalData = v1
                                    break; 
                                }
                            }
                            if (originalData !== null) {
                                break;
                            }
                        }
                        if (originalData !== null) {
                            break;
                        }
                    }


                    if( !originalData ) return;


                    // 控盤
                    if( msg.channel === 'risk' ) {
                        // pos
                        let pos = JSON.parse(msg.data.pos)
                        Object.entries(pos).forEach(([k, v]) => {
                            // console.log(k, v)

                            // 定位中間
                            let item = $('.indexEachCard[key=' + msg.data.match_id + '] .betItemDiv[game_priority=' + msg.data.game_priority + '][index=' + k + ']')
                            // 定位右邊
                            let itemR = $('#indexContainerRight[match=' + msg.data.match_id + '] .betItemDiv[game_priority=' + msg.data.game_priority + ']').eq(k)


                            // rate status
                            let originalRateStatus = parseInt($(item).attr('rate_status'))
                            // rate item status
                            let originalRateItemStatus = parseInt($(item).attr('rate_item_status'))

                           
                            switch (v) {
                                case 0: // 按照原本狀態
                                    if( originalRateStatus === 1 && originalRateItemStatus === 1 ) {
                                        // 中間
                                        item.find('i').hide()
                                        item.find('.odd').show()

                                        // 右邊
                                        itemR.find('i').hide()
                                        itemR.find('.odd').show()
                                    } else {
                                        // 中間
                                        item.find('i').show()
                                        item.find('.odd').hide()

                                        // 右邊
                                        itemR.find('i').show()
                                        itemR.find('.odd').hide()
                                    }
                                    break;
                                case 1: // 按照控端狀態
                                    // 中間
                                    item.find('i').show()
                                    item.find('.odd').hide()

                                    // 右邊
                                    itemR.find('i').show()
                                    itemR.find('.odd').hide()
                                    break;
                                default:
                                    break;

                                
                            }


                            // 改變risk 不然右邊在收到封包之前被點擊之後會消失判斷
                            const targetRateList = originalData.rate[msg.data.game_priority];
                            for (let i = 0; i < targetRateList.length; i++) {
                                const rateData = targetRateList[i];
                                const rateKeys = Object.keys(rateData.rate);
                                if (rateKeys.length > k) {
                                    // 找到第 k 比資料，設置 risk 
                                    rateData.rate[rateKeys[k]].risk = v;
                                }
                            }
                        });
                    }
                    

                    // 比分變化
                    if (msg.action === 'update' && msg.channel === 'match') {
                        // 判斷局數
                        let stage = msg.data.teams[0].scores.length
                        let stageStr = langTrans['sportBetData'][sport]['stage'][stage-1]
                        
                        $('.indexEachCard[key="' + msg.match_id + '"] .timeSpan').html(stageStr) // 更新局數 (中間)
                        $('#indexContainerRightInfo[match="' + msg.match_id + '"] .gameStage').html(stageStr) // 更新局數 (右邊)
                        // 更新時間 -> 局數 (原本使用者右邊並不是更新的比賽 更新後點開不會有局數資料)
                        match_list[key1][key2]['list'][key3].stageStr = stageStr

                        // 判斷比分
                        let updateHomeScore = msg.data.teams.find(item => item.index === 1).total_score
                        let updateAwayScore = msg.data.teams.find(item => item.index === 2).total_score
                        let originalHome = originalData.teams.find(item => item.index === 1).total_score
                        let originalAway = originalData.teams.find(item => item.index === 2).total_score
                        // 更新比分 (中間)
                        $('.indexEachCard[key="' + msg.match_id + '"] .scoreSpan').eq(0).html(updateHomeScore)
                        $('.indexEachCard[key="' + msg.match_id + '"] .scoreSpan').eq(1).html(updateAwayScore)
                        // 更新比分 (右邊)
                        $('#indexContainerRightInfo[match="' + msg.match_id + '"] .teamScore').eq(0).html(updateHomeScore)
                        $('#indexContainerRightInfo[match="' + msg.match_id + '"] .teamScore').eq(1).html(updateAwayScore)

                        // 改變status
                        let originalMatchStatus = match_list[key1][key2]['list'][key3].status
                        let updateMatchStatus = msg.data.status
                        match_list[key1][key2]['list'][key3].status = updateMatchStatus
                        $('.indexEachCard[key="' + msg.match_id + '"]').attr('status', updateMatchStatus)
                        if( originalMatchStatus !== updateMatchStatus ) {
                            matchStatusJudge(msg.match_id, updateMatchStatus, msg.data.series.id)
                        }
                        
                        // 更新match_list資料 以便下次做比對
                        match_list[key1][key2]['list'][key3].teams.find(item => item.index === 1).total_score = updateHomeScore
                        match_list[key1][key2]['list'][key3].teams.find(item => item.index === 2).total_score = updateAwayScore

                        // 添加樣式 -> 3秒後移除
                        if( updateHomeScore > originalHome ) {
                            $('.indexEachCard[key="' + msg.match_id + '"] .scoreSpan').eq(0).addClass('raiseScore')
                            $('#indexContainerRightInfo[match="' + msg.match_id + '"] .teamScore').eq(0).addClass('raiseScore')
                            setTimeout(() => {
                                $('.indexEachCard[key="' + msg.match_id + '"] .scoreSpan').eq(0).removeClass('raiseScore')
                                $('#indexContainerRightInfo[match="' + msg.match_id + '"] .teamScore').eq(0).removeClass('raiseScore')
                            }, 3000);
                        }
                        if( updateAwayScore > originalAway ) {
                            $('.indexEachCard[key="' + msg.match_id + '"] .scoreSpan').eq(1).addClass('raiseScore')
                            $('#indexContainerRightInfo[match="' + msg.match_id + '"] .teamScore').eq(1).addClass('raiseScore')
                            setTimeout(() => {
                                $('.indexEachCard[key="' + msg.match_id + '"] .scoreSpan').eq(1).removeClass('raiseScore')
                                $('#indexContainerRightInfo[match="' + msg.match_id + '"] .teamScore').eq(1).removeClass('raiseScore')
                            }, 3000);
                        }
                    }

                    // 賠率變化
                    if ( ( msg.action === 'update' || msg.action === 'update-B' ) && msg.channel === 'match-group') {
                        var isAppendNewBet = false
                        var isAppendNewRate = false
                        if (originalData && originalData.rate && originalData.rate[msg.game_priority] !== undefined) {
                            // 原本就有的game_priority
                            originalData = originalData.rate[msg.game_priority].find(item => item.rate_id === msg.rate_id)
                            var bd_originalData = originalData

                            if( originalData !== undefined ) {
                                // 原本就有的rate_id
                                msg.data.forEach(e => {
                                    if ( originalData.updated_at > e.updated_at ) return;
                                    let foundMatch = false;
                                    let origianlRate = null;
                                    let originalRateItemStatus = null;
                                    let originalRateStatus = null;
                                    let originalRateItemRisk = null;
                                    
                                    let updateRate = parseFloat(e.rate)
                                    let updateRateStatus = parseInt(msg.status)
                                    let updateRateItemStatus = parseInt(e.status)
                                    let updateRateItemRisk = parseInt(e.risk)

                                    // 波膽不用item id當key 要另外判斷
                                    if (msg.game_priority === 7 || msg.game_priority === 8) {
                                        originalData = bd_originalData
                                        for (let i = 0; i < 3; i++) {
                                            var bdval = originalData.rate[i];
                                            key4 = i
                                            for (let j = 0; j < bdval.length; j++) {
                                                let element = bdval[j]
                                                if (element.id === e.id) {
                                                    originalData = element;
                                                    origianlRate = parseFloat(element.rate);
                                                    originalRateItemStatus = parseInt(element.status);
                                                    originalRateItemRisk = parseInt(element.risk)
                                                    foundMatch = true;
                                                }
                                                if (foundMatch) {
                                                    break;
                                                }
                                            }
                                            if (foundMatch) {
                                                break;
                                            }
                                        }
                                    } else {
                                        origianlRate = parseFloat(originalData.rate[e.id].rate);
                                        originalRateStatus = parseInt(originalData.status)
                                        originalRateItemStatus = parseInt(originalData.rate[e.id].status);
                                        originalRateItemRisk = parseInt(originalData.rate[e.id].risk)
                                    }

                                    // 更新match_list資料 以便下次做比對
                                    if( msg.game_priority === 7 || msg.game_priority === 8 ) {
                                        match_list[key1][key2]['list'][key3].rate[msg.game_priority].find(item => item.rate_id === msg.rate_id)['rate'][key4].find(item => item.id === e.id).rate = e.rate
                                        match_list[key1][key2]['list'][key3].rate[msg.game_priority].find(item => item.rate_id === msg.rate_id)['rate'][key4].find(item => item.id === e.id).status = e.status
                                        match_list[key1][key2]['list'][key3].rate[msg.game_priority].find(item => item.rate_id === msg.rate_id)['rate'][key4].find(item => item.id === e.id).risk = e.risk
                                    } else {
                                        // risk
                                        match_list[key1][key2]['list'][key3].rate[msg.game_priority].find(item => item.rate_id === msg.rate_id).rate[e.id].risk = e.risk
                                        // rate
                                        match_list[key1][key2]['list'][key3].rate[msg.game_priority].find(item => item.rate_id === msg.rate_id).rate[e.id].rate = e.rate
                                        // rate_item_status
                                        match_list[key1][key2]['list'][key3].rate[msg.game_priority].find(item => item.rate_id === msg.rate_id).rate[e.id].status = updateRateItemStatus
                                        // rate_status
                                        match_list[key1][key2]['list'][key3].rate[msg.game_priority].find(item => item.rate_id === msg.rate_id).status = updateRateStatus
                                        // updatetime
                                        match_list[key1][key2]['list'][key3].rate[msg.game_priority].find(item => item.rate_id === msg.rate_id).updated_at = e.updated_at
                                    }

                                    if( originalRateStatus !== updateRateStatus || originalRateItemStatus !== updateRateItemStatus || originalRateItemRisk !== updateRateItemRisk ) {
                                        // 狀態判斷
                                        if ( indexRightMatchId === msg.match_id ) {
                                            changeTab(key1, key2, key3, 1)
                                        }

                                        rateStatusJudge(msg.match_id, 1, 0, 1)
                                    }

                                    if( origianlRate !== updateRate ) {
                                        // 賠率樣式
                                        if (updateRate > origianlRate && updateRateStatus === 1 && updateRateItemStatus === 1 ) {
                                            raiseOdd(msg.match_id, msg.rate_id, e.id, updateRate)
                                        }
                                        if (updateRate < origianlRate && updateRateStatus === 1 && updateRateItemStatus === 1 ) {
                                            lowerOdd(msg.match_id, msg.rate_id, e.id, updateRate)
                                        }

                                        // 計算機最高可贏金額
                                        let inputMoney = parseInt($('#moneyInput').val())
                                        let newRate = parseFloat($('.leftSlideOrderCard .odd').html())
                                        let maxWinning = (inputMoney * newRate).toFixed(2);
                                        $('#maxWinning').html(maxWinning)
                                    }
                                });
                            } else {
                                // 新增新的 rate_id 玩法
                                // console.log('new rate_id')
                                isAppendNewBet = true
                                isAppendNewRate = true
                            }
                        } else {
                            // 新增新的game_priority
                            // console.log('new game_priority')
                            isAppendNewBet = true
                        }

                        if ( isAppendNewBet ) {
                            let bet_name = langTrans.game_priority[msg.game_priority]
                            let insertRateData = msg.data.reduce((acc, item) => {
                                acc[item.id] = item;
                                item['name'] = item.name_cn // ws通知沒有語系
                                item['value'] = item.name_cn // ws通知沒有語系
                                return acc;
                            }, {});

                            let insertData = {
                                rate_id: msg.rate_id,
                                game_priority: msg.game_priority,
                                name: bet_name,
                                rate: insertRateData,
                                status: msg.status,
                                updated_at: msg.data[0].updated_at
                            }

                            if( !isAppendNewRate ) match_list[key1][key2]['list'][key3].rate[msg.game_priority] = []
                            match_list[key1][key2]['list'][key3].rate[msg.game_priority].push(insertData)

                            // 判斷這個玩法有沒有已經存在按鈕(otherbet)
                            if ( $('.indexEachCard[key="' + msg.match_id + '"] .toggleOtherBtn').length < otherbetCountLimit && langTrans.sportBetData[sport].priorityArr.indexOf((msg.game_priority).toString()) === -1 ) {
                                var isBtnExist = true
                                if ( $('#' + msg.match_id + '_' + msg.game_priority).length === 0 ) {
                                    // 不存在
                                    isBtnExist = false
                                }
                                if ( !isBtnExist ) {
                                    // 限制顯示玩法數量
                                    let btnStr = '<div class="toggleOtherBtn" id=' + msg.match_id + '_' + msg.game_priority + ' onclick="toggleOther(\'' + msg.match_id + '_' + msg.game_priority + '\')">'
                                    btnStr += '<span>' + bet_name + '</span>'
                                    btnStr += '<span id="dir_' + msg.match_id + '_' + msg.game_priority + '">▸</span>'
                                    btnStr += '</div>'
                                    $('.indexEachCard[key="' + msg.match_id + '"] .otherbet').append(btnStr)

                                    let containerStr = '<div id="otherBet_' + msg.match_id + '_' + msg.game_priority + '" class="otherBetArea">'
                                    containerStr += '<div class="indexBetCardTable row text-center" sport="' + sport + '">'
                                    containerStr += '<div class="indexBetCardInfo">'
                                    containerStr += '<div class="fw-600 ml-1 w-100">' + bet_name + '</div>'
                                    containerStr += '</div>'
                                    containerStr += '<div style="width: 72%;" class="m-0 row p-0 otherbet_item_container"></div>'
                                    $('.indexEachCard[key="' + msg.match_id + '"] .otherbet').after(containerStr)
                                }

                                let otherbetItemStr = '<div class="col-2 p-0 otherbet_col">'
                                otherbetItemStr += '<div class="col-12 p-0">' + bet_name + '</div>'
                                Object.entries(insertRateData).forEach(([key, item], i) => {
                                    otherbetItemStr += '<div class="col-12 p-0">'
                                    otherbetItemStr += '<div class="betItemDiv" index=' + i + ' game_priority=' + msg.game_priority + ' onclick="openCal($(this), 1)" match_id=' + msg.match_id + ' rate_id=' + msg.rate_id + ' rate_name="' + bet_name + '" rate=' + item.id + ' bet_name="' + item.name + '" key="' + key1 + '" key2=' + key2 + ' key3=' + key3 + ' status="1">'
                                    otherbetItemStr += '<span>' + item.name + '</span>'
                                    otherbetItemStr += '<span class="odd">' + item.rate + '</span>'
                                    otherbetItemStr += '<i class="fa-solid fa-lock"></i>'
                                    otherbetItemStr += '</div>'
                                    otherbetItemStr += '</div>'
                                });
                                otherbetItemStr += '</div>'

                                $('#otherBet_' + msg.match_id + '_' + msg.game_priority + ' .otherbet_item_container').append(otherbetItemStr)
                            }

                            // 更新右邊 changetab
                            if ( indexRightMatchId === msg.match_id ) {
                                changeTab(key1, key2, key3, 1)
                            }
                        }
                       
                    }
                }
            } catch (error) {
                // 處理WebSocket連接錯誤
                console.error(langTrans.js.websocket_connect_err, error);
            }
        } else {
            // The browser doesn't support WebSocket
            console.log("WebSocket NOT supported by your Browser!");
        }
    }

    // 重連機制
    function reconnent() {
        if (socket_status === false) {
            WebSocketDemo();
        }
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

    // 關閉比賽
    function closeMatch(seriesWrapperContent, match_id) {
        console.log('closeMatch', match_id)
        $('#' + seriesWrapperContent + ' .indexEachCard[key="' + match_id + '"]').hide(1000)
        // 隱藏動畫完後移除此場比賽
        setTimeout(() => {
            $('#' + seriesWrapperContent + ' .indexEachCard[key="' + match_id + '"]').remove()
        }, 1000);
        // 判斷右邊是不是現在這場比賽  如果是的話 選取第一場
        if (match_id === indexRightMatchId) {
            $('.indexBetCardInfo').eq(0).click() // 預設第一比賽事
        }

        $('div[id^="toggleContent"]').each(function(){
            let count = $(this).find('.indexEachCard').length
            if(count > 0) {
                $(this).find('.catWrapperTitle').show(1000)
                $(this).find('.catWrapperContent').show(1000)
            } 
        })
    }

    // 排版補空
    function fillEmpty() {
        // 補鎖頭
        $('.indexBetCardTable').each(function() {
            const parentElement = $(this);
            let maxCount = 0;
            $('.col-2', parentElement).each(function() {
                const divCount = $('div', this).length;
                if (divCount > maxCount) {
                    maxCount = divCount;
                }
            });
            $('.col-2', parentElement).each(function() {
                const divCount = $('div', this).length;
                if (divCount < maxCount) {
                    const diff = maxCount - divCount;
                    for (let i = 0; i < diff; i++) {
                        const divElement = $('<div><i class="fa-solid fa-lock"></i></div>');
                        $(this).append(divElement);
                    }
                }
            });
        });
    }

    // 其他玩法
    var lastToggleId = null
    function toggleOther(id) {
        var game_priority = id.split('_')[1]
        if (lastToggleId !== id) {
            $('#otherBet_' + lastToggleId).animate({ maxHeight: '0px' }, 0);
            $('#dir_' + lastToggleId).html('▸')
        }
        // 依照id顯示
        if ($('#otherBet_' + id).css('max-height') !== '0px') {
            $('#otherBet_' + id).animate({ maxHeight: '0px' }, 500);
            $('#dir_' + id).html('▸')
        } else {
            let calCount = 1 // default
            let rowCount = 9

            if( game_priority == 7 || game_priority == 8 ) {
                calCount = $('#otherBet_' + id + ' .col-2').eq(0).find('div').length + 1
                rowCount = calCount * 3
            } else {
                calCount = $('#otherBet_' + id + ' .col-2').length
                rowCount = Math.ceil(calCount / 6) * 9
            }
            $('#otherBet_' + id).animate({ maxHeight: rowCount + 'rem' }, 500);
            $('#dir_' + id).html('▾')
            // 文字太長處理
            setTimeout(() => {
                fixTextOverflow()
            }, 1000);
        }
        lastToggleId = id
    }


    //insert Record Table
    



    function insertRecordTable(record){
        $('#indexContainerRightLiving tbody').html('') //clear table
        var sportn = parseInt(searchData["sport"], 10);
        var homeData = record.teams.find(item => item.index === 1)
        var awayData = record.teams.find(item => item.index === 2)
        var scorehome = [];
        var scoreaway = [];
        let bbinsertStr = '<tr>'
        let insertStr = '<tr>'
                if (homeData !== undefined) {
                    homeData.scores.sort((a, b) => a.stage - b.stage);
                    homeData.scores.forEach(el => {
                    scorehome.push(el.score);
                });
                    
                    if (homeData.scores !== undefined) {
                        switch(sportn){
                            case 1: //if sport is soccer
                            insertStr += '<td class="nowrap home-name"><div class="d-flex align-items-center"><div><img src="' + homeData.team.logo + '" class="serieslogo" onerror="this.src=\'https://sporta.asgame.net/uploads/default.png?v=' + version + '\'" ></div><div class="text"><div>'+homeData.team.name+' ['+langTrans.mainArea.homeTeamTag+']</div></div></div></td>'
                            insertStr += '<td>'+(typeof homeData.total_score === 'undefined' ? '' : homeData.total_score)+'</td>'
                            for (let i = 0; i < 2; i++) {
                                var score = typeof scorehome[i] === 'undefined' ? '' : scorehome[i];
                                insertStr += '<td>' + score + '</td>';
                            }
                            break;
                            case 2: //if sport is basketball
                            insertStr += '<td class="nowrap home-name"><div class="d-flex align-items-center"><div><img src="' + homeData.team.logo + '" class="serieslogo" onerror="this.src=\'https://sporta.asgame.net/uploads/default.png?v=' + version + '\'" ></div><div class="text"><div>'+homeData.team.name+' ['+langTrans.mainArea.homeTeamTag+']</div></div></div></td>'
                            insertStr += '<td>'+(typeof homeData.total_score === 'undefined' ? '' : homeData.total_score)+'</td>'
                            for (let i = 0; i < 4; i++) {
                                    var score = typeof scorehome[i] === 'undefined' ? '' : scorehome[i];
                                    insertStr += '<td>' + score + '</td>';
                                }
                            break;
                            case 3: //if sport is baseball
                            insertStr += '<td class="nowrap home-name"><div class="d-flex align-items-center"><div><img src="' + homeData.team.logo + '" class="serieslogo" onerror="this.src=\'https://sporta.asgame.net/uploads/default.png?v=' + version + '\'" ></div><div class="text"><div>'+homeData.team.name+' ['+langTrans.mainArea.homeTeamTag+']</div></div></div></td>'
                            insertStr += '<td>'+(typeof homeData.total_score === 'undefined' ? '' : homeData.total_score)+'</td>'
                                if(scorehome.length < 6){
                                    $('#indexContainerRightLiving thead').html('') //clear table
                                    bbinsertStr += '<th></th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.fulltimescore+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.firstround+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.secondgame+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.thirdinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.fourthinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.fifthinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.sixthinning+'</th>'
                                    bbinsertStr += '</tr>'
                                    $('#indexContainerRightLiving thead').append(bbinsertStr)
                                    for (let i = 0; i < 6; i++) {
                                        var score = typeof scorehome[i] === 'undefined' ? '' : scorehome[i];
                                        insertStr += '<td>' + score + '</td>';
                                    }
                                }
                                if (scorehome.length >= 6 && scorehome.length <= 9){
                                    $('#indexContainerRightLiving thead').html('') //clear table
                                    bbinsertStr += '<th></th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.fulltimescore+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.fourthinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.fifthinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.sixthinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.seventhinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.eighthinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.ninthinning+'</th>'
                                    bbinsertStr += '</tr>'
                                    $('#indexContainerRightLiving thead').append(bbinsertStr)
                                    for (let i = 3; i < 9; i++) {
                                        var score = typeof scorehome[i] === 'undefined' ? '' : scorehome[i];
                                        insertStr += '<td>' + score + '</td>';
                                    }
                                }
                                if (scorehome.length > 9){
                                    $('#indexContainerRightLiving thead').html('') //clear table
                                    bbinsertStr += '<th></th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.fulltimescore+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.seventhinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.eighthinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.ninthinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.tenthinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.eleventhinning+'</th>'
                                    bbinsertStr += '<th nowrap>'+langTrans.tableLivingData.twelfthinning+'</th>'
                                    bbinsertStr += '</tr>'
                                    $('#indexContainerRightLiving thead').append(bbinsertStr)
                                    for (let i = 6; i < 12; i++) {
                                        var score = typeof scorehome[i] === 'undefined' ? '' : scorehome[i];
                                        insertStr += '<td>' + score + '</td>';
                                    }
                                }
                            break;
                        }
                    }
                }
                insertStr += '</tr>'
                insertStr += '<tr>'
                if (awayData !== undefined) {
                    awayData.scores.sort((a, b) => a.stage - b.stage);
                    awayData.scores.forEach(el => {
                        scoreaway.push(el.score);
                    });
                    
                    if (awayData.scores !== undefined) {
                        switch(sportn){
                            case 1: //if sport is soccer
                            insertStr += '<td class="nowrap away-name"><div class="d-flex align-items-center"><div><img src="' + awayData.team.logo + '" class="serieslogo" onerror="this.src=\'https://sporta.asgame.net/uploads/default.png?v=' + version + '\'" ></div><div class="text"><div>'+awayData.team.name+'</div></div></div></td>'
                            insertStr += '<td>'+(typeof awayData.total_score === 'undefined' ? '' : awayData.total_score)+'</td>'
                            for (let i = 0; i < 2; i++) {
                                var score = typeof scoreaway[i] === 'undefined' ? '' : scoreaway[i];
                                insertStr += '<td>' + score + '</td>';
                            }
                            break;

                            case 2: //if sport is basketball
                            insertStr += '<td class="nowrap away-name"><div class="d-flex align-items-center"><div><img src="' + awayData.team.logo + '" class="serieslogo" onerror="this.src=\'https://sporta.asgame.net/uploads/default.png?v=' + version + '\'" ></div><div class="text"><div>'+awayData.team.name+'</div></div></div></td>'
                            insertStr += '<td>'+(typeof awayData.total_score === 'undefined' ? '' : awayData.total_score)+'</td>'
                            
                            for (let i = 0; i < 4; i++) {
                                    var score = typeof scoreaway[i] === 'undefined' ? '' : scoreaway[i];
                                    insertStr += '<td>' + score + '</td>';
                                }
                            break;

                            case 3: //if sport is baseball
                            insertStr += '<td class="nowrap away-name"><div class="d-flex align-items-center"><div><img src="' + awayData.team.logo + '" class="serieslogo" onerror="this.src=\'https://sporta.asgame.net/uploads/default.png?v=' + version + '\'" ></div><div class="text"><div>'+awayData.team.name+'</div></div></div></td>'
                            insertStr += '<td>'+(typeof awayData.total_score === 'undefined' ? '' : awayData.total_score)+'</td>'
                                if(scoreaway.length < 6){
                                    for (let i = 0; i < 6; i++) {
                                        var score = typeof scoreaway[i] === 'undefined' ? '' : scoreaway[i];
                                        insertStr += '<td>' + score + '</td>';
                                    }
                                }
                                if (scoreaway.length >= 6 && scorehome.length <= 9){
                                    
                                    for (let i = 3; i < 9; i++) {
                                        var score = typeof scoreaway[i] === 'undefined' ? '' : scoreaway[i];
                                        insertStr += '<td>' + score + '</td>';
                                    }
                                }
                                if (scoreaway.length > 9){
                                    
                                    $('#indexContainerRightLiving thead').append(bbinsertStr)
                                    for (let i = 6; i < 12; i++) {
                                        var score = typeof scoreaway[i] === 'undefined' ? '' : scoreaway[i];
                                        insertStr += '<td>' + score + '</td>';
                                    }
                                }
                            break;
                        }
                    }
                }
                insertStr += '</tr>'
        
		$('#indexContainerRightLiving tbody').append(insertStr)
        marqueeLongText()
    }


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

    //insert Format Date
    function formatDateTime(dateStr){
		var startTime = dateStr; // Assuming e.start_time is a date string
		var date = new Date(startTime);

		// Extract date components
		var month = (date.getMonth() + 1).toString().padStart(2, '0'); // Adding 1 to the month because it's zero-based
		var day = date.getDate().toString().padStart(2, '0');
		var hours = date.getHours().toString().padStart(2, '0');
		var minutes = date.getMinutes().toString().padStart(2, '0');

		// Formatted date string in "m-d H:i" format
		var formattedDate = month + '-' + day + ' ' + hours + ':' + minutes;
		return formattedDate
	}

    // 右邊切換賽事
    function changeTab(key1, key2, key3, resetTeam = 0) {
        // 取得目標資料 get target match data
        var result = match_list[key1][key2]['list'][key3]
        var match_id = result.match_id
        // 紀錄目前match id
        indexRightMatchId = match_id



        if ( resetTeam === 0 ) {
            $('#indexContainerRight').attr('match', match_id)
            $('#indexContainerRightInfo').attr('match', match_id)
            $('div[key="rightInfoSeries"]').html(result.series.name)
            
            // 取得主客隊資料 get team data
            var homeData = result.teams.find(item => item.index === 1)
            var awayData = result.teams.find(item => item.index === 2)
            // 若有主隊資料 set home data
            if (homeData !== undefined) {
                $('img[key="rightInfoHome"]').attr('src', homeData.team.logo)
                $('p[key="rightInfoHomeName"]').html(homeData.team.name)
            }
            // 若有客隊資料 set away data
            if (awayData !== undefined) {
                $('img[key="rightInfoAway"]').attr('src', awayData.team.logo)
                $('p[key="rightInfoAwayName"]').html(awayData.team.name)
            }

            // 判斷是否開賽中 detect is its living
            let matchStatus = result.status
            if ( matchStatus === 2 ) { 
                // 開賽中 living
                let str = '<div class="row m-0" style="line-height: 7rem;">'
                str += '<div class="col-4 fs-3 p-0 teamScore">' + homeData.total_score + '</div>'
                if( result.stageStr !== undefined ) {
                    str += '<div class="col-4 p-0 gameStage">' + result.stageStr + '</div>'
                } else {
                    str += '<div class="col-4 p-0 gameStage">' + langTrans.mainArea.gaming + '</div>'
                }
                str += '<div class="col-4 fs-3 p-0 teamScore">' + awayData.total_score + '</div>'
                $('.seriesName').html('<img src="' + result.series.logo + '" class="serieslogo" onerror="this.src=\'https://sporta.asgame.net/uploads/default.png?v=' + version + '\'" >'+ result.series.name+'')
                $('.seriesDate').html(formatDateTime(result.start_time))
                insertRecordTable(result)
                $('div[key="rightInfoStatus"]').html(str)
                $('#indexContainerRightInfo').css("display","none")
                $('#indexContainerRightLiving').css("display","block")
                marqueeLongText()
            } else {
                // 等待開賽 early
                let str = '<p class="fs-5 mb-0 mt-4">' + langTrans.mainArea.notgaming + '</p>'
                str += '<p class="mb-0">' + result.start_time.split(' ')[0] + '</p>'
                str += '<p class="fs-5 mb-0">' + result.start_time.split(' ')[1] + '</p>'
                $('#indexContainerRightInfo').css("display","block")
                $('#indexContainerRightLiving').css("display","none")
                $('div[key="rightInfoStatus"]').html(str)
            }
        }

        // 清空目前的
        $('div[data-tab="all"] .segmentContainer').html('')
        $('div[data-tab="win"] .segmentContainer').html('')
        $('div[data-tab="hcap"] .segmentContainer').html('')
        $('div[data-tab="size"] .segmentContainer').html('')

        // 以玩法名稱分類
        Object.entries(result.rate).forEach(([key, val]) => {
            var str = ''
            // 獨贏系列
            let winPriorityArr = langTrans.priorityArr[1]
            if (winPriorityArr.indexOf(key) !== -1) {
                str += '<p class="tabCardLabel">' + val[0].name + '</p>'
                str += '<div class="tabCardContent bg-white row text-center">'
                if (sport === 1) {
                    str += '<div class="col-4">' + langTrans.mainArea.homeTeam + '</div>'
                    str += '<div class="col-4">' + langTrans.mainArea.tie + '</div>'
                    str += '<div class="col-4">' + langTrans.mainArea.awayTeam + '</div>'
                } else {
                    str += '<div class="col-6">' + langTrans.mainArea.homeTeam + '</div>'
                    str += '<div class="col-6">' + langTrans.mainArea.awayTeam + '</div>'
                }
                val.forEach(ele => {
                    let j = sport === 1 ? 3 : 2
                    var rStr = ''
                    var statusCount = 0
                    for (let i = 0; i < j; i++) {
                        const statusLock = 1
                        const subData = ele.rate[Object.keys(ele.rate)[i]]
                        if( ele.status === 1 ) {
                            if(subData.status === 1) statusCount ++
                            if (sport === 1) {
                                rStr += '<div onclick="openCal($(this), 0)" class="col-4 betItemDiv" game_priority=' + ele.game_priority + ' match_id=' + match_id + ' rate_id=' + ele.rate_id + ' rate_name=' + ele.name + ' rate=' + subData.id + ' bet_name=' + subData.name + ' key=' + key1 + ' key2=' + key2 + ' key3=' + key3 + ' rate_item_status=' + subData.status + ' rate_status=' + ele.status + ' risk=' + subData.risk + ' >'
                            } else {
                                rStr += '<div onclick="openCal($(this), 0)" class="col-6 betItemDiv" game_priority=' + ele.game_priority + ' match_id=' + match_id + ' rate_id=' + ele.rate_id + ' rate_name=' + ele.name + ' rate=' + subData.id + ' bet_name=' + subData.name + ' key=' + key1 + ' key2=' + key2 + ' key3=' + key3 + ' rate_item_status=' + subData.status + ' rate_status=' + ele.status + ' risk=' + subData.risk + ' >'
                            }
                            rStr += '<span class="odd">' + subData.rate + '</span>'
                            rStr += '<i class="fa-solid fa-lock"></i>'
                            rStr += '</div>'
                        }
                    }
                    if (statusCount > 0) str += rStr
                });
                $('div[data-tab="win"] .segmentContainer').append(str)
            }

            // 大小系列
            let sizePriorityArr = langTrans.priorityArr[2]
            if (sizePriorityArr.indexOf(key) !== -1) {
                str += '<p class="tabCardLabel">' + val[0].name + '</p>'
                str += '<div class="tabCardContent bg-white row text-center">'
                val.forEach(element => {
                    var rStr = ''
                    var statusCount = 0
                    Object.entries(element.rate).forEach(([key, ele], i) => {
                        const statusLock = 1
                        const subData = ele
                        if( element.status === 1 ) {
                            if(subData.status === 1) statusCount ++
                            if (i === 0) rStr += '<div class="col-4" id=' + subData.id + '_label>' + subData.value + '</div>'
                            rStr += '<div onclick="openCal($(this), 0)" class="col-4 betItemDiv" game_priority=' + element.game_priority + ' match_id=' + match_id + ' rate_id=' + element.rate_id + ' rate_name=' + element.name + ' rate=' + subData.id + ' bet_name="' + subData.name + '" key=' + key1 + ' key2=' + key2 + ' key3=' + key3 + ' rate_item_status=' + subData.status + ' rate_status=' + element.status + ' risk=' + subData.risk + ' >'
                            rStr += '<span>' + subData.name + '</span>&ensp;'
                            rStr += '<span class="odd">' + subData.rate + '</span>'
                            rStr += '<i class="fa-solid fa-lock"></i>'
                            rStr += '</div>'
                        }
                    })
                    if (statusCount > 0) str += rStr
                });
                $('div[data-tab="size"] .segmentContainer').append(str)
            }

            // 讓球系列
            let hcapPriorityArr = langTrans.priorityArr[3]
            if (hcapPriorityArr.indexOf(key) !== -1) {
                str += '<p class="tabCardLabel">' + val[0].name + '</p>'
                str += '<div class="tabCardContent bg-white row text-center">'

                val.forEach(element => {
                    var rStr = ''
                    var statusCount = 0
                    Object.entries(element.rate).forEach(([i, ele]) => {
                        if( element.status === 1 ) {
                            if(ele.status === 1) statusCount ++
                            if (Object.keys(element.rate).length === 2) {
                                rStr += '<div class="col-6" id=' + ele.id + '_label>' + ele.name + '</div>'
                            } else {
                                rStr += '<div class="col-4" id=' + ele.id + '_label>' + ele.name + '</div>'
                            }
                        }
                    })
                    Object.entries(element.rate).forEach(([key, ele], i) => {
                        const statusLock = 1
                        const subData = ele
                        if( element.status === 1 ) {
                            if (Object.keys(element.rate).length === 2) {
                                rStr += '<div onclick="openCal($(this), 0)" class="col-6 betItemDiv" game_priority=' + element.game_priority + ' match_id=' + match_id + ' rate_id=' + element.rate_id + ' rate_name=' + element.name + ' rate=' + subData.id + ' bet_name="' + subData.name + '" key=' + key1 + ' key2=' + key2 + ' key3=' + key3 + ' rate_item_status=' + subData.status + ' rate_status=' + element.status + ' risk=' + subData.risk + ' >'
                            } else {
                                rStr += '<div onclick="openCal($(this), 0)" class="col-4 betItemDiv" game_priority=' + element.game_priority + ' match_id=' + match_id + ' rate_id=' + element.rate_id + ' rate_name=' + element.name + ' rate=' + subData.id + ' bet_name="' + subData.name + '" key=' + key1 + ' key2=' + key2 + ' key3=' + key3 + ' rate_item_status=' + subData.status + ' rate_status=' + element.status + ' risk=' + subData.risk + ' >'
                            }

                            rStr += '<span class="odd">' + subData.rate + '</span>'
                            rStr += '<i class="fa-solid fa-lock"></i>'
                            rStr += '</div>'
                        }
                    })
                    if (statusCount > 0) str += rStr
                });
                $('div[data-tab="hcap"] .segmentContainer').append(str)
            }
        });
        // 所有
        $('.segment.tab .tabCardLabel, .segment.tab .tabCardContent').each(function() {
            let item = $(this).clone()
            $('div[data-tab="all"] .segmentContainer').append(item)
        })

        // 右邊都被關閉的移除title
        clearUnusedRight()
        // 文字太長處理
        fixTextOverflow()
        // 判斷狀態顯示鎖頭或是賠率
        rateStatusJudge( match_id, 0, 1, 0 )
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
            let homeTeamLogo = homeData.team.logo ?? 'https://sporta.asgame.net/uploads/default.png?v=' + version
            let awayTeamLogo = awayData.team.logo ?? 'https://sporta.asgame.net/uploads/default.png?v=' + version

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
            $('#leftSlideOrder img[key="homeLogo"]').attr('src', homeTeamLogo)
            $('#leftSlideOrder img[key="awayLogo"]').attr('src', awayTeamLogo)
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


            // 比賽如果在打開投注區塊的情況下開始  監聽比賽狀態
            // betTimer = setInterval(() => {
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

            // }, 1000);
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
        clearInterval(betTimer)
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
        console.log('refreshBalence')
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