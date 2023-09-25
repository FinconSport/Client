<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Session;
use DB;
// use Exception;

// use App\Models\GameMatch;
use App\Models\GameResult;
use App\Models\GameOrder;

// LSport
use App\Models\LsportFixture;
use App\Models\LsportLeague;
use App\Models\LsportSport;
use App\Models\LsportTeam;
use App\Models\LsportMarket;
use App\Models\LsportMarketBet;

use App\Models\PlayerOnline;
use App\Models\Player;
use App\Models\Agent;
use App\Models\PlayerBalanceLogs;
use App\Models\ClientMarquee;
use App\Models\SystemConfig;

define('DEFAULT_SPORT_ID', 154914);  //預設的 sport_id (棒球)

/**
 * LsportApiController
 * 
 * Client端的前端所需的資料接口。對應號源:LSports。
 * Providing data sources needed by the Client front-end. Corresponding dataset source: LSports.
 */

class LsportApiController extends Controller {
    
    protected $page_limit = 20;

    protected $agent_lang;  // 玩家的代理的語系. 選擇相對應的DB翻譯欄位時會用到.

    /**
     * index
     *
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     * @return view('match.index') = 賽事頁面index。view('match.index') = MATCH page's index。
     */
    public function index(Request $request) {
        return view('match.index', $this->data);
    }

    /**
     * CommonAccount
     * 
     * 先以玩家ID檢查玩家是否需要重新登入。
     * 如果無須重新登入則回傳玩家的帳號名稱及帳戶餘額。
     * Firstly checks if the player needs to re-login.
     * If re-login is not required, then return an array that includes the player's account name and account balance.
     *
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     * @return ::ApiSuccess($data = ARRAY{account, balance}) | ApiError
     */
    public function CommonAccount(Request $request) {
      
        $input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
          $this->ApiError("PLAYER_RELOGIN", true);
        }

        // 獲取用戶資料
        $player_id = $input['player'];
        $return = Player::where("id", $player_id)->first();
        if ($return === false) {
          $this->ApiError("01");
        }

        if ($return['status'] != 1) {
          $this->ApiError("02");
        }

        $data = array(
            'account' => $return['account'],
            'balance' => $return['balance'],
        );
        
        $this->ApiSuccess($data, "01");
    }

    /**
     * IndexCarousel
     * 
     * 取出當前有效的'賽事結果'讓前端顯示。
     * 
     *
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     * @return ::ApiSuccess($data = ARRAY 篩選過的賽事結果) | ApiError
     */
    // 輪播
    public function IndexCarousel(Request $request) {
      
        $input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }


        $fakeData = [
            [
                "id" => 1,
                "home" => '主人A隊',
                "away" => '客人B隊',
                "home_score" => 2,
                "away_score" => 8,
                "sell_status" => 1,
                "match_time" => '2023-09-25 09:08:16'
            ],
            [
                "id" => 1,
                "home" => '主C隊',
                "away" => '客D隊',
                "home_score" => 6,
                "away_score" => 127,
                "sell_status" => 1,
                "match_time" => '2023-09-25 09:15:40'
            ],
            [
                "id" => 1,
                "home" => '主主主甲隊',
                "away" => '客客客乙隊',
                "home_score" => 26,
                "away_score" => 35,
                "sell_status" => 1,
                "match_time" => '2023-09-25 09:30:36'
            ],
        ];
        //---------------------------------

        //欲回傳的賽事結果的欄位
        $arrColsToReturn = array(
            "id",
            "home",
            "away",
            "home_score",
            "away_score",
            "sell_status",
            "match_time"
        );

        // 篩選要回傳的賽事結果的欄位
        $data = array();
        foreach ($fakeData as $k => $v) {
            $tmp = array();
            foreach ($arrColsToReturn as $key => $val) {
                $tmp[$val] = $v[$val];
            }

            // 日期格式重整
            $tmp['match_time'] = date('Y-m-d H:i:s', strtotime($tmp['match_time']));
            $data[] = $tmp;
        }

        $this->ApiSuccess($data, "01");

    }

    /**
     * IndexMarquee
     * 
     * 取出當前有效的'Client端跑馬燈'(也就是Client端系統公告)讓前端顯示。
     * 
     * 
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     * @return ApiSuccess($data = ARRAY Client跑馬燈資料) | ApiError
     */
    // 首頁跑馬燈
    public function IndexMarquee(Request $request) {
      
    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        //---------------------------------
        // 自DB取出有效的Client端跑馬燈(此跑馬燈其實也就是Client端系統公告)
        $return = ClientMarquee::where("status", 1)->get();      
        if ($return === false) {
            $this->ApiError("01");
        }

        $data = array();
        foreach ($return as $k => $v) {
            $data[] = $v['marquee'];
        }

        $this->ApiSuccess($data, "01");
    }

    /**
     * IndexNotice
     * 
     * 取出各種公告(依系統、各球種...分類)讓前端顯示。
     * 
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     * @return ApiSuccess($data = ARRAY Client各種公告列表) | ApiError
     */
    // 系統公告接口
    public function IndexNotice(Request $request) {

    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        //---------------------------------
        // 自DB取出有效的Client端系統公告(其實也就是Client端跑馬燈)
        $notice_list = array();

        // 系統公告
        // ????????????????????????????????????????? 下面怪怪的
        $return = ClientMarquee::where("status", 1)->get();      
        if ($return === false) {
            $this->ApiError("01");
        }

        foreach ($return as $k => $v) {
            $sport_id = 0;
            $title = $v['title'];
            $context = $v['marquee'];
            $create_time = $v['create_time'];

            $notice_list[$sport_id][] = [
                "sport_id" => $sport_id,
                "title" => $title,
                "context" => $context,
                "create_time" => $create_time,
            ];
        }

        // gzip
        $notice_list = $this->gzip($notice_list);

        $this->ApiSuccess($notice_list, "01", true);
    }

    /**
     * IndexMatchList
     *
     * 取出賽事列表讓前端顯示。
     * 
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     * @return ApiSuccess($data = ARRAY 賽事列表) | ApiError
     */
    // 首頁賽事
    public function IndexMatchList(Request $request) {
      
    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        // 取得語系
        $langCol = 'name_' . $this->agent_lang;

    	//---------------------------------
        // 取得球種資料
        $arrSports = LsportSport::get();
        if ($arrSports === false) {
            $this->ApiError("01");
        }

        $sport_type = array();
        foreach ($arrSports as $k => $v) {
            $sport_type[$v['sport_id']] = $v[$langCol];
        }

        $menu_type = [
            0 => "living",  //走地
            1 => "early",  //早盤
        ];

        $data = array();
        //$total = 0;

        foreach ($menu_type as $k => $v) {

            switch ($k) {
                case 0:  // 進行中

                    $arrFixtures = LsportFixture::join('lsport_market_bet', 'lsport_fixture.fixture_id', '=', 'lsport_market_bet.fixture_id')
                        ->join('lsport_league', function ($join) {
                            $join->on('lsport_fixture.sport_id', '=', 'lsport_league.sport_id')
                                 ->on('lsport_fixture.league_id', '=', 'lsport_league.league_id');
                        })
                        ->selectRaw('lsport_fixture.sport_id, COUNT(DISTINCT lsport_fixture.id) AS count, COUNT(*) AS rate_count')
                        //->where('lsport_market_bet.is_active', '=', 1)
                        ->where('lsport_fixture.status', 2)  // 2=進行中
                        ->where('lsport_league.status', 1)
                        ->groupBy('lsport_fixture.sport_id')
                        ->having('rate_count', '>', 0)
                        ->get();

                    if ($arrFixtures === false) {
                        $this->ApiError("01");
                    }
                    
                    $tmp = array();
                    $total = 0;
                    foreach ($arrFixtures as $kk => $vv) {
                        $tmp["items"][$vv['sport_id']]['name'] = $sport_type[$vv['sport_id']];
                        $tmp["items"][$vv['sport_id']]['count'] = $vv['count'];
                        $total += $vv['count'];
                    }

                    $tmp['total'] = $total;
                    $data[$v] = $tmp;
                    break;

                case 1:  // 早盤

                    $arrFixtures = LsportFixture::join('lsport_market_bet', 'lsport_fixture.fixture_id', '=', 'lsport_market_bet.fixture_id')
                        ->join('lsport_league', function ($join) {
                            $join->on('lsport_fixture.sport_id', '=', 'lsport_league.sport_id')
                                 ->on('lsport_fixture.league_id', '=', 'lsport_league.league_id');
                        })
                    ->selectRaw('lsport_fixture.sport_id, COUNT(DISTINCT lsport_fixture.id) AS count, COUNT(*) AS rate_count')
                    //->where('lsport_market_bet.is_active', '=', 1)
                    ->where('lsport_fixture.status', 1)  // 1=尚未開打
                    ->where('lsport_league.status', 1)
                    ->groupBy('lsport_fixture.sport_id')
                    ->having('rate_count', '>', 0)
                    ->get();

                    if ($arrFixtures === false) {
                        $this->ApiError("01");
                    }
                    
                    $tmp = array();
                    $total = 0;
                    foreach ($arrFixtures as $kk => $vv) {
                        $tmp["items"][$vv['sport_id']]['name'] = $sport_type[$vv['sport_id']];
                        $tmp["items"][$vv['sport_id']]['count'] = $vv['count'];
                        $total += $vv['count'];
                    }

                    $tmp['total'] = $total;
                    $data[$v] = $tmp;
                    break;

                default:
                    break;
            }
            
            // 處理加總
            $total = array_sum($data[$v]);
            $data[$v]['total'] = $total;
            
        }

        $this->ApiSuccess($data, "01"); 
    }


