@extends('layout.app')

@section('content')
    <div id ="rulePage" class="h-100 rule-con">
        <div class="row rule-row">
            <div class="col-xl-2 col-lg-2 col-md-2 col-2 nopad rule-col-left">
                <nav>
                    <div class="nav nav-tabs flex-column" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-baseball" data-bs-toggle="tab" data-bs-target="#navBaseball" type="button" role="tab" aria-controls="#navBaseball" aria-selected="true">{{ trans('rule.ruleTitles.baseball') }}</button>      
                        <button class="nav-link" id="nav-basketball" data-bs-toggle="tab" data-bs-target="#navBasketball" type="button" role="tab" aria-controls="#navBasketball" aria-selected="false">{{ trans('rule.ruleTitles.basketball') }}</button>
                        <button class="nav-link" id="nav-soccor" data-bs-toggle="tab" data-bs-target="#navSoccor" type="button" role="tab" aria-controls="#navSoccor" aria-selected="false">{{ trans('rule.ruleTitles.soccor') }}</button>          
                    </div>
                </nav>
            </div>
            <div class="col-xl-10 col-lg-10 col-md-10 col-10 rule-col-right">
            <div class="rule-tab">
                <div class="rule-tab-con">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane active" id="navBaseball" role="tabpanel" aria-labelledby="nav-baseball">
                                <h2>{{ trans('rule.ruleTitles.baseball') }}</h2>
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
                                <h2>{{ trans('rule.ruleTitles.betting_type') }}</h2>
                                <h3>{{ trans('rule.ruleTitles.solo_winners') }}</h3>
                                <ul class="number-bullets">
                                    @foreach(range(1, 2) as $i)
                                        <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.get_the_ball') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([3, 2] as $i)
                                        <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.lets_roll') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([3, 2] as $i)
                                        <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.total_score') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([4, 2, 5, 6, 7, 8] as $i)
                                        <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.rolling_total_score') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([4, 9, 2] as $i)
                                        <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.total_score_sd') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([10, 2] as $i)
                                        <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.solo_win') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([11, 12] as $i)
                                        <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.team_scores') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([13, 14] as $i)
                                        <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.overtime') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([14, 15, 16] as $i)
                                        <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="tab-pane" id="navBasketball" role="tabpanel" aria-labelledby="nav-basketball">
                                <h2>{{ trans('rule.ruleTitles.basketball') }}</h2>
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
                                <h2>{{ trans('rule.ruleTitles.betting_type') }}</h2>
                                <h3>{{ trans('rule.ruleTitles.solo_winners') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([1, 2] as $i)
                                        <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.get_the_ball') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([3, 4, 5, 2] as $i)
                                        <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.lets_roll') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([6, 2] as $i)
                                        <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.total_score') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([7, 2, 8, 9, 4, 10, 11] as $i)
                                        <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_' . $i) }}</li>
                                    @endforeach
                                    <ul class="alpha-bullets">
                                        @foreach(trans('rule.ruleContentsBasketball.rc_basketball_11_0') as $key => $grRule)
                                            <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_11_0.' . $key) }}</li>
                                        @endforeach
                                    </ul>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.rolling_total_score') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([7, 12, 8] as $i)
                                        <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.team_scores') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([13, 14, 15] as $i)
                                        <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.total_points') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([16, 2] as $i)
                                        <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_' . $i) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="tab-pane" id="navSoccor" role="tabpanel" aria-labelledby="nav-soccor">
                                <h2>{{ trans('rule.ruleTitles.soccor') }}</h2>
                                <h3>{{ trans('rule.ruleTitles.general_rule') }}</h3>
                                <ul class="number-bullets">
                                    @foreach(trans('rule.generalRulesSoccor') as $key => $grRule)
                                        <li>{{ trans('rule.generalRulesSoccor.' . $key) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h2>{{ trans('rule.ruleTitles.handicap') }}</h2>
                                <h3>{{ trans('rule.ruleTitles.general_rule') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([1, 2, 3, 4, 5] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                    <ul class="alpha-bullets">
                                        @foreach(trans('rule.ruleContentsSoccor.rc_soccor_5_0') as $key => $grRule)
                                            <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_5_0.' . $key) }}</li>
                                        @endforeach
                                    </ul>
                                    @foreach([6, 7, 8] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.fulltime_handicap_result') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([9, 10, 11, 12] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                    <ul class="number-bullets">
                                        @foreach(trans('rule.ruleContentsSoccor.rc_soccor_12_0') as $key => $grRule)
                                            <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_12_0.' . $key) }}</li>
                                        @endforeach
                                    </ul>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_13') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.handicap_1stHalf') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([14, 15, 16] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.lets_roll') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([17, 18] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_handicap') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([19, 20] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_let_1stHalf') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([21, 22, 23] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.betting_sizes') }}</h3>
                                <h4>{{ trans('rule.ruleTitles.general_rule') }}</h4>
                                <ul class="number-bullets">
                                    @foreach([24, 25, 26, 27] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                    <ul class="alpha-bullets">
                                        @foreach(trans('rule.ruleContentsSoccor.rc_soccor_27_0') as $key => $grRule)
                                            <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_27_0.' . $key) }}</li>
                                        @endforeach
                                    </ul>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28') }}</li>
                                    <ul class="alpha-bullets">
                                        @foreach(trans('rule.ruleContentsSoccor.rc_soccor_28_0') as $key => $grRule)
                                            @if (is_array($grRule))
                                                <ul class="roman-bullets">
                                                    @foreach($grRule as $subKey => $subRule)
                                                        @if (is_array($subRule))
                                                            <ul class="roman-bullets">
                                                                @foreach($subRule as $subSubKey => $subSubRule)
                                                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28_0.' . $key . '.' . $subKey . '.' . $subSubKey) }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28_0.' . $key . '.' . $subKey) }}</li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @else
                                                <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28_0.' . $key) }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.goal_largeSmall') }}</h4>
                                <ul class="number-bullets">
                                    @foreach([29, 30] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.goal_overUnder_1stHalf') }}</h4>
                                <ul class="number-bullets">
                                    @foreach([31, 32, 33] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.rolling_ball_overUnder') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_34') }}</li>   
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_goal_overUnder') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([35, 36, 37] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_goals_overUnder_1stHalf') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([38, 39, 40] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.team_goals_overUnder') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([41, 42, 43, 44] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.solo_winners') }}</h3>
                                <h4>{{ trans('rule.ruleTitles.general_rule') }}</h4>
                                <ul class="number-bullets">
                                    @foreach([45, 46] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.win_alone') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_47') }}</li>   
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.win_alone_1stHalf') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_48') }}</li>   
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.score_goal') }}</h4>
                                <ul class="number-bullets">
                                    @foreach([49, 50, 51] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                    <ul class="number-bullets">
                                        @foreach(trans('rule.ruleContentsSoccor.rc_soccor_52') as $key => $grRule)
                                            <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_52.' . $key) }}</li>
                                        @endforeach
                                        <ul class="upper-alpha-bullets">
                                            <li><h4>{{ trans('rule.ruleTitles.example_1') }}</h4></li>  
                                            <ul class="roman-bullets">
                                                @foreach(trans('rule.ruleContentsSoccor.rc_soccor_53') as $key => $grRule)
                                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_53.' . $key) }}</li>
                                                @endforeach
                                            </ul>
                                            <li><h4>{{ trans('rule.ruleTitles.example_2') }}</h4></li>  
                                            <ul class="roman-bullets">
                                                @foreach(trans('rule.ruleContentsSoccor.rc_soccor_54') as $key => $grRule)
                                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_54.' . $key) }}</li>
                                                @endforeach
                                            </ul>
                                        </ul>
                                        <li>{{ trans('rule.ruleTitles.example_2') }}</li>
                                    </ul>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_win_alone') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([55, 56] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_winAlone_1stHalf') }}</h3>
                                <ul class="number-bullets">
                                    @foreach([57, 58, 59] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.crts') }}</h3>
                                <h4>{{ trans('rule.ruleTitles.general_rule') }}</h4>
                                <ul class="number-bullets">
                                    @foreach([60, 61, 62] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.crts_1stHalf') }}</h4>
                                <ul class="number-bullets">
                                    @foreach([63, 64, 65, 66] as $i)
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_' . $i) }}</li>
                                    @endforeach
                                </ul>
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