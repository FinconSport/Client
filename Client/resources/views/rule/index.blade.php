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
                                    <li>{{ trans('rule.generalRulesBaseball.gr_baseball_1') }}</li>
                                    <li>{{ trans('rule.generalRulesBaseball.gr_baseball_2') }}</li>
                                    <li>{{ trans('rule.generalRulesBaseball.gr_baseball_3') }}</li>
                                    <li>{{ trans('rule.generalRulesBaseball.gr_baseball_4') }}</li>
                                    <li>{{ trans('rule.generalRulesBaseball.gr_baseball_5') }}</li>
                                    <li>{{ trans('rule.generalRulesBaseball.gr_baseball_6') }}</li>
                                    <li>{{ trans('rule.generalRulesBaseball.gr_baseball_7') }}</li>
                                    <li>{{ trans('rule.generalRulesBaseball.gr_baseball_8') }}</li>
                                    <li>{{ trans('rule.generalRulesBaseball.gr_baseball_9') }}</li>
                                    <li>{{ trans('rule.generalRulesBaseball.gr_baseball_10') }}</li>
                                    <li>{{ trans('rule.generalRulesBaseball.gr_baseball_11') }}</li>
                                    <ul>
                                        <li>{{ trans('rule.generalRulesBaseball.gr_baseball_11_1') }}</li>
                                        <li>{{ trans('rule.generalRulesBaseball.gr_baseball_11_2') }}</li>
                                        <ul>
                                            <li>{{ trans('rule.generalRulesBaseball.gr_baseball_11_2_1') }}</li>
                                            <li>{{ trans('rule.generalRulesBaseball.gr_baseball_11_2_2') }}</li>
                                        </ul>
                                    </ul>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.betting_type') }}</h3>
                                <h3>{{ trans('rule.ruleTitles.moneyline') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.moneyline_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.moneyline_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.get_the_ball') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.getTheBall_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.getTheBall_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.inplay_handicap') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.inplayHandicap_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.inplayHandicap_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.total_score') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.totalScore_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.totalScore_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.totalScore_3') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.totalScore_4') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.totalScore_5') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.totalScore_6') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.rolling_total_score') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.rollingTotalScore_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rollingTotalScore_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.rollingTotalScore_3') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.total_score_sd') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.totalScoreSd_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.totalScoreSd_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.solo_win') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.soloWin_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.soloWin_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.team_scores') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.teamScores_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.teamScores_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.overtime') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBaseBall.overtime_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBaseBall.overtime_2') }}</li>
                                </ul>
                            </div>
                            <!-- basketball -->
                            <div class="tab-pane" id="nav2" role="tabpanel" aria-labelledby="nav-2">
                                <h3>{{ trans('rule.ruleTitles.sportName.2') }}</h3>
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
                                <h3>{{ trans('rule.ruleTitles.betting_type') }}</h3>
                                <h3>{{ trans('rule.ruleTitles.moneyline') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.moneyline_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.moneyline_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.get_the_ball') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.getTheBall_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.getTheBall_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.getTheBall_3') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.getTheBall_4') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.inplay_handicap') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.inplayHandicap_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.inplayHandicap_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.total_score') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.totalScore_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.totalScore_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.totalScore_3') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.totalScore_4') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.totalScore_5') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.totalScore_6') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.totalScore_7') }}</li>
                                    <ul class="alpha-bullets">
                                        <li>{{ trans('rule.ruleContentsBasketball.totalScore_7_1') }}</li>
                                        <li>{{ trans('rule.ruleContentsBasketball.totalScore_7_2') }}</li>
                                    </ul>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.rolling_total_score') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.rollingTotalScore_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rollingTotalScore_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.rollingTotalScore_3') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.team_scores') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.teamScores_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.teamScores_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.teamScores_3') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.total_points') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsBasketball.totalPoints_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsBasketball.totalPoints_2') }}</li>
                                </ul>
                            </div>
                            <!-- Soccor -->
                            <div class="tab-pane" id="nav3" role="tabpanel" aria-labelledby="nav-3">
                                <h3>{{ trans('rule.ruleTitles.sportName.3') }}</h3>
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
                                <h3>{{ trans('rule.ruleTitles.handicap') }}</h3>
                                <h4>{{ trans('rule.ruleTitles.general_rule') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.handicap_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.handicap_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.handicap_3') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.handicap_4') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.handicap_5') }}</li>
                                    <ul class="alpha-bullets">
                                        <li>{{ trans('rule.ruleContentsSoccor.handicap_5_1') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.handicap_5_2') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.handicap_5_3') }}</li>
                                    </ul>
                                    <li>{{ trans('rule.ruleContentsSoccor.handicap_6') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.handicap_7') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.handicap_8') }}</li>
                                </ul>
                                <hr class="solid">

                                <h3>{{ trans('rule.ruleTitles.fulltime_handicap_result') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.fulltimeHandicapResult_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.fulltimeHandicapResult_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.fulltimeHandicapResult_3') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.fulltimeHandicapResult_4') }}</li>
                                    <ul class="number-bullets">
                                        <li>{{ trans('rule.ruleContentsSoccor.fulltimeHandicapResult_4_1') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.fulltimeHandicapResult_4_2') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.fulltimeHandicapResult_4_3') }}</li>
                                    </ul>
                                    <li>{{ trans('rule.ruleContentsSoccor.fulltimeHandicapResult_5') }}</li>
                                </ul>
                                <hr class="solid">

                                <h3>{{ trans('rule.ruleTitles.handicap_1stHalf') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.handicap1stHalf_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.handicap1stHalf_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.handicap1stHalf_3') }}</li>
                                </ul>
                                <hr class="solid">

                                <h3>{{ trans('rule.ruleTitles.inplay_handicap') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.inplayHandicap_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.inplayHandicap_2') }}</li>
                                </ul>
                                <hr class="solid">

                                <h3>{{ trans('rule.ruleTitles.ot_handicap') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.otHandicap_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.otHandicap_2') }}</li>
                                </ul>
                                <hr class="solid">

                                <h3>{{ trans('rule.ruleTitles.ot_let_1stHalf') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.otLet1stHalf_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.otLet1stHalf_2') }}</li>
                                </ul>
                                <hr class="solid">

                                <h3>{{ trans('rule.ruleTitles.betting_sizes') }}</h3>
                                <h4>{{ trans('rule.ruleTitles.general_rule') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_3') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_4') }}</li>
                                    <ul class="alpha-bullets">
                                        <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_4_1') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_4_2') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_4_3') }}</li>
                                    </ul>
                                    <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_5') }}</li>
                                    <ul class="alpha-bullets">
                                        <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_5_1') }}</li>
                                        <ul class="roman-bullets">
                                            <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_5_1_1') }}</li>
                                            <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_5_1_2') }}</li>
                                        </ul>
                                        <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_5_2') }}</li>
                                        <ul class="roman-bullets">
                                            <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_5_2_1') }}</li>
                                            <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_5_2_2') }}</li>
                                        </ul>
                                        <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_5_3') }}</li>
                                        <ul class="roman-bullets">
                                            <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_5_3_1') }}</li>
                                            <li>{{ trans('rule.ruleContentsSoccor.bettingSizes_5_3_2') }}</li>
                                        </ul>
                                    </ul>
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.goal_largeSmall') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.goalLargeSmall_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.goalLargeSmall_2') }}</li>
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.goal_overUnder_1stHalf') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.goalOverUnder1stHalf_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.goalOverUnder1stHalf_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.goalOverUnder1stHalf_3') }}</li>
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.rolling_ball_overUnder') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.rollingBallOverUnder_1') }}</li>
                                </ul>
                                <hr class="solid">

                                <h3>{{ trans('rule.ruleTitles.ot_goal_overUnder') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.otGoalOverUnder_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.otGoalOverUnder_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.otGoalOverUnder_3') }}</li>
                                </ul>

                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_goals_overUnder_1stHalf') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.otGoalsOverUnder1stHalf_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.otGoalsOverUnder1stHalf_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.otGoalsOverUnder1stHalf_3') }}</li>
                                </ul>
                                <hr class="solid">

                                <h3>{{ trans('rule.ruleTitles.team_goals_overUnder') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.teamGoalsOverUnder_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.teamGoalsOverUnder_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.teamGoalsOverUnder_3') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.teamGoalsOverUnder_4') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.moneyline') }}</h3>
                                <h4>{{ trans('rule.ruleTitles.general_rule') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.moneyline_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.moneyline_2') }}</li>
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.win_alone') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.winAlone_1') }}</li>
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.win_alone_1stHalf') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.winAlone1stHalf_1') }}</li>
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.score_goal') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.scoreGoal_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.scoreGoal_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.scoreGoal_3') }}</li>
                                    <ul class="number-bullets">
                                        <li>{{ trans('rule.ruleContentsSoccor.scoreGoal_3_1') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.scoreGoal_3_2') }}</li>
                                        <li>{{ trans('rule.ruleContentsSoccor.scoreGoal_3_3') }}</li>
                                        <ul class="upper-alpha-bullets">
                                            <li><h4>{{ trans('rule.ruleTitles.example_1') }}</h4></li> 
                                            <ul class="roman-bullets">
                                                <li>{{ trans('rule.ruleContentsSoccor.scoreGoal_3_1_1') }}</li>
                                                <li>{{ trans('rule.ruleContentsSoccor.scoreGoal_3_1_2') }}</li>
                                                <li>{{ trans('rule.ruleContentsSoccor.scoreGoal_3_1_3') }}</li>
                                            </ul> 
                                            <li><h4>{{ trans('rule.ruleTitles.example_1') }}</h4></li> 
                                            <ul class="roman-bullets">
                                                <li>{{ trans('rule.ruleContentsSoccor.scoreGoal_3_2_1') }}</li>
                                                <li>{{ trans('rule.ruleContentsSoccor.scoreGoal_3_2_2') }}</li>
                                                <li>{{ trans('rule.ruleContentsSoccor.scoreGoal_3_2_3') }}</li>
                                            </ul> 
                                        </ul>
                                    </ul>
                                    <li>{{ trans('rule.ruleTitles.example_2') }}</li>
                                </ul>
                                <hr class="solid">

                                <h3>{{ trans('rule.ruleTitles.ot_win_alone') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.otWinAlone_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.otWinAlone_2') }}</li>
                                </ul>
                                <hr class="solid">
                                <h3>{{ trans('rule.ruleTitles.ot_winAlone_1stHalf') }}</h3>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.otWinAlone1stHalf_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.otWinAlone1stHalf_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.otWinAlone1stHalf_3') }}</li>
                                </ul>
                                <hr class="solid">

                                <h3>{{ trans('rule.ruleTitles.crts') }}</h3>
                                <h4>{{ trans('rule.ruleTitles.general_rule') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.crts_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.crts_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.crts_3') }}</li>
                                </ul>
                                <h4>{{ trans('rule.ruleTitles.crts_1stHalf') }}</h4>
                                <ul class="number-bullets">
                                    <li>{{ trans('rule.ruleContentsSoccor.crts1stHalf_1') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.crts1stHalf_2') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.crts1stHalf_3') }}</li>
                                    <li>{{ trans('rule.ruleContentsSoccor.crts1stHalf_4') }}</li>
                                </ul>
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
                                    <hr class="solid {{ ($title === 'inPlay_overUnder') ? 'd-none' : '' }}">
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
                                    <hr class="solid {{ ($title === 'inPlay_overUnder') ? 'd-none' : '' }}">
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