/****************************************
 *    
 *    賽事列表頁
 *    
****************************************/

    /**
     * MatchSport
     *
     * 取回當前體育的所有球種(體育類型)的列表。
     *
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     * @return ApiSuccess($data = ARRAY 球種列表) | ApiError
     */
    // 球種列表
    public function MatchSport(Request $request) {
      
    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        // 取得語系
        $langCol = 'name_' . $this->agent_lang;

        //---------------------------------
        // 取得球種資料
        $Sports = LsportSport::where('status', 1)
            ->select(
                'sport_id', 'name_en AS name_en', $langCol.' AS name_locale', 'status'
            )
            ->orderBy('id', 'ASC')
            ->get();

        if ($Sports === false) {
            $this->ApiError("01");
        }

        $arrAllSports = array();
        foreach ($Sports as $dk => $sv) {

            // sport_name: 判斷用戶語系資料是否為空,若是則用en就好
            if (!strlen($sv->name_locale)) {  // sport name
                $sport_name = $sv->name_en;
            } else {
                $sport_name = $sv->name_locale;
            }

            $arrAllSports[] = array(
                'sport_id' => $sv->sport_id,
                'name' => $sport_name,
                //'status' => $sv->status,
            );
        }

        $this->ApiSuccess($arrAllSports, "01"); 

    }

    /**
     * MatchSport
     *
     * 取回一指定球種的近期賽事(2天內開賽)的列表包含賠率及聯盟等。
     *
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     *                          # *sport_id: 球種ID。
     * @return ApiSuccess($data = ARRAY 賽事列表) | ApiError
     */
    public function MatchIndex(Request $request) {
      
    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        // 取得語系
        $langCol = 'name_' . $this->agent_lang;

        //////////////////////////////////////////

        if (!isset($input['sport_id'])) {
            $this->ApiError("01");
        }
        $sport_id = $input['sport_id'];


        //////////////////////////////////////////

        //取2天內賽事
        $today = time();
        $after_tomorrow = $today + 2 * 24 * 60 * 60; 
        $after_tomorrow = date('Y-m-d 00:00:00', $after_tomorrow);

        //lsport_fixture.status 賽事狀態
        define('FIXTURE_STATUS', array(
            'early' => 1,  // 未開賽
            'living' => 2,  // 賽事中
        ));

        //////////////////////////////////////////
        // DB取出賽事

        $data = DB::table('lsport_league as l')
            ->join('lsport_sport as s', 'l.sport_id', '=', 's.sport_id')
            ->join('lsport_fixture as f', 'l.league_id', '=', 'f.league_id')
            ->join('lsport_market as m', 'f.fixture_id', '=', 'm.fixture_id')
            ->join('lsport_team as th', function ($join) {
                $join->on('f.home_id', '=', 'th.team_id')
                ->on('l.league_id', '=', 'th.league_id');
            })
            ->join('lsport_team as ta', function ($join) {
                $join->on('f.away_id', '=', 'ta.team_id')
                ->on('l.league_id', '=', 'ta.league_id');
            })
            ->select(
                'l.name_en AS l_name_en', 'l.'.$langCol.' AS l_name_locale',
                's.name_en AS s_name_en', 's.'.$langCol.' AS s_name_locale',
                'f.fixture_id', 'f.sport_id', 'f.league_id', 'f.start_time', 'f.livescore_extradata', 'f.periods', 'f.scoreboard', 'f.status AS f_status', 'f.last_update AS f_last_update', //'f.home_id', 'f.away_id', 
                'm.market_id', 'm.name_en AS m_name_en', 'm.'.$langCol.' AS m_name_locale', 'm.priority', 'm.main_line',
                'th.team_id AS th_team_id', 'th.name_en AS th_name_en', 'th.'.$langCol.' AS th_name_locale',
                'ta.team_id AS ta_team_id', 'ta.name_en AS ta_name_en', 'ta.'.$langCol.' AS ta_name_locale'
            )
            //->where('l.status', 1)  // 原本只抓1=尚未開賽
            ->where('l.status', 1)
            ->where('l.sport_id', $sport_id)
            ->where('s.sport_id', $sport_id)
            ->whereIn('f.status', [1, 2])  //可區分:未開賽及走地中
            ->where('f.start_time', "<=", $after_tomorrow)
            ->where("th.sport_id", $sport_id)
            ->where("th.sport_id", $sport_id)
            ->orderBy('l.league_id', 'ASC')
            ->orderBy('f.fixture_id', 'ASC')
            ->orderBy('m.market_id', 'ASC')
            ->get();

        if ($data === false) {
            $this->ApiError('02');
        }

        //儲存league-fixture-market的階層資料
        $arrLeagues = array(
            FIXTURE_STATUS['early'] => array(),  // 早盤
            FIXTURE_STATUS['living'] => array(),  // 走地
        );
        //$arrFixtureAndMarkets = array();  //將用於稍後SQL查詢market_bet資料
        $sport_name = '';  //儲存球種名稱

/*
{
    early : {
        Sport_id : { 
            League_id : {
                Fixture_id: {
                    Fixture.*,
                    Market : [
                        Market_id : {
                            Market.id,
                            Market.name : *LANG*,
                            Bet : [
                                Bet_id : {
                                    Bet.*
                                }
                            ]
                        }
                    ]
                }
            }
    },
    living : {
        ... 結構同上
    }
}
*/

        //////////////////////////////////////////
        // 開始loop 賽事資料

        foreach ($data as $dk => $dv) {
            $league_id = $dv->league_id;
            $fixture_id = $dv->fixture_id;
            $market_id = $dv->market_id;
            $main_line = $dv->main_line;
            $fixture_status = intval($dv->f_status);

            // sport_name: 判斷用戶語系資料是否為空,若是則用en就好
            if (!strlen($sport_name)) {  //只須設定一次
                if (!strlen($dv->s_name_locale)) {  // sport name
                    $sport_name = $dv->s_name_en;
                } else {
                    $sport_name = $dv->s_name_locale;
                }
            }

            // league 層 ----------------------------
            if (!isset($arrLeagues[$fixture_status][$league_id])
                || !sizeof($arrLeagues[$fixture_status][$league_id])
                ) {

                // league_name: 判斷用戶語系資料是否為空,若是則用en就好
                if (!strlen($dv->l_name_locale)) {  // league name
                    $league_name = $dv->l_name_en;
                } else {
                    $league_name = $dv->l_name_locale;
                }

                // 包入 league 聯賽資料
                $arrLeagues[$fixture_status][$league_id] = array(
                    'league_id' => $dv->league_id,
                    'league_name' => $league_name,
                    'list' => array(),
                );
            }

            // fixture 層 ----------------------------
            if (!isset($arrLeagues[$fixture_status][$league_id]['list'][$fixture_id])
                || !sizeof($arrLeagues[$fixture_status][$league_id]['list'][$fixture_id])
                ) {

                // home_team_name: 判斷用戶語系資料是否為空,若是則用en就好
                if (!strlen($dv->th_name_locale)) {  // home team
                    $home_team_name = $dv->th_name_en;
                } else {
                    $home_team_name = $dv->th_name_locale;
                }
                // away_team_name: 判斷用戶語系資料是否為空,若是則用en就好
                if (!strlen($dv->ta_name_locale)) {  // away_team
                    $away_team_name = $dv->ta_name_en;
                } else {
                    $away_team_name = $dv->ta_name_locale;
                }

                $periods = null;
                $scoreboard = null;

                // 包入 fixture 賽事資料 ---------------
                $arrLeagues[$fixture_status][$league_id]['list'][$fixture_id] = array(
                    //'sport_id' => $dv->sport_id,
                    //'league_id' => $dv->league_id,
                    //'home_id' => $dv->home_id,
                    //'away_id' => $dv->away_id,
                    'fixture_id' => $dv->fixture_id,
                    'start_time' => $dv->start_time,
                    //'livescore_extradata' => $dv->livescore_extradata,  // 目前開發階段先不給
                    //'periods' => $dv->periods,  // 目前開發階段先不給
                    //'scoreboard' => $dv->scoreboard,  // 目前開發階段先不給
                    'status' => $fixture_status,
                    'last_update' => $dv->f_last_update,
                    'home_team_id' => $dv->th_team_id,
                    'home_team_name' => $home_team_name,
                    'away_team_id' => $dv->ta_team_id,
                    'away_team_name' => $away_team_name,
                    'list' => array(),
                    'periods' => $periods,
                    'scoreboard' => $scoreboard,
                );
            }

            //market 層 ----------------------------
            if (!isset($arrLeagues[$fixture_status][$league_id]['list'][$fixture_id]['list'][$market_id])
                || !sizeof($arrLeagues[$fixture_status][$league_id]['list'][$fixture_id]['list'][$market_id])) {

                // market_name: 判斷用戶語系資料是否為空,若是則用en就好
                if (!strlen($dv->m_name_locale)) {  // market name
                    $market_name = $dv->m_name_en;
                } else {
                    $market_name = $dv->m_name_locale;
                }

                // 包入 market 玩法資料 ---------------
                $arrLeagues[$fixture_status][$league_id]['list'][$fixture_id]['list'][$market_id] = array(
                    'market_id' => $dv->market_id,
                    'market_name' => $market_name,
                    'priority' => $dv->priority,
                    'main_line' => $dv->main_line,
                    'list' => array(),
                );
 
                //取出[賽事+玩法+玩法.base_line]的賠率 ----------------------------
                $marketBetData = DB::table('lsport_market_bet as mb')
                ->select(
                    'mb.bet_id',
                    'mb.base_line',
                    'mb.line',
                    'mb.name_en AS mb_name_en',
                    'mb.name_'.$this->agent_lang.' AS mb_name_locale',
                    'mb.price',
                    'mb.status AS status',
                    'mb.last_update AS last_update',
                )
                ->where('mb.fixture_id', $fixture_id)
                ->where('mb.market_id', $market_id)
                ->where('mb.base_line', $main_line)  //這邊用 base_line 或 line 都可以
                ->orderBy('mb.bet_id', 'ASC')  //注意排序
                ->get();

                if ($marketBetData === false) {
                    $this->ApiError('03');
                }

                // 開始繞賠率資料
                foreach ($marketBetData as $bk => $bv) {
                    $market_bet_id = $bv->bet_id;

                    // market_bet_name: 判斷用戶語系資料是否為空,若是則用en就好
                    if (isset($bv->mb_name_locale)) {  // market name
                        $market_bet_name = $bv->mb_name_en;
                    } else {
                        $market_bet_name = $bv->mb_name_locale;
                    }

                    // 包入 market_bet 賠率資料 ---------------
                    $arrLeagues[$fixture_status][$league_id]['list'][$fixture_id]['list'][$market_id]['list'][$market_bet_id] = array(
                        'market_bet_id' => $market_bet_id,
                        'market_bet_name' => $market_bet_name,
                        'base_line' => $bv->base_line,
                        'line' => $bv->line,
                        'price' => $bv->price,
                        'status' => $bv->status,
                        'last_update' => $bv->last_update,
                    );
                }
            }
        }

        ///////////////////////////////

        $arrRet = array();  //用於回傳結果

        // early 早盤
        $arrRet['early'][$sport_id] = array(
            'sport_id' => $sport_id,
            'sport_name' => $sport_name,
            'list' => $arrLeagues[FIXTURE_STATUS['early']],
        );

        // living 走地
        $arrRet['living'][$sport_id] = array(
            'sport_id' => $sport_id,
            'sport_name' => $sport_name,
            'list' => $arrLeagues[FIXTURE_STATUS['living']],
        );

        $data = $arrRet;

        ///////////////////////////////
        // gzip
        $data = $this->gzip($data);

        $this->ApiSuccess($data, "01", true);
        
        // $this->ApiSuccess($data, "01", false);
    }

    /**
     * GameBet
     *
     * 投注接口
     * 
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     * @return ApiSuccess($data = ???) | ApiError
     */
    public function GameBet(Request $request) {
      
    	$input = $this->getRequest($request);

        $return = $this->checkToken($input);
        if ($return === false) {
            $this->ApiError("PLAYER_RELOGIN",true);
        }

        /////////////////////////

        $columns = array(
            "token","player","sport_id","fixture_id","market_id","market_bet_id","bet_rate","bet_amount","better_rate"
        );

        foreach ($columns as $k => $v) {
            if (!isset($input[$v])) {
                $this->ApiError("01");
            }
        }

        /////////////////

        // 取得語系
        $player_id = $input['player'];
        $api_lang = $this->getAgentLang($player_id);
        if ($api_lang === false) {
          $this->ApiError("01");
        }
        
        $name_columns = "name_".$api_lang;


        //////////////////////////////////////////

        // 取得系統參數
        $return = SystemConfig::where("name","risk_order")->first();
        if ($return['value'] > 0) {
            $default_order_status = 1;
            $default_approval_time = null;
        } else {
            // 預設通過
            $default_order_status = 2;
            $default_approval_time = date("Y-m-d H:i:s");
        }

        // 取得必要參數
        $player_id = $input['player'];
        $fixture_id = $input['fixture_id'];  
        $market_id = $input['market_id'];  
        $market_bet_id = $input['market_bet_id'];
        $player_rate = $input['bet_rate'];  //前端傳來的賠率
        $bet_amount = $input['bet_amount'];  //投注金額
        $is_better_rate = $input['better_rate'];  //是否自動接受更好的賠率(若不接受則在伺服器端賠率較佳時會退回投注)

        $sport_id = DEFAULT_SPORT_ID ;  //球種ID
        if (isset($input['sport_id'])) {
            $sport_id = $input['sport_id'];
        }
        
        $order = array();
        
        // 參數檢查 TODO - 初步 隨便弄弄
        if ($bet_amount <= 0) {
            $this->ApiError("02");
        }

        // 取得用戶資料
        $return = Player::where("id",$player_id)->first();
        if ($return == false) {
            $this->ApiError("03");
        }

        // 如果用戶已停用
        if ($return['status'] == 0) {
            $this->ApiError("04");
        }

        $player_account = $return['account'];
        $currency_type = $return['currency_type'];
        $agent_id = $return['agent_id'];
        $player_balance = $return['balance'];

        // 判斷餘額是否足夠下注
        if ($player_balance < $bet_amount) {
            $this->ApiError("05");
        }
        
        //////////////////////////////////////////
        // order data
        $order['player_id'] = $player_id;
        $order['player_name'] = $player_account;
        $order['currency_type'] = $currency_type;
        //////////////////////////////////////////

        // 取得商戶資料
        $return = Agent::where("id",$agent_id)->first();
        if ($return == false) {
            $this->ApiError("06");
        }

        // 如果商戶已停用
        if ($return['status'] == 0) {
            $this->ApiError("07");
        }

        $agent_account = $return['account'];

        //////////////////////////////////////////
        // order data
        $order['agent_id'] = $agent_id;
        $order['agent_name'] = $agent_account;
        //////////////////////////////////////////

        // 取得賽事資料
        $return = LsportFixture::where("fixture_id",$fixture_id)->where("sport_id",$sport_id)->first();
        if ($return == false) {
            $this->ApiError("08");
        }

        $fixture_data = $return;
        $league_id = $fixture_data['league_id'];
        $home_id = $fixture_data['home_id'];
        $away_id = $fixture_data['away_id'];

        //////////////////////////////////////////
        // order data
        $order['fixture_id'] = $fixture_id;
        $order['sport_id'] = $fixture_data['sport_id'];
        //////////////////////////////////////////

        // 取得聯盟資料
        $return = LsportLeague::where("league_id",$league_id)->where("sport_id",$sport_id)->first();
        if ($return == false) {
            $this->ApiError("08");
        }

        $league_data = $return;

        //////////////////////////////////////////
        // order data
        $order['league_id'] = $league_data['league_id'];
        $order['league_name'] = $league_data[$name_columns];
        $order['fixture_id'] = $fixture_id;
        $order['sport_id'] = $league_data['sport_id'];

        //////////////////////////////////////////

        // 取得玩法
        $market_data = LSportMarket::where("market_id",$market_id)->where("fixture_id",$fixture_id)->first();
        if ($market_data == false) {
            $this->ApiError("13");
        }

        $market_priority = $market_data['priority'];

        // 取得賠率
        $market_bet_data = LSportMarketBet::where("fixture_id",$fixture_id)->where("bet_id",$market_bet_id)->first();
        if ($market_bet_data == false) {
          $this->ApiError("14");
        }

        $current_market_bet_status = $market_bet_data['status'];
        $current_market_bet_rate = $market_bet_data['price'];
        $market_bet_line = $market_bet_data['base_line'];

        // 非開盤狀態 1开、2锁、3结算
        if (($current_market_bet_status != 1)) {
            $this->ApiError("15");
        }

        //////////////////////////////////////////
        // order data
        $order['market_id'] = $market_id;
        $order['market_bet_id'] = $market_bet_id;
        $order['market_bet_line'] = $market_bet_line;

        $order['market_name'] = $market_data[$name_columns];
        $order['market_bet_name'] = $market_bet_data[$name_columns];
        $order['market_priority'] = $market_priority;
        $order['bet_rate'] = $current_market_bet_rate;
        
        $order['player_rate'] = $player_rate;
        $order['better_rate'] = $is_better_rate;
        
        //////////////////////////////////////////

        // 判斷 is_better_rate
        if (($is_better_rate == 1) && ($current_market_bet_rate < $player_rate)) {
            $this->ApiError("16");
        }

        //////////////////////////////////////////
        // order data
        $order['bet_amount'] = $bet_amount;
        $order['status'] = $default_order_status;
        $order['create_time'] = date("Y-m-d H:i:s");
        $order['approval_time'] = $default_approval_time;
        
        //////////////////////////////////////////

        // 新增注單資料
        $return = GameOrder::insertGetId($order);      
        if ($return == false) {
            $this->ApiError("17");
        }

        $order_id = $return;
        // 設定m_id 
        $return = GameOrder::where("id",$order_id)->update([
            "m_id" => $order_id
        ]);      
        if ($return == false) {
            $this->ApiError("18");
        }
        
        // 扣款
        $before_amount = $player_balance;
        $change_amount = $bet_amount;
        $after_amount = $before_amount - $change_amount;

        $return = Player::where("id",$player_id)->update([
            "balance" => $after_amount
        ]);      
        if ($return == false) {
            $this->ApiError("19");
        }
        
        // 帳變
        $tmp = array();
        $tmp['agent_id'] = $agent_id;
        $tmp['player_id'] = $player_id;
        $tmp['player'] = $player_account;
        $tmp['currency_type'] = $currency_type;
        $tmp['type'] = "game_bet";
        $tmp['change_balance'] = $change_amount;
        $tmp['before_balance'] = $before_amount;
        $tmp['after_balance'] = $after_amount;
        $tmp['create_time'] = date("Y-m-d H:i:s");
        PlayerBalanceLogs::insert($tmp);

        $this->ApiSuccess($return,"01");

    }

    /**
     * mGameBet
     *
     * 串關投注接口
     * 
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     *                          # *bet_amount: 投注金額。
     *                          # *better_rate: 是否接受較佳賠率。
     *                          # sport_id: 球種ID。有預設值(棒球)。
     *                          # *bet_data: 串關注單資料的陣列。
     * @return ApiSuccess($data = ???) | ApiError
     */
    public function mGameBet(Request $request) {
      
    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }
        
        /////////////////////////

        $columns = array(
            "token","player","sport_id","bet_data","bet_amount","better_rate"
        );

        foreach ($columns as $k => $v) {
            if (!isset($input[$v])) {
                $this->ApiError("01");
            }
        }

        //////////////////////////////////////////
        // 取得語系
        $langCol = 'name_' . $this->agent_lang;

        // 取得系統參數
        $arrSysConfig = SystemConfig::where("name","risk_order")->first();
        if ($arrSysConfig['value'] > 0) {
            $default_order_status = 1;
            $default_approval_time = null;
        } else {
            // 預設通過
            $default_order_status = 2;
            $default_approval_time = date("Y-m-d H:i:s");
        }

        // 取得必要參數
        $player_id = $input['player'];
        $bet_amount = $input['bet_amount'];  //投注金額
        $is_better_rate = $input['better_rate'];  //是否自動接受更好的賠率(若不接受則在伺服器端賠率較佳時會退回投注)

        $sport_id = DEFAULT_SPORT_ID;
        if (isset($input['sport_id'])) {
            $sport_id = $input['sport_id'];
        }

        $arrBetData = json_decode($input['bet_data'], true);

        //串關的注單數不能低於2
        if (sizeof($arrBetData) < 2) {
            $this->ApiError("20");
        }

        //$order = array();
        
        // 參數檢查 TODO - 初步 隨便弄弄
        if ($bet_amount <= 0) {
            $this->ApiError("01");
        }

        // 取得用戶資料
        $arrPlayerData = Player::where("id", $player_id)->first();
        if ($arrPlayerData == false) {
            $this->ApiError("02");
        }

        // 如果用戶已停用
        if ($arrPlayerData['status'] == 0) {
            $this->ApiError("03");
        }

        $player_account = $arrPlayerData['account'];
        $player_currency_type = $arrPlayerData['currency_type'];
        $agent_id = $arrPlayerData['agent_id'];
        $player_balance = $arrPlayerData['balance'];

        // 判斷餘額是否足夠下注
        if ($player_balance < $bet_amount) {
            $this->ApiError("04");
        }

        // 判斷下注額度是否超過限額
        // ...
        
        // 取得商戶資料
        $arrAgentData = Agent::where("id", $agent_id)
            ->first();
        if ($arrAgentData == false) {
            $this->ApiError("05");
        }

        // 如果商戶已停用
        if ($arrAgentData['status'] == 0) {
            $this->ApiError("06");
        }

        $agent_account = $arrAgentData['account'];

        // 取第一筆注單ID做為串關注單ID
        $m_order_id = false;

        // 取第一筆串關注單的 sport_id
        //用來檢查串關注單是否都是同球種
        $m_sport_id = false;

        // 串關批量處理訂單
        foreach ($arrBetData AS $bk => $bet) {
            // 取得必要參數
            $fixture_id = $bet['fixture_id'];
            $market_id = $bet['market_id'];  
            $market_bet_id = $bet['market_bet_id'];
            $player_rate = $bet['bet_rate'];  //前端傳來的賠率

            $columns = array(
                "fixture_id", "market_id", "market_bet_id", "bet_rate"
            );
    
            foreach ($columns as $k => $v) {
                if (!isset($bet[$v])) {
                    $this->ApiError("07");
                }
            }

            //////////////////////////////////////////
            // order data
            $order = array(
                'player_id' => $player_id,
                'player_name' => $player_account,
                'currency_type' => $player_currency_type,
                'agent_id' => $agent_id,
                'agent_name' => $agent_account,
                'sport_id' => null,
                'league_id' => null,
                'league_name' => null,
                'fixture_id' => null,
                'home_team_id' => null,
                'home_team_name' => null,
                'away_team_id' => null,
                'away_team_name' => null,
                'market_id' => null,
                'market_bet_id' => null,
                'market_bet_line' => null,
                'market_name' => null,
                'market_bet_name' => null,
                'market_priority' => null,
                'bet_rate' => null,
                'player_rate' => null,
                'better_rate' => null,
                'bet_amount' => $bet_amount,
                'status' => $default_order_status,
                'create_time' => date("Y-m-d H:i:s"),
                'approval_time' => $default_approval_time,
            );

            /////////////////////////////

            // 取得賽事資料
            $arrFixtureData = LsportFixture::where("fixture_id", $fixture_id)->where("sport_id", $sport_id)->first();
            if ($arrFixtureData == false) {
                $this->ApiError("13");
            }

            // 判斷注單 是否為同一sport_id
            if ($m_sport_id === false) {
                $m_sport_id = $arrFixtureData['sport_id'];
            }

            //串關注單全部的sport_id都要一樣 (不能跨球種)
            if ($m_sport_id != $arrFixtureData['sport_id']) {
                $this->ApiError("14");
            } else {
                //////////////////////////////////////////
                // order data
                $order['sport_id'] = $arrFixtureData['sport_id'];
            }

            //fixture status : 1未开始、2进行中 
            // 串關只能賽前注單,不得是走地滾球
            if ($arrFixtureData['status'] != 1) {
                $this->ApiError("15");
            }
    
            $league_id = $arrFixtureData['league_id'];
            $home_id = $arrFixtureData['home_id']; 
            $away_id = $arrFixtureData['away_id']; 
    
            // 取得聯盟
            $arrLeagueData = LsportLeague::where("league_id", $league_id)->first();
            if ($arrLeagueData['status'] != 1) {
                $this->ApiError("10");
            }

            //////////////////////////////////////////
            // order data
            $order['league_id'] = $league_id;
            $order['league_name'] = $arrLeagueData[$langCol];
            if (!strlen($arrLeagueData[$langCol])) {
                $order['league_name'] = $arrLeagueData['name_en'];
            }
            $order['fixture_id'] = $fixture_id;

            //////////////////////////////////////////
            // 取得隊伍資料
            // 主隊
            $homeTeamData = LsportTeam::where("team_id",$home_id)->first();
            if ($homeTeamData === false) {
                $this->ApiError("11");
            }
            // order data
            $order['home_team_id'] = $home_id;
            $order['home_team_name'] = $homeTeamData[$langCol];
            // 語系
            if (!strlen($homeTeamData[$langCol])) {
                $order['home_team_name'] = $homeTeamData['name_en'];
            }
            
            // 客隊
            $awayTeamData = LsportTeam::where("team_id", $away_id)->first();
            if ($awayTeamData === false) {
                $this->ApiError("12");
            }
            // order data
            $order['away_team_id'] = $away_id;
            $order['away_team_name'] = $awayTeamData[$langCol];
            // 語系
            if (!strlen($awayTeamData[$langCol])) {
                $order['away_team_name'] = $awayTeamData['name_en'];
            }

            //////////////////////////////////////////
            // 取得玩法
            $market_data = LSportMarket::where("market_id", $market_id)->where("fixture_id",$fixture_id)->first();
            if ($market_data === false) {
                $this->ApiError("13");
            }
            $market_priority = $market_data['priority'];

            // 取得賠率
            $marketBetData = LSportMarketBet::where("fixture_id", $fixture_id)->where("bet_id",$market_bet_id)->first();
            if ($marketBetData === false) {
              $this->ApiError("14");
            }
            $current_market_bet_status = $marketBetData['status'];
            $current_market_bet_rate = $marketBetData['price'];
            $market_bet_line = $marketBetData['base_line'];

            // 非開盤狀態 1开、2锁、3结算
            if (($current_market_bet_status != 1)) {
                $this->ApiError("15");
            }

            //////////////////////////////////////////
            // order data

            $order['m_order'] = 1;  //1=屬於串關注單
            $order['market_id'] = $market_id;
            $order['market_bet_id'] = $market_bet_id;
            $order['market_bet_line'] = $market_bet_line;
        
            $order['market_name'] = $market_data[$langCol];
            $order['market_bet_name'] = $marketBetData[$langCol];
            $order['market_priority'] = $market_priority;
            $order['bet_rate'] = $current_market_bet_rate;
            
            $order['player_rate'] = $player_rate;
            $order['better_rate'] = $is_better_rate;
            
            //////////////////////////////////////////
            // 判斷 is_better_rate
            if (($is_better_rate == 1) && ($current_market_bet_rate < $player_rate)) {
                $this->ApiError("16");
            }

            //////////////////////////////////////////
            //

            if ($m_order_id !== false) { 
                $order['m_id'] = $m_order_id;  // 同一串關注單m_id均相同(第一筆寫入的注單ID)
            }

            //////////////////////////////////////////
            // 新增注單
            $newOrderId = GameOrder::insertGetId($order);      
            if ($newOrderId == false) {
                $this->ApiError("18");
            }

            // 若是第一筆注單設定m_id
            if ($m_order_id === false) {
                $m_order_id = $newOrderId;
                //更新第一筆注單的m_id = 自己的id
                $return = GameOrder::where("id", $m_order_id)
                    ->update([
                        "m_id" => $m_order_id
                    ]);
                if ($return == false) {
                    $this->ApiError("19");
                }
            } 
               
        }
      
        //////////////////////////////////////////

        // 扣款
        $before_amount = $player_balance;
        $change_amount = $bet_amount;
        $after_amount = $before_amount - $change_amount;

        $return = Player::where("id", $player_id)->update([
            "balance" => $after_amount
        ]);      
        if ($return == false) {
            $this->ApiError("20");
        }
        
        // 帳變
        $tmp = array();
        $tmp['agent_id'] = $agent_id;
        $tmp['player_id'] = $player_id;
        $tmp['player'] = $player_account;
        $tmp['currency_type'] = $player_currency_type;
        $tmp['type'] = "game_bet";
        $tmp['change_balance'] = $change_amount;
        $tmp['before_balance'] = $before_amount;
        $tmp['after_balance'] = $after_amount;
        $tmp['create_time'] = date("Y-m-d H:i:s");
        PlayerBalanceLogs::insert($tmp);

        $this->ApiSuccess($return, "01");

    }

    /**
     * ResultIndex
     * 
     * 取得特定球種的賽事狀態(球隊,比賽結果等)，不含玩法及賠率。
     * Get game conditions of a specified sport ID. Ex. teams, results, etc.
     *
     * @param Request $request: 前端傳入的使用者請求。User requests passed in from the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     *                          # sport: 球種的ID，未指定時為1 (足球)。The specified sport ID. Value = 1 (soccer) when not specified.
     *                          # page: 頁次，未指定時為1。The specified page number. Value = 1 when not specified.
     * @return ApiSuccess($data = ARRAY 指定球種的賽事狀態列表) | ApiError
     */
    // 賽事結果 
    public function ResultIndex(Request $request) {
      
    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        /////////////////////////
        // 取得語系
        $langCol = 'name_' . $this->agent_lang;

        /////////////////////////
        // 輸入判定
        if (!isset($input['sport']) || ($input['sport'] == "")) {
            $input['sport'] = DEFAULT_SPORT_ID;  // 預設1 , 足球
        }

        if (!isset($input['page']) || ($input['page'] == "")) {
            $input['page'] = 1; // 預設1 
        }

    	/////////////////////////
        // Search 區用
        $sport_id = $input['sport'];
        $page = $input['page'];
        
        /////////////////////////
        // 狀態
        $fixture_status = array(
             1 => "等待開賽",
             2 => "進行中",
             3 => "已結束",
             4 => "取消",
             5 => "延期",
             6 => "中斷",
             7 => "放棄",
             8 => "失去覆蓋",
             9 => "即將開始"
        );

        /////////////////////////
        // 分頁 
        $page_limit = $this->page_limit;
        $skip = ($page-1)*$page_limit;

        /////////////////////////
        // 取得比賽資料
        $return = LsportFixture::where("status",">=",3)->where("sport_id",$sport_id)->orderBy("start_time","DESC")->get();
        if ($return === false) {
            $this->ApiError('02');
        }

        $fixture_data = $return;

        $reponse = array();
        foreach ($fixture_data as $k => $v) {

            $tmp = array();
            
            $tmp['fixture_id']  = $v['fixture_id'];
            $tmp['start_time']  = $v['start_time'];
            $tmp['status']      = $fixture_status[$v['status']];
            $tmp['last_update'] = $v['last_update'];

            ///////////////////////

            // 語系套用
            // league_name: 
            $league_id = $v['league_id'];
            $return = LsportLeague::where("league_id",$league_id)->first();
            if ($return === false) {
                $this->ApiError('03');
            }
            $tmp['league_id'] = $league_id;
            $tmp['league_name'] = $return[$langCol];

            // sport_name: 
            $sport_id = $v['sport_id'];
            $return = LsportSport::where("sport_id",$sport_id)->first();
            if ($return === false) {
                $this->ApiError('04');
            }
            $tmp['sport_id'] = $sport_id;
            $tmp['sport_name'] = $return[$langCol];

            // home_team_name: 
            $team_id = $v['home_id'];
            $return = LsportTeam::where("team_id",$team_id)->first();
            if ($return === false) {
                $this->ApiError('05');
            }
            $tmp['home_team_id'] = $team_id;
            $tmp['home_team_name'] = $return[$langCol];

            // away_team_name: 
            $team_id = $v['away_id'];
            $return = LsportTeam::where("team_id",$team_id)->first();
            if ($return === false) {
                $this->ApiError('05');
            }
            $tmp['away_team_id'] = $team_id;
            $tmp['away_team_name'] = $return[$langCol];

            //////////////

            $scoreboard = array();

            // 總分
            $json = json_decode($v['scoreboard'],true);
            $json = (array)$json;

            if (count($json) > 0) {
                $d = array();
                foreach ($json['Results'] as $kk => $vv) {
                    $pos = $vv['Position']-1;
                    $d[$pos] = $vv['Value'];
                }
                $scoreboard[] = $d;
            }

            // 局數 
            $d = array();
            $json = json_decode($v['periods'],true);
            $json = (array)$json;
            if (count($json) > 0) {
                foreach ($json as $kk => $vv) {
                    $d = array();
                    foreach ($vv['Results'] as $kkk => $vvv) {
                        $pos = $vvv['Position']-1;
                        $d[$pos] = $vvv['Value'];
                    }
                    $type = $vv['Type'];
                    if ($type < 100) {  // 粗暴判斷
                        $scoreboard[] = $d;
                    }
                }
            }

            $tmp['scoreboard'] = $scoreboard;
            ////////////

            $reponse[] = $tmp;
        }

        $data = $reponse;

        // gzip
        $data = $this->gzip($data);

        $this->ApiSuccess("01", $data);
    }


