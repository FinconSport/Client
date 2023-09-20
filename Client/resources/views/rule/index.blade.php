@extends('layout.app')

@section('content')
    <div id ="rulePage" class="container h-100 rule-con">
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
                                        @if (is_array($grRule)) {{-- Check if the value is an array --}}
                                            <ul>
                                                @foreach($grRule as $subKey => $subRule)
                                                    @if (is_array($subRule)) {{-- Check if the sub-value is an array --}}
                                                        <ul>
                                                            @foreach($subRule as $subSubKey => $subSubRule)
                                                                <li>{{ trans('rule.generalRulesBaseball.' . $key . '.' . $subKey . '.' . $subSubKey) }}: {{ $subSubRule }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <li>{{ trans('rule.generalRulesBaseball.' . $key . '.' . $subKey) }}: {{ $subRule }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @else
                                            <li>{{ trans('rule.generalRulesBaseball.' . $key) }}: {{ $grRule }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                                <hr class="solid">
                                <h2>{{ trans('rule.ruleTitles.betting_type') }}</h2>
                                <h3>{{ trans('rule.ruleTitles.solo_winners') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.get_the_ball') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_3') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.lets_roll') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_3') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.total_score') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_4') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_5') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_6') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_7') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_8') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.rolling_total_score') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_4') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_9') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.total_score_sd') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_10') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.solo_win') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_11') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_12') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.team_scores') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_13') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_14') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.overtime') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_14') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rc_baseball_15') }}</li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="navBasketball" role="tabpanel" aria-labelledby="nav-basketball">
                                <h2>{{ trans('rule.ruleTitles.basketball') }}</h2>
                                <h3>{{ trans('rule.ruleTitles.general_rule') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.generalRulesBasketball.gr_basketball_1') }}</li>
                                    <li>{{ trans('rule.generalRulesBasketball.gr_basketball_2') }}</li>
                                    <li>{{ trans('rule.generalRulesBasketball.gr_basketball_3') }}</li>
                                    <li>{{ trans('rule.generalRulesBasketball.gr_basketball_4') }}</li>
                                    <li>{{ trans('rule.generalRulesBasketball.gr_basketball_5') }}</li>
                                    <li>{{ trans('rule.generalRulesBasketball.gr_basketball_6') }}</li>
                                    <li>{{ trans('rule.generalRulesBasketball.gr_basketball_7') }}</li>
                                    <li>{{ trans('rule.generalRulesBasketball.gr_basketball_8') }}</li>
                                    <ul class="alpha-bullets">
                                        <li>{{ trans('rule.generalRulesBasketball.gr_basketball_8_1') }}</li>
                                        <li>{{ trans('rule.generalRulesBasketball.gr_basketball_8_2') }}</li>
                                        <li>{{ trans('rule.generalRulesBasketball.gr_basketball_8_3') }}</li>
                                    </ul>
                                </ul>
                                <hr class="solid">
                                <h2>{{ trans('rule.ruleTitles.betting_type') }}</h2>
                                <h3>{{ trans('rule.ruleTitles.solo_winners') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.get_the_ball') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_3') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_4') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_5') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.lets_roll') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_6') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.total_score') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_7') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_8') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_9') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_4') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_10') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_11') }}</li>
                                    <ul class="alpha-bullets">
                                        <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_11_1') }}</li>
                                        <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_11_2') }}</li>
                                    </ul>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.rolling_total_score') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_7') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_12') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_8') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.team_scores') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_13') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_14') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_15') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.total_points') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_16') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rc_basketball_2') }}</li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="navSoccor" role="tabpanel" aria-labelledby="nav-soccor">
                                <h2>{{ trans('rule.ruleTitles.soccor') }}</h2>
                                <h3>{{ trans('rule.ruleTitles.general_rule') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_1') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_2') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_3') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_4') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_5') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_6') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_7') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_8') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_9') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_10') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_11') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_12') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_13') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_14') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_15') }}</li>
                                    <li>{{ trans('rule.generalRulesSoccor.gr_soccor_16') }}</li>
                                </ul>
                                <hr class="solid">
                                <h2>{{ trans('rule.ruleTitles.handicap') }}</h2>
                                <h3>{{ trans('rule.ruleTitles.general_rule') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_3') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_4') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_5') }}</li>
                                    <ul class="alpha-bullets">
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_5_1') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_5_2') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_5_3') }}</li>
                                    </ul>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_6') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_7') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_8') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.fulltime_handicap_result') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_9') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_10') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_11') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_12') }}</li>
                                    <ul class="number-bullets">
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_12_1') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_12_2') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_12_3') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_12_4') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_12_5') }}</li>
                                    </ul>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_13') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.handicap_1stHalf') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_14') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_15') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_16') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.lets_roll') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_17') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_18') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_handicap') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_19') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_20') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_let_1stHalf') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_21') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_22') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_23') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.betting_sizes') }}</h3>
                                <h4>{{ trans('rule.ruleTitles.general_rule') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_24') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_25') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_26') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_27') }}</li>
                                    <ul class="alpha-bullets">
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_27_1') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_27_2') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_27_3') }}</li>
                                    </ul>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28') }}</li>
                                    <ul class="alpha-bullets">
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28_1') }}</li>
                                        <ul class="roman-bullets">
                                            <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28_1_1') }}</li>
                                            <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28_1_2') }}</li>
                                        </ul>   
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28_2') }}</li> 
                                        <ul class="roman-bullets">
                                            <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28_2_1') }}</li>
                                            <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28_2_2') }}</li>
                                        </ul> 
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28_3') }}</li>
                                        <ul class="roman-bullets">
                                            <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28_3_1') }}</li>
                                            <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_28_3_2') }}</li>
                                        </ul>  
                                    </ul>
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.goal_largeSmall') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_29') }}</li>   
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_30') }}</li> 
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.goal_overUnder_1stHalf') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_31') }}</li>   
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_32') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_33') }}</li> 
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.rolling_ball_overUnder') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_34') }}</li>   
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_goal_overUnder') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_35') }}</li>   
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_36') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_37') }}</li> 
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_goals_overUnder_1stHalf') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_38') }}</li>   
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_39') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_40') }}</li> 
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.team_goals_overUnder') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_41') }}</li>   
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_42') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_43') }}</li> 
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_44') }}</li> 
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.solo_winners') }}</h3>
                                <h4>{{ trans('rule.ruleTitles.general_rule') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_45') }}</li>   
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_46') }}</li>
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
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_49') }}</li>   
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_50') }}</li>  
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_51') }}</li>  
                                    <ul class="number-bullets">
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_52_1') }}</li>  
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_52_2') }}</li>  
                                        <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_52_3') }}</li>   
                                        <ul class="upper-alpha-bullets">
                                            <li><h4>{{ trans('rule.ruleTitles.example_1') }}</h4></li>  
                                            <ul class="roman-bullets">
                                                <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_53_1') }}</li>
                                                <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_53_2') }}</li>
                                                <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_53_3') }}</li>
                                            </ul>
                                            <li><h4>{{ trans('rule.ruleTitles.example_2') }}</h4></li>  
                                            <ul class="roman-bullets">
                                                <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_54_1') }}</li>
                                                <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_54_2') }}</li>
                                                <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_54_3') }}</li>
                                            </ul>
                                        </ul>
                                        <li>{{ trans('rule.ruleTitles.example_2') }}</li>
                                    </ul>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_win_alone') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_55') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_56') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_winAlone_1stHalf') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_57') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_58') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_59') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.crts') }}</h3>
                                <h4>{{ trans('rule.ruleTitles.general_rule') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_60') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_61') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_62') }}</li>
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.crts_1stHalf') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_63') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_64') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_65') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.rc_soccor_66') }}</li>
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
<!-- <link href="{{ asset('css/rule.css?v=' . $system_config['version']) }}" rel="stylesheet"> -->
<link href="{{ asset('css/rule.css?v=' . $current_time) }}" rel="stylesheet">
<style>	
/* 寫入頁面限定CSS */
</style>
@endSection
@push('main_js')
<script src="{{ asset('js/bootstrap.min.js?v=' . $system_config['version']) }}"></script>
<!-- <script src="{{ asset('js/bootstrap.min.js?v=' .$current_time) }}"></script> -->
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

    // 左邊菜單  當點擊體育或串關時 移除目前選中樣式
    $('.menuTypeBtn').click(function(){
        let key = $(this).attr('key')
        if( (key === 'index' || key === 'm_order' || key === 'match') && $(this).hasClass('on') ) {
            $('div[key="rule"] .slideMenuTag').css('border-bottom-left-radius','0')
            $('div[key="rule"] .slideMenuTag').css('border-top-left-radius','0')
            $('div[key="rule"] .slideMenuTag').css('background-color','#415b5a')
            $('div[key="rule"] .slideMenuTag').css('color','white')

            $('div[key="match"] .slideMenuTag').css('border-bottom-right-radius','0')
            $('div[key="logs"] .slideMenuTag').css('border-top-right-radius','0')
        } else {
            $('div[key="rule"] .slideMenuTag').css('border-bottom-left-radius','25px')
            $('div[key="rule"] .slideMenuTag').css('border-top-left-radius','25px')
            $('div[key="rule"]').css('background-color','#415b5a')
            $('div[key="rule"] .slideMenuTag').css('background-color','rgb(196, 211, 211)')
            $('div[key="rule"] .slideMenuTag').css('color','#415b5a')

            $('div[key="match"] .slideMenuTag').css('border-bottom-right-radius','15px')
            $('div[key="logs"] .slideMenuTag').css('border-top-right-radius','15px')
        }
    })

    $("button.nav-link").click(function() {
        $(".rule-tab-con").animate({ scrollTop: 0 }, "smooth");
        console.log("top");   
    });
    
</script>
@endpush