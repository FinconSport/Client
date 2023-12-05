@extends('layout.app')

@section('content')
    <div id ="rulePage" class="h-100 w-100 rule-con">
        <div class="row rule-row">
            <div class="col-xl-2 col-lg-2 col-md-2 col-2 nopad rule-col-left">
                <nav>
                    <div class="nav nav-tabs flex-column" id="nav-tab" role="tablist">
                    @foreach(range(1, 5) as $i)
                        <button
                            key="{{ $i }}"
                            class="nav-link {{ $i === 1 ? 'active' : '' }}"
                            id="nav-{{ $i }}"
                            data-bs-toggle="tab"
                            data-bs-target="#nav{{ $i }}"
                            type="button"
                            role="tab" 
                            aria-controls="#nav{{ $i }}" 
                            aria-selected="{{ $i === 1 ? 'true' : 'false' }}"
                        >
                            {{ trans('rule.ruleTitles.sportName.' . $i) }}
                        </button>
                    @endforeach
                    </div>
                </nav>
            </div>
            <div class="col-xl-10 col-lg-10 col-md-10 col-10 rule-col-right">
            <div class="rule-tab">
                <div class="rule-tab-con">
                        <div class="tab-content" id="nav-tabContent">
                            <!-- baseball -->
                            <div class="tab-pane active" id="nav1" role="tabpanel" aria-labelledby="nav-1">
                                <h3>{{ trans('rule.ruleTitles.sportName.1') }}</h3>
                                <h3>{{ trans('rule.ruleTitles.general_rule') }}</h3>
                                <ul class="number-bullets">
                                    @foreach(trans('rule.generalRulesBaseball') as $key => $grRule)
                                        @if (is_array($grRule))
                                            <ul>
                                                @foreach($grRule as $subKey => $subRule)
                                                    @if (is_array($subRule))
                                                        <ul>
                                                            @foreach($subRule as $subSubKey => $subSubRule)
                                                                <li>{{ trans('rule.generalRulesBaseball.' . $key . '.' . $subKey . '.' . $subSubKey) }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <li>{{ trans('rule.generalRulesBaseball.' . $key . '.' . $subKey) }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @else
                                            <li>{{ trans('rule.generalRulesBaseball.' . $key) }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.betting_type') }}</h3>
                                @foreach([
                                    'moneyline' => [1, 2],
                                    'get_the_ball' => [3, 2],
                                    'inplay_handicap' => [3, 2],
                                    'total_score' => [4, 2, 5, 6, 7, 8],
                                    'rolling_total_score' => [4, 9, 2],
                                    'total_score_sd' => [10, 2],
                                    'solo_win' => [11, 12],
                                    'team_scores' => [13, 14],
                                    'overtime' => [14, 15, 16],
                                ] as $title => $items)
                                    <h3>{{ trans('rule.ruleTitles.' . $title) }}</h3>
                                    <ul class="number-bullets">
                                        @foreach($items as $i)
                                            <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_' . $i) }}</li>
                                        @endforeach
                                    </ul>
                                    <hr class="solid">
                                @endforeach
                            </div>
                            <!-- basketball -->
                            <div class="tab-pane" id="nav2" role="tabpanel" aria-labelledby="nav-2">
                                <h3>{{ trans('rule.ruleTitles.sportName.2') }}</h3>
                                <h3>{{ trans('rule.ruleTitles.general_rule') }}</h3>
                                <ul class="number-bullets">
                                    @foreach(trans('rule.generalRulesBasketball') as $key => $grRule)
                                        @if (is_array($grRule))
                                            <ul class="alpha-bullets">
                                                @foreach($grRule as $subKey => $subRule)
                                                    <li>{{ trans('rule.generalRulesBasketball.' . $key . '.' . $subKey) }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <li>{{ trans('rule.generalRulesBasketball.' . $key) }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.betting_type') }}</h3>
                                @foreach([
                                    'moneyline' => [1, 2],
                                    'get_the_ball' => [3, 4, 5, 2],
                                    'inplay_handicap' => [6, 2],
                                    'total_score' => [7, 2, 8, 9, 4, 10, 11],
                                    'rolling_total_score' => [7, 12, 8],
                                    'team_scores' => [13, 14, 15],
                                    'total_points' => [16, 2],
                                ] as $title => $items)
                                    <h3>{{ trans('rule.ruleTitles.' . $title) }}</h3>
                                    <ul class="number-bullets">
                                        @foreach($items as $i)
                                            <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_' . $i) }}</li>
                                        @endforeach
                                        @if(in_array($title, ['total_score']))
                                            <ul class="alpha-bullets">
                                                @foreach(trans('rule.ruleContentsBasketball.rc_basketball_11_0') as $key => $grRule)
                                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_11_0.' . $key) }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </ul>
                                    <hr class="solid">
                                @endforeach
                            </div>
                            <!-- Soccor -->
                            <div class="tab-pane" id="nav3" role="tabpanel" aria-labelledby="nav-3">
                                <h3>{{ trans('rule.ruleTitles.sportName.3') }}</h3>
                                <h3>{{ trans('rule.ruleTitles.general_rule') }}</h3>
                                <ul class="number-bullets">
                                    @foreach(trans('rule.generalRulesSoccor') as $key => $grRule)
                                        <li>{{ trans('rule.generalRulesSoccor.' . $key) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                @foreach([
                                    'handicap' => [1, 2, 3, 4, 5 => [1, 2, 3], 6, 7, 8],
                                    'fulltime_handicap_result' => [9, 10, 11, 12 => [1, 2, 3, 4, 5], 13],
                                    'handicap_1stHalf' => [14, 15, 16],
                                    'inplay_handicap' => [17, 18],
                                    'ot_handicap' => [19, 20],
                                    'ot_let_1stHalf' => [21, 22, 23],
                                    'betting_sizes' => [24, 25, 26, 27 => [1, 2, 3]],
                                    'goal_largeSmall' => [29, 30],
                                    'goal_overUnder_1stHalf' => [31, 32, 33],
                                    'rolling_ball_overUnder' => [34],
                                    'ot_goal_overUnder' => [35, 36, 37],
                                    'ot_goals_overUnder_1stHalf' => [38, 39, 40],
                                    'handicap_1stHalf' => [14, 15, 16],
                                    'team_goals_overUnder' => [41, 42, 43, 44],
                                    'moneyline' => [45, 46],
                                    'win_alone' => [47],
                                    'win_alone_1stHalf' => [48],
                                    'betting_sizes' => [24, 25, 26, 27 => [1, 2, 3]],
                                    'ot_win_alone' => [55, 56],
                                    'ot_winAlone_1stHalf' => [57, 58, 59],
                                    'crts' => [60, 61, 62],
                                    'crts_1stHalf' => [63, 64, 65, 66],
                                ] as $title => $items)
                                    @if(in_array($title, ['handicap', 'fulltime_handicap_result', 'handicap_1stHalf', 'inplay_handicap', 'ot_handicap', 'ot_let_1stHalf', 'betting_sizes', 'ot_goal_overUnder', 'ot_goals_overUnder_1stHalf', 'handicap_1stHalf', 'team_goals_overUnder', 'moneyline', 'win_alone', 'win_alone_1stHalf', 'score_goal', 'ot_win_alone', 'ot_winAlone_1stHalf']))
                                        <h3>{{ trans('rule.ruleTitles.' . $title) }}</h3>
                                    @endif
                                    @if(in_array($title, ['goal_largeSmall', 'goal_overUnder_1stHalf', 'rolling_ball_overUnder', 'crts', 'crts_1stHalf']))
                                        <h4>{{ trans('rule.ruleTitles.' . $title) }}</h4>
                                    @endif
                                    @if(in_array($title, ['handicap', 'betting_sizes', 'moneyline', 'crts']))
                                        <h4>({{ trans('rule.ruleTitles.general_rule') }})</h4>
                                    @endif
                                    <ul class="number-bullets">
                                        @foreach($items as $i => $v)
                                            @if(!is_array($v))
                                                <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $v) }}</li>
                                            @else
                                                <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                                <ul class="{{ in_array($title, ['handicap']) ? 'alpha-bullets' : '' }} {{ in_array($title, ['fulltime_handicap_result']) ? 'number-bullets' : '' }}">
                                                    @foreach($v as $x)
                                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i . '_' . $x) }}</li>
                                                    @endforeach
                                                </ul>
                                                <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                    @if(in_array($title, ['handicap', 'fulltime_handicap_result', 'handicap_1stHalf', 'inplay_handicap', 'ot_handicap', 'ot_let_1stHalf', 'betting_sizes', 'ot_goal_overUnder', 'ot_goals_overUnder_1stHalf', 'handicap_1stHalf', 'team_goals_overUnder', 'moneyline', 'win_alone', 'win_alone_1stHalf', 'score_goal', 'ot_win_alone', 'ot_winAlone_1stHalf']))
                                        <hr class="solid">
                                    @endif
                                @endforeach
                            </div>
                            <!-- ice hockey -->
                            <div class="tab-pane" id="nav4" role="tabpanel" aria-labelledby="nav-4">
                                <h3>{{ trans('rule.ruleTitles.sportName.4') }}</h3>
                                <h3>{{ trans('rule.ruleTitles.general_rule') }}</h3>
                                <ul class="number-bullets">
                                    @foreach(trans('rule.generalRuleIceHockey') as $key => $grRule)
                                        <li>{{ trans('rule.generalRuleIceHockey.' . $key) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.betting_type') }}</h3>
                                @foreach([
                                    'moneyline' => [1],
                                    'get_the_ball' => [2],
                                    'inplay_handicap' => [2],
                                    'overUnder' => [3, 4],
                                    'inPlay_overUnder' => [3, 4],
                                ] as $title => $items)
                                    <h3>{{ trans('rule.ruleTitles.' . $title) }} @if(in_array($title, ['overUnder', 'inPlay_overUnder'])) ({{ trans('rule.ruleTitles.ball') }}) @endif</h3>
                                    <ul class="number-bullets">
                                        @foreach($items as $i)
                                            <li>{{ trans('rule.rulesContentsIceHockey.rc_IceHockey_' . $i) }}</li>
                                        @endforeach
                                    </ul>
                                    <hr class="solid">
                                @endforeach
                            </div>
                            <!-- american football -->
                            <div class="tab-pane" id="nav5" role="tabpanel" aria-labelledby="nav-5">
                                <h3>{{ trans('rule.ruleTitles.sportName.5') }}</h3>
                                <h3>{{ trans('rule.ruleTitles.general_rule') }}</h3>
                                <ul class="number-bullets">
                                    @foreach(trans('rule.rulesGeneralAmericanFootball') as $grRule)
                                        <li>{{ $grRule }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.betting_type') }}</h3>
                                @foreach([
                                    'moneyline' => [1, 2],
                                    'get_the_ball' => [3, 4, 5, 2],
                                    'inplay_handicap' => [3, 6, 2],
                                    'overUnder' => [7, 2],
                                    'inPlay_overUnder' => [8, 9, 2],
                                ] as $title => $items)
                                    <h3>{{ trans('rule.ruleTitles.' . $title) }} @if(in_array($title, ['overUnder', 'inPlay_overUnder'])) ({{ trans('rule.ruleTitles.totalPoints') }}) @endif</h3>
                                    <ul class="number-bullets">
                                        @foreach($items as $i)
                                            <li>{{ trans('rule.rulesContentsAmericanFootball.rc_AmericanFootball_' . $i) }}</li>
                                        @endforeach
                                    </ul>
                                    <hr class="solid">
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<link href="{{ asset('css/rule.css?v=' . $system_config['version']) }}" rel="stylesheet">
<style>	
/* 寫入頁面限定CSS */
</style>
@endSection
@push('main_js')
<script src="{{ asset('js/bootstrap.min.js?v=' . $system_config['version']) }}"></script>
<script>
    var isReadyRuleInt = null
    $(document).ready(function() {
        // check if api are all loaded every 500 ms 
        isReadyRuleInt = setInterval(() => {
            if( isReadyCommon ) {
                $('#dimmer').dimmer('hide'); // hide loading
                $('#wrap').css('opacity', 1); // show the main content
                clearInterval(isReadyRuleInt); // stop checking
            }
        }, 500);
    });

    // 語系
    var langTrans = @json(trans('rule'));

    $("button.nav-link").click(function() {
        $(".rule-tab-con").animate({ scrollTop: 0 }, "smooth");
        var firstButton = document.querySelector('.nav-link');
        if (firstButton.classList.contains('active')) {
            $('.rule-col-right').css('border-top-left-radius', '0px');
        } else {
            $('.rule-col-right').css('border-top-left-radius', '5px');
        } 
    });
    
</script>
@endpush