/****************************************
 *    
 *    遊戲頁
 *    
****************************************/

    /**
     * GameIndex
     * 
     * 取得單場賽事的資料。
     *
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     *                          # fixture_id: 指定賽事的ID。The specified fixture ID.
     *                          # sport_id: 指定賽事的球種ID。The sport ID of the specified fixture.
     * @return ApiSuccess($data = ARRAY 指定的單場賽事資料) | ApiError
     */
    // 遊戲頁
    public function GameIndex(Request $request) {

    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        /////////////////////////
        // 語系
        $langCol = 'name_' . $this->agent_lang;

        //////////////////////////////////////////
        if (!empty($input['sport_id'])) {
            $sport_id = $input['sport_id'];
        } else {
            $this->ApiError("01");
        }
        if (($fixture_id+0 != $fixture_id) || ($fixture_id+0 == 0)) {
            $this->ApiError("01");
        }

        if (!empty($input['sport_id'])) {
            $fixture_id = $input['fixture_id'];
        } else {
            $this->ApiError("02");
        }
        if (($sport_id+0 != $sport_id) && ($sport_id+0 == 0)) {
            $this->ApiError("02");
        }

        $return = LsportFixture::where("fixture_id", $fixture_id)
            ->where("sport_id", $sport_id)
            ->get();
        if ($return === false) {
            $this->ApiError("03");
        }
        
        // $tmp = $this->rebuild($return, $this->agent_lang, $sport_id);

        $data = $return;

        /**************************************/
        // gzip
        $data = $this->gzip($data);

        $this->ApiSuccess($data, "01");
    }

    /**
     * CommonOrder
     *
     * 抓取玩家的投注紀錄。
     * 
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     *                          # page: 紀錄的頁次。預設1。
     *                          # result: 記錄類型為已結算(1)或未結算(0)。預設0 (未結算)。
     * @return ApiSuccess($data = ???) | ApiError
     */
    // 下注紀錄
    public function CommonOrder(Request $request) {
      
    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        /////////////////////////
        // 語系
        $langCol = 'name_' . $this->agent_lang;

        //////////////////////////////////////////

        if (!isset($input['page'])) {
            $input['page'] = 1;
        }

        if (!isset($input['result'])) {
            $input['result'] = 0;
        }

        $page_limit = $this->page_limit;
        $page = $input['page'];
        $skip = ($page-1)*$page_limit;

        // 獲取注單資料
        $GameOrder = GameOrder::where("player_id", $input['player']);
        $groupedData = GameOrder::select('m_id')->where("player_id", $input['player']);

        if (isset($input['result']) && ($input['result'] != "")) {
            
            // 未結算
            if ($input['result'] == 0) {
                $GameOrder = $GameOrder->whereIn("status",[0,1,2,3]);
                $groupedData = $groupedData->whereIn("status",[0,1,2,3]);
            }
            
            // 已派獎
            if ($input['result'] == 1) {
                $GameOrder = $GameOrder->where("status",4);
                $groupedData = $groupedData->where("status",4);
            }
        }
            
        $return = $GameOrder->groupBy('m_id')->skip($skip)->take($page_limit)->orderBy('m_id', 'DESC')->get();
        if ($return === false) {
            $this->ApiError("01");
        }

        $order_status = array(
            0 => "已取消",
            1 => "等待審核",
            2 => "等待開獎",
            3 => "等待派獎",
            4 => "已開獎"
        );

        $data = array();
        $tmp = array();

        /////////////////////////

        $columns = array(
            "id",
            "m_id",
            "bet_amount",
            "result_data",
            "result_amount",
            "create_time",
            "result_time",
            "status"
        );

        foreach ($return as $k => $v) {
            foreach ($columns as $kk => $vv) {
                $tmp[$k][$vv] = $v[$vv]; 
            }

            $tmp[$k]['status'] = $order_status[$v['status']];
            $tmp[$k]['m_order'] = $v['m_order'];

            $sport_id = $v["sport_id"];
            $home_team_id = $v["home_team_id"];
            $away_team_id = $v["away_team_id"];

            // 有串關資料
            if ($v['m_order'] == 1) {

                $cc = GameOrder::where("m_id", $v['m_id'])->get();
                if ($cc === false) {
                    $this->error(__CLASS__, __FUNCTION__, "02");
                }

                foreach ($cc as $kkk => $vvv) {
                    $tmp_bet_data = array();

                    // 聯賽
                    $league_id = $vvv['league_id'];
                    $tmp_d = LsportLeague::where("league_id", $league_id)->where("sport_id", $vvv['sport_id'])->first();
                    if ($tmp_d === null) {
                        $tmp_bet_data['league_name'] = $vvv['name_en'];
                    } else {
                        $tmp_bet_data['league_name'] = $tmp_d[$langCol];
                    }
        
                    $type_id = $vvv['type_id'];

                    $tmp_d = LsportMarketBet::where("id", $type_id)->where("sport_id", $vvv['sport_id'])->first();
                    if ($tmp_d === null) {
                        $tmp_bet_data['market_bet_name'] = $vvv['name_en'];
                    } else {
                        $tmp_bet_data['type_name'] = $tmp_d[$langCol];
                    }
        
                    // 主隊
                    $home_team_id = $vvv['home_team_id'];
                    $tmp_d = LsportTeam::where("team_id", $home_team_id)->where("sport_id", $vvv['sport_id'])->first();
                    if ($tmp_d === null) {
                        $tmp_bet_data['home_team_name'] = $vvv['name_en'];
                    } else {
                        $tmp_bet_data['home_team_name'] = $tmp_d[$langCol];
                    }
        
                    // 客隊
                    $away_team_id = $vvv['away_team_id'];
                    $tmp_d = LsportTeam::where("team_id", $away_team_id)->where("sport_id", $vvv['sport_id'])->first();
                    if ($tmp_d === null) {
                        $tmp_bet_data['away_team_name'] = $vvv['away_team_name'];
                    } else {
                        $tmp_bet_data['away_team_name'] = $tmp_d[$langCol];
                    }
        
                    ///////////////

                    $tmp_bet_data['type_item_name'] = $vvv['type_item_name'];
                    $tmp_bet_data['bet_rate'] = $vvv['bet_rate'];
                    $tmp_bet_data['status'] = $arrOrderStatus[$vvv['status']];
                    $tmp_bet_data['type_priority'] = $vvv['type_priority'];
                    

                    $tmp[$k]['bet_data'][] = $tmp_bet_data;
                }
            } else {
                $tmp_bet_data = array();

                // 聯賽
                $league_id = $v['league_id'];
                $tmp_d = LsportLeague::where("league_id", $league_id)->where("sport_id", $v['sport_id'])->first();
                if ($tmp_d === false) {
                    $this->ApiError("01");
                }

                if ($tmp_d === null) {
                    $tmp_bet_data['league_name'] = $v['name_en'];
                } else {
                    $tmp_bet_data['league_name'] = $tmp_d[$langCol];
                }

                $type_id = $v['type_id'];

                $tmp_d = LsportMarketBet::where("id", $type_id)->where("sport_id", $v['sport_id'])->first();
                if ($tmp_d === null) {
                    $tmp_bet_data['type_name'] = $v['type_name'];
                } else {
                    $tmp_bet_data['type_name'] = $tmp_d[$langCol];
                }
                
                $replace_lang = array();

                $home_team_id = $v['home_team_id'];

                $tmp_d = LsportTeam::where("team_id", $home_team_id)->where("sport_id", $v['sport_id'])->first();
                if ($tmp_d === null) {
                    $tmp_bet_data['home_team_name'] = $v['home_team_name'];
                } else {
                    $tmp_bet_data['home_team_name'] = $tmp_d[$langCol];
                }

                $away_team_id = $v['away_team_id'];

                $tmp_d = LsportTeam::where("team_id", $away_team_id)->where("sport_id", $v['sport_id'])->first();
                if ($tmp_d === null) {
                    $tmp_bet_data['away_team_name'] = $v['away_team_name'];
                } else {
                    $tmp_bet_data['away_team_name'] = $tmp_d[$langCol];
                }

                // rate item 顯示轉化
                $item_name = $v['type_item_name']; // 預設
                $replace_lang[] = array("cn" => "单", "tw" => "單");
                $replace_lang[] = array("cn" => "双", "tw" => "雙");
                $tmp_bet_data['type_item_name'] = $item_name;

                $tmp_bet_data['bet_rate'] = $v['bet_rate'];
                $tmp_bet_data['status'] = $arrOrderStatus[$v['status']];
                $tmp_bet_data['type_priority'] = $v['type_priority'];
                $tmp[$k]['bet_data'][] = $tmp_bet_data;


                $tmp[$k]['bet_data'][] = $tmp_bet_data;
            }

        }

        $data['list'] = $tmp;
        ////////////////////////

        // gzip
        $data = $this->gzip($data);

        $this->ApiSuccess($data, "01");

    }

    /**
     * BalanceLogs
     * 
     * 取得當前玩家的帳變紀錄 (balance logs)。
     * The (balance logs)。
     *
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
     *                          # page: 頁次，未指定時為1。The specified page number. Value = 1 when not specified.
     * @return ApiSuccess($data = ARRAY 列表) | ApiError
     */
    public function BalanceLogs(Request $request) {
      
    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        /////////////////////////
        // 取得語系
        $langCol = 'name_' . $this->agent_lang;

        //////////////////////////////////////////

        if (!isset($input['page'])) {
            $input['page'] = 1;
        }

        $page_limit = $this->page_limit;
        $page = $input['page'];
        $skip = ($page-1)*$page_limit;

        // 帳變類型
        $typeList = trans("pc.BalanceLogs_TypeList");

        //////////////////////////
        $player_id = $input['player'];
        $return = PlayerBalanceLogs::where("player_id", $player_id)
            ->skip($skip)
            ->take($page_limit)
            ->orderBy('id', 'DESC')
            ->get();  
        if ($return === false) {
            //$this->error(__CLASS__, __FUNCTION__, "04");
            $this->ApiError("01");
        }

        $list = array();
        foreach ($return as $k => $v) {

            $v['type'] = $typeList[$v['type']];
            $list[] = $v;

        } 

        $data = array();
        $data['list'] = $list;
        ////////////////////////

        // gzip
        $data = $this->gzip($data);

        $this->ApiSuccess($data, "01"); 

    }

    //====================================== PROTECTED methods ======================================

    /**
     * gzip
     * 
     * 壓縮過大過長的資料以利於傳遞
     * Gzipping long and big data for better transmission efficiency
     *
     * @param data - 需要壓縮的陣列資料. Array data to be zipped.
     * @return STR - 已經過gzcompress且base64_encode編碼的資串資料
     */
    protected function gzip($data) {

        $data = json_encode($data, true);
        $compressedData = gzcompress($data);  // 使用 gzcompress() 函數進行壓縮
        $base64Data = base64_encode($compressedData);  // 使用 base64_encode() 函數進行 base64 編碼

        return $base64Data;
    }

    /**
     * compareRateValue
     * 
     * strcmp比較兩陣列的 $key 的元素的值的大小(字串比較)
     *
     * @param a - 比較的陣列
     * @param b - 被比較的陣列
     * @param key - 要比較的元素的鍵值
     * @return INT {
     *       0=兩者相等時。The two params are equal.
     *       1=前者大於後者。The first param is bigger than the second.
     *      -1=前者小於後者。The first param is smaller than the second.
     * }
     */
    private static function compareKeyValue($a, $b, $key) {

        if (isset($key)) {
            return strcmp($a[$key], $b[$key]);
        }
        return null;
    }

    /**
     * customExplode
     * 
     * 依據傳入字串的三種情況:含空白、含+、含，回傳一陣列，分別包含鍵值filter=0或1或2，及鍵值value=已被拆分(explode)為陣列。
     *
     * @param str - 要被拆分的字串
     * @return ARRAY{filter:0或1或2，value:拆分的字串的陣列}
     */
    protected function customExplode($str) {

        $data = array();
        if (strpos($str, ' ') !== false) {  // 字串包含空白，以空白符分割
            $data['filter'] = 0;
            $tmp = explode(' ', $str);
            $data['value'] = $tmp;
        } elseif (strpos($str, '+') !== false) {// 以+符分割
            $data['filter'] = 1;
            $tmp = explode('+', $str);
            $data['value'] = $tmp;
        } elseif (strpos($str, '-') !== false) {// 以-符分割
            $data['filter'] = 2;
            $tmp = explode('-', $str);
            $data['value'] = $tmp;
        }

        return $data;
    }

    /**
     * ApiSuccess
     * 
     * 回傳予前端表示後端對前端請求的操作成功，以及所請求的結果。
     *
     * @param data - 請求的結果。
     * @param message -  操作成功訊息。
     * @param gzip -  {true | false, 預設值=false。} 參數data是否已經過gzip壓縮及base64編碼處理。
     * @return JSON {
     *      status = 狀態(恆為1),
     *      data = 欲回傳給前端的資料,
     *      message = 經過拼裝可給前端翻譯顯示的包含控制器、方法、訊息的代碼字串。
     *      gzip = BOOL: 參數data是否已經過gzip壓縮及base64編碼處理。
     * }
     */
    protected function ApiSuccess($data, $message, $is_gzip = false) {

        $success_code = strtoupper("SUCCESS_" . $this->controller . "_" . $this->function . "_" . $message);

        $tmp = array();
        $tmp['status'] = 1;
        $tmp['data'] = $data;
        $tmp['message'] = $success_code;
        $tmp['gzip'] = ($is_gzip == true);
        
        echo json_encode($tmp, true);
        exit();
    }

    /**
     * ApiError
     *
     * 回傳予前端表示後端對前端請求的操作失敗，以及失敗訊息。
     *
     * @param $message = 經過拼裝可給前端翻譯顯示的包含控制器、方法、訊息的代碼字串。
     * @param $is_common = 是否為通用錯誤,
     * @param $gzip = BOOL: 參數data是否已經過gzip壓縮及base64編碼處理。實際上沒有作用
     * @return JSON {
     *      status = 狀態(恆為0),
     *      data = 欲回傳給前端的資料(恆為null),
     *      message = 經過拼裝可給前端翻譯顯示的包含控制器、方法、訊息的代碼字串,
     *      gzip = BOOL: 參數data是否已經過gzip壓縮及base64編碼處理。
     * }
     */
    protected function ApiError($message , $is_common = false, $is_gzip = false) {

        if (! $is_common) {  // CLASS_FUNCTION ONLY
            $error_code = strtoupper("ERROR_" . $this->controller . "_" . $this->function . "_" . $message);
        } else {  // 通用錯誤類
            $error_code = strtoupper("ERROR_COMMON_" . $message);
        }      
        
        $tmp = array();
        $tmp['status'] = 0;
        $tmp['data'] = null;
        $tmp['message'] = $error_code;
        $tmp['gzip'] = ($is_gzip == true);
        
        echo json_encode($tmp, true);
        exit();
    }

    /**
     * checkToken
     *
     * 檢查玩家是否存在於PlayerOnline DB資料表中。
     * Checks if the player exists in the 'PlayerOnline' DB table.
     * 
     * @param $input ARRAY 包含玩家ID(key=player)及其token(key=token)的陣列
     * @return BOOL {true | false}
     */
    protected function checkToken($input) {
      
        $player_id = $input['player'];
        $token = $input['token'];
        
        $checkToken = PlayerOnline::where("player_id", $player_id)
            ->where("token", $token)
            ->where("status", 1)
            ->count();

        if ($checkToken) {
            //---------------------------------
            // 取得代理的語系
            $agentlang = $this->getAgentLang($player_id);
            if ($agentlang === false) {
                //$this->error(__CLASS__, __FUNCTION__, "02");
                $agentlang = 'en';
            }
            $this->agent_lang = $agentlang;
            //---------------------------------
            return true;
        }

        return false;
    }

    /**
     * getMatchScoreboard
     *
     * 解析並回傳一場賽事的各局主客隊得分資料。應為走地中賽事。
     * 
     * @param 
     * @return  # null
     *          # false
     *          # ARRAY
     */

    public function getMatchScoreboard_caller(Request $request) {

        $return = $this->getMatchScoreboard(
            $request['sport_id'],
            $request['fixture_status'],
            $request['periods_raw_data'],
            $request['scoreboard_raw_data']
        );
        echo json_encode(['ret' => $return]);
    }

    protected function getMatchScoreboard($sport_id, $fixture_status, $periods_raw_data, $scoreboard_raw_data) {

        // 如果還未開賽就回傳null
        if ($fixture_status < 2) {
            return null;
        }

        // 目前只處理棒球
        if ($sport_id != DEFAULT_SPORT_ID) {
            return null;
        }

        //========================================

        if (is_array($periods_raw_data)) {
            $arr_periods_raw_data = $periods_raw_data;
        } else {
            // 如果參數是字串則json_decoe看看
            $arr_periods_raw_data = json_decode($periods_raw_data, true);
            // de不出東西就回傳false
            if (!$arr_periods_raw_data) {
                return false;
            }
        }

        if (is_array($scoreboard_raw_data)) {
            $arr_scoreboard_raw_data = $scoreboard_raw_data;
        } else {
            // 如果參數是字串則json_decoe看看
            $arr_scoreboard_raw_data = json_decode($scoreboard_raw_data, true);
            // de不出東西就回傳false
            if (!$arr_scoreboard_raw_data) {
                return false;
            }
        }

        //========================================

/*
periods:
[
    {
      "Incidents": null,
      "IsConfirmed": true,
      "IsFinished": true,
      "Results": [
        {
          "Position": "1",
          "Value": "0"
        },
        {
          "Position": "2",
          "Value": "0"
        }
      ],
      "SequenceNumber": 1,
      "SubPeriods": null,
      "Type": 1
    },
    {
      "Incidents": [
        {
          "IncidentType": 27,
          "ParticipantPosition": "1",
          "Period": 2,
          "Results": [
            {
              "Position": "1",
              "Value": "2"
            },
            {
              "Position": "2",
              "Value": "0"
            }
          ],
          "Seconds": -1
        }
      ],
      "IsConfirmed": true,
      "IsFinished": true,
      "Results": [
        {
          "Position": "1",
          "Value": "2"
        },
        {
          "Position": "2",
          "Value": "0"
        }
      ],
      "SequenceNumber": 2,
      "SubPeriods": null,
      "Type": 2
    },

scoreboard:
{
    "CurrentPeriod": 40,
    "Results": [
      {
        "Position": "1",
        "Value": "2"
      },
      {
        "Position": "2",
        "Value": "2"
      }
    ],
    "Status": 2,
    "Time": "-1"
}

*/

        $ret = [];

        // 處理總分
        $arr_results = $arr_scoreboard_raw_data['Results'];
        foreach ($arr_results as $rk => $rv) {
            $pos = intval($rv['Position']);
            $total_score = intval($rv['Value']);
            //總分都是放在key=0的位置
            $ret[$pos][0] = $total_score;
        }

        // 各局得分
        foreach ($arr_periods_raw_data as $pk => $pv) {
            $type = intval($pv['Type']);  // type=局數號碼
            $arr_results = $pv['Results'];
            foreach ($arr_results as $rk => $rv) {
                $pos = intval($rv['Position']);
                $score = intval($rv['Value']);
                $ret[$pos][$type] = $score;
            }
        }

        return $ret;

    }

    /**
     * getMatchScoreboard
     *
     * 解析並回傳一場賽事的比分板資料。應為走地中賽事。
     * 
     * @param 
     * @return 
     */
    public function getMatchPeriods_caller(Request $request) {

        $return = $this->getMatchPeriods(
            $request['sport_id'],
            $request['fixture_status'],
            $request['periods_raw_data']
        );
        echo json_encode(['ret' => $return]);
    }

     protected function getMatchPeriods($sport_id, $fixture_status, $scoreboard_raw_data) {

        // 除了走地中賽事其餘均回傳null
        if ($fixture_status != 2) {
            return null;
        }

        // 目前只處理棒球
        if ($sport_id != DEFAULT_SPORT_ID) {
            return null;
        }

    }

}

