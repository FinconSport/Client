@extends('layout.app')

@section('content')
    <pre id="fetchMoreSuccessMsg">
        @if(session('success'))
            <!-- Success Message -->
            {{ session('success') }}
        @endif
    </pre>
    <pre id="fetchMoreErrorMsg">
        @if(session('error'))
            <!-- error Message -->
            {{ session('error') }}
        @endif
    </pre>
    @if(!session('error'))
        @if(!empty($match_list['list']))
            <pre id="fetchMoreList">
                {{ json_encode($match_list['list']) }}
            </pre>
            @foreach ($match_list['list'] as $item)
                <div class="indexEachCard" key='{{ $item["match_id"] }}'>
                    <div class="indexBetCardLabel">
                        @if(isset($item['series']['logo']))
                        <img src="{{ $item['series']['logo'] }}" class="serieslogo" onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'">
                        @endif
                        @if(isset($item['series']['name']))
                        <div class="indexBetCardSpan textOverFlow">{{ $item['series']['name'] }}</div>
                        @endif
                    </div>
                    <div class="indexBetCard">
                        <div class="indexBetCardInfo" onclick="changeTab({{ $item['match_id'] }})">
                            <div class="timeSpan">{{ trans('index.mainArea.time') }}<span class="timer">{{ $item['start_time'] }}</span></div>
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
                                @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['rate'])->sortKeys() as $subrate)
                                @if( $loop->first)
                                <div onclick="selectMOrderBet($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}">
                                    <span>{{ trans('index.mainArea.homeWin') }}&ensp;</span>
                                    @if (( !isset($statusLock) || $subrate['status'] === 1) && isset($subrate['rate']))
                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                    @else
                                    <i class="fa-solid fa-lock"></i>
                                    @endif
                                </div>
                                @break
                                @endif
                                @endforeach
                                @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['rate'])->sortKeys() as $subrate)
                                @if( $loop->last)
                                <div onclick="selectMOrderBet($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}">
                                    <span>{{ trans('index.mainArea.awayWin') }}&ensp;</span>
                                    @if (( !isset($statusLock) || $subrate['status'] === 1) && isset($subrate['rate']))
                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                    @else
                                    <i class="fa-solid fa-lock"></i>
                                    @endif
                                </div>
                                @break
                                @endif
                                @endforeach
                                @if($search['sport'] == 1)
                                @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['rate'])->sortKeys() as $subrate)
                                @if( $loop->index === 1)
                                <div onclick="selectMOrderBet($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.0')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}">
                                    <span>{{ trans('index.mainArea.tie') }}&ensp;</span>
                                    @if (( !isset($statusLock) || $subrate['status'] === 1) && isset($subrate['rate']))
                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                    @else
                                    <i class="fa-solid fa-lock"></i>
                                    @endif
                                </div>
                                @break
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
                                @if (isset($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.1')][0]))
                                @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.1')][0]['rate'])->sortKeys() as $subrate)
                                <div onclick="selectMOrderBet($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.1')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.1')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}">
                                    <span>{{ $subrate['value'] }}</span>&ensp;
                                    @if ((!isset($statusLock) || $subrate['status'] === 1) && isset($subrate['rate']))
                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                    @else
                                    <i class="fa-solid fa-lock"></i>
                                    @endif
                                </div>
                                @endforeach
                                @if($search['sport'] == 1)
                                <div></div>
                                @endif
                                @endif
                            </div>
                            <!-- 全場讓球 -->
                            <!-- 全場大小 -->
                            <div class="col-2 p-0">
                                <div class="betLabel">
                                    {{ trans('index.sportBetData.' . intval($search['sport']) . '.gameTitle.2') }}
                                </div>
                                @if (isset($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.2')][0]))
                                @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.2')][0]['rate'])->sortKeys() as $subrate)
                                <div onclick="selectMOrderBet($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.2')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.2')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}">
                                    <span>{{ $subrate['value'] }}</span>&ensp;
                                    @if ((!isset($statusLock) || $subrate['status'] === 1) && isset($subrate['rate']))
                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                    @else
                                    <i class="fa-solid fa-lock"></i>
                                    @endif
                                </div>
                                @endforeach
                                @if($search['sport'] == 1)
                                <div></div>
                                @endif
                                @endif
                            </div>
                            <!-- 全場大小 -->
                            <!-- 半場獨贏 -->
                            <div class="col-2 p-0">
                                <div class="betLabel">
                                    {{ trans('index.sportBetData.' . intval($search['sport']) . '.gameTitle.3') }}
                                </div>
                                @if (isset($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]))
                                @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['rate'])->sortKeys() as $subrate)
                                @if( $loop->first)
                                <div onclick="selectMOrderBet($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}">
                                    <span>{{ trans('index.mainArea.homeWin') }}&ensp;</span>
                                    @if (( !isset($statusLock) || $subrate['status'] === 1) && isset($subrate['rate']))
                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                    @else
                                    <i class="fa-solid fa-lock"></i>
                                    @endif
                                </div>
                                @break
                                @endif
                                @endforeach
                                @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['rate'])->sortKeys() as $subrate)
                                @if( $loop->last)
                                <div onclick="selectMOrderBet($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}">
                                    <span>{{ trans('index.mainArea.awayWin') }}&ensp;</span>
                                    @if (( !isset($statusLock) || $subrate['status'] === 1) && isset($subrate['rate']))
                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                    @else
                                    <i class="fa-solid fa-lock"></i>
                                    @endif
                                </div>
                                @break
                                @endif
                                @endforeach
                                @if($search['sport'] == 1)
                                @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['rate'])->sortKeys() as $subrate)
                                @if( $loop->index === 1)
                                <div onclick="selectMOrderBet($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.3')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}">
                                    <span>{{ trans('index.mainArea.tie') }}&ensp;</span>
                                    @if (( !isset($statusLock) || $subrate['status'] === 1) && isset($subrate['rate']))
                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                    @else
                                    <i class="fa-solid fa-lock"></i>
                                    @endif
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
                                @if (isset($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.4')][0]))
                                @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.4')][0]['rate'])->sortKeys() as $subrate)
                                <div onclick="selectMOrderBet($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.4')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.4')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}">
                                    <span>{{ $subrate['value'] }}</span>&ensp;
                                    @if ((!isset($statusLock) || $subrate['status'] === 1) && isset($subrate['rate']))
                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                    @else
                                    <i class="fa-solid fa-lock"></i>
                                    @endif
                                </div>
                                @endforeach
                                @if($search['sport'] == 1)
                                <div></div>
                                @endif
                                @endif
                            </div>
                            <!-- 半場讓球 -->
                            <!-- 半場大小 -->
                            <div class="col-2 p-0">
                                <div class="betLabel">
                                    {{ trans('index.sportBetData.' . intval($search['sport']) . '.gameTitle.5') }}
                                </div>
                                @if (isset($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.5')][0]))
                                @foreach (collect($item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.5')][0]['rate'])->sortKeys() as $subrate)
                                <div onclick="selectMOrderBet($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.5')][0]['rate_id'] }}" rate_name="{{ $item['rate'][trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr.5')][0]['name'] }}" rate="{{ $subrate['id'] }}" bet_name="{{ $subrate['name'] }}">
                                    <span>{{ $subrate['value'] }}</span>&ensp;
                                    @if ((!isset($statusLock) || $subrate['status'] === 1) && isset($subrate['rate']))
                                    <span class="odd">{{ $subrate['rate'] }}</span>
                                    @else
                                    <i class="fa-solid fa-lock"></i>
                                    @endif
                                </div>
                                @endforeach
                                @if($search['sport'] == 1)
                                <div></div>
                                @endif
                                @endif
                            </div>
                            <!-- 半場大小 -->
                        </div>
                        <!-- 其他玩法 -->
                        @if (isset($item['rate']))
                        <!-- 判斷有沒有其他玩法 -->
                        <div class="otherbet">
                            @foreach ($item['rate'] as $key => $rate)
                            @if (!in_array($key, trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr')))
                            <div class='toggleOtherBtn' id="{{ $item['match_id'] }}_{{ $key }}" onclick="toggleOther('{{ $item['match_id'] }}_{{ $key }}')">
                                <span>{{ $rate[0]['name'] }}</span>
                                <span id="dir_{{ $item['match_id'] }}_{{ $key }}">▸</span>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @foreach ($item['rate'] as $key => $rate)
                        @if (!in_array($key, trans('index.sportBetData.' . intval($search['sport']) . '.priorityArr')))
                        <div id="otherBet_{{ $item['match_id'] }}_{{ $key }}" class="otherBetArea" style="display: none;">
                            <div class="indexBetCardTable row text-center" sport="{{ $search['sport'] }}">
                                <div class="indexBetCardInfo">
                                    <div class="fw-600 ml-1 w-100">{{ $rate[0]['name'] }}</div>
                                </div>
                                <div style="width: 72%;" class="m-0 row p-0">
                                    @if($key === 7 || $key === 8 )
                                    @foreach ($rate as $subrate)
                                    @for ($i = 0; $i < 3; $i ++ ) <div class="col-2 p-0">
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
                                        <div onclick="selectMOrderBet($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $subrate['rate_id'] }}" rate_name="{{ $subrate['name'] }}" rate="{{ $sssubrate['id'] }}" bet_name="{{ $sssubrate['name'] }}">
                                            <span>{{ $sssubrate['value'] }}</span>&ensp;
                                            @if ((!isset($statusLock) || $sssubrate['status'] === 1) && isset($sssubrate['rate']))
                                            <span class="odd">{{ $sssubrate['rate'] }}</span>
                                            @else
                                            <i class="fa-solid fa-lock"></i>
                                            @endif
                                        </div>
                                        @endforeach
                                </div>
                                @endfor
                                @endforeach
                                @else
                                @foreach ($rate as $subrate)
                                <div class="col-2 p-0">
                                    <div class="col-12 p-0">
                                        {{ $subrate['name'] }}
                                    </div>
                                    @foreach (collect($subrate['rate'])->sortKeys() as $ssubrate)
                                    <div class="col-12 p-0">
                                        <div onclick="selectMOrderBet($(this), 1)" match_id='{{ $item["match_id"] }}' rate_id="{{ $subrate['rate_id'] }}" rate_name="{{ $subrate['name'] }}" rate="{{ $ssubrate['id'] }}" bet_name="{{ $ssubrate['name'] }}" class="h-100">
                                            @if (in_array($key, trans('index.priorityArr.1')))
                                            <div class="row m-0 w-100 h-100">
                                                <div class="col-8 p-0 textOverFlow">
                                                    {{ $ssubrate['name'] }}
                                                </div>
                                                <div class="col-4 p-0">
                                                    @if ((!isset($statusLock) || $ssubrate['status'] === 1) && isset($ssubrate['rate']))
                                                    <span class="odd">{{ $ssubrate['rate'] }}</span>
                                                    @else
                                                    <i class="fa-solid fa-lock"></i>
                                                    @endif
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
                                            @if ((!isset($statusLock) || $ssubrate['status'] === 1) && isset($ssubrate['rate']))
                                            <span class="odd">{{ $ssubrate['rate'] }}</span>
                                            @else
                                            <i class="fa-solid fa-lock"></i>
                                            @endif
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
    @endif
@endsection
@push('main_js')
@endpush