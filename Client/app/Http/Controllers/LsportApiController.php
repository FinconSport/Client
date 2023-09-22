<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;

use App\Models\GameMatch;
use App\Models\GameResult;
use App\Models\GameOrder;

//LSport
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
use Exception;

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

        $data = array();
        $data['account'] = $return['account'];
        $data['balance'] = $return['balance'];
        
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

        //---------------------------------
        // DB取出有效的賽事結果
        $return = GameResult::where("status", 1)->get();
        if ($return === false) {
          $this->ApiError("01");
        }

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
        foreach ($return as $k => $v) {
            $tmp = array();
            foreach ($arrColsToReturn as $key => $val) {
                $tmp[$val] = $v[$val];
            }

            // 日期格式重整
            $tmp['match_time'] = date('Y-m-d H:i:s', $tmp['match_time']);
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
                        ->selectRaw('lsport_fixture.sport_id, COUNT(DISTINCT lsport_fixture.id) AS count,COUNT(*) AS rate_count')
                        //->where('lsport_market_bet.is_active', '=', 1)
                        ->where('lsport_fixture.status', 2)
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
                    ->selectRaw('lsport_fixture.sport_id, COUNT(DISTINCT lsport_fixture.id) AS count,COUNT(*) AS rate_count')
                    //->where('lsport_market_bet.is_active', '=', 1)
                    ->where('lsport_fixture.status', 1)
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
        $arrLsportSports = LsportSport::where('status', 1)
            ->select(
                'sport_id', 'name_en AS name_en', $langCol.' AS name_locale', 'status'
            )
            ->orderBy('id', 'ASC')
            ->get();

        if ($arrLsportSports === false) {
            $this->ApiError("01");
        }

        $arrAllSports = array();
        foreach ($arrLsportSports as $dk => $dv) {

            // sport_name: 判斷用戶語系資料是否為空,若是則用en就好
            if (!strlen($dv->name_locale)) {  // sport name
                $sport_name = $dv->name_en;
            } else {
                $sport_name = $dv->name_locale;
            }

            $arrAllSports[] = array(
                'sport_id' => $dv->sport_id,
                'name' => $sport_name,
                //'status' => $dv->status,
            );
        }

        $this->ApiSuccess($arrAllSports, "01"); 

    }

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

        // 新的LIST
        $data = array();
        $today = time();
        $after_tomorrow = $today + 2 * 24 * 60 * 60; 
        $after_tomorrow = date('Y-m-d 00:00:00', $after_tomorrow); 

        //////////////////////////////
        // 早盤

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
            ->where('l.status', 1)
            ->where('l.sport_id', $sport_id)
            ->where('s.sport_id', $sport_id)
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

        $arrLeagues = array();  //儲存league-fixture-market的階層資料
        //$arrFixtureAndMarkets = array();  //將用於稍後SQL查詢market_bet資料
        $sport_name = '';  //儲存球種名稱


/*
{
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
   }
}
*/

        foreach ($data as $dk => $dv) {
            $league_id = $dv->league_id;
            $fixture_id = $dv->fixture_id;
            $market_id = $dv->market_id;
            $main_line = $dv->main_line;

            // sport_name: 判斷用戶語系資料是否為空,若是則用en就好
            if (!strlen($sport_name)) {  //只須設定一次
                if (!strlen($dv->s_name_locale)) {  // sport name
                    $sport_name = $dv->s_name_en;
                } else {
                    $sport_name = $dv->s_name_locale;
                }
            }

            //儲存 fixture_id, market_id 及 main_line
            // $arrFixtureAndMarkets["{$fixture_id}|{$market_id}|{$main_line}"] = array(
            //     'fixture_id' => $fixture_id,
            //     'market_id' => $market_id,
            //     'main_line' => $main_line,
            //     //'market_name' => $dv->m_name_en,
            // );

            // league 層
            if (!isset($arrLeagues[$league_id]) || !sizeof($arrLeagues[$league_id])) {

                // league_name: 判斷用戶語系資料是否為空,若是則用en就好
                if (!strlen($dv->l_name_locale)) {  // league name
                    $league_name = $dv->l_name_en;
                } else {
                    $league_name = $dv->l_name_locale;
                }

                // 包入 league 聯賽資料
                $arrLeagues[$league_id] = array(
                    'league_id' => $dv->league_id,
                    'league_name' => $league_name,
                    'list' => array(),
                );
            }

            // fixture 層
            if (!isset($arrLeagues[$league_id]['list'][$fixture_id]) || !sizeof($arrLeagues[$league_id]['list'][$fixture_id])) {

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

                // 包入 fixture 賽事資料
                $arrLeagues[$league_id]['list'][$fixture_id] = array(
                    //'sport_id' => $dv->sport_id,
                    //'league_id' => $dv->league_id,
                    //'home_id' => $dv->home_id,
                    //'away_id' => $dv->away_id,
                    'fixture_id' => $dv->fixture_id,
                    'start_time' => $dv->start_time,
                    //'livescore_extradata' => $dv->livescore_extradata,  // 此階段先不給
                    //'periods' => $dv->periods,  // 此階段先不給
                    //'scoreboard' => $dv->scoreboard,  // 此階段先不給
                    'status' => $dv->f_status,
                    'last_update' => $dv->f_last_update,
                    'home_team_id' => $dv->th_team_id,
                    'home_team_name' => $home_team_name,
                    'away_team_id' => $dv->ta_team_id,
                    'away_team_name' => $away_team_name,
                    'list' => array(),
                );
            }

            //market 層
            if (!isset($arrLeagues[$league_id]['list'][$fixture_id]['list'][$market_id]) ||
                !sizeof($arrLeagues[$league_id]['list'][$fixture_id]['list'][$market_id])) {

                // market_name: 判斷用戶語系資料是否為空,若是則用en就好
                if (!strlen($dv->m_name_locale)) {  // market name
                    $market_name = $dv->m_name_en;
                } else {
                    $market_name = $dv->m_name_locale;
                }

                // 包入 market 玩法資料
                $arrLeagues[$league_id]['list'][$fixture_id]['list'][$market_id] = array(
                    'market_id' => $dv->market_id,
                    'market_name' => $market_name,
                    'priority' => $dv->priority,
                    'main_line' => $dv->main_line,
                    'list' => array(),
                );

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
                ->orderBy('mb.bet_id', 'ASC')
                ->get();

                //dd($marketBetData);

                if ($marketBetData === false) {
                    $this->ApiError('03');
                }

                foreach ($marketBetData as $bk => $bv) {
                    $market_bet_id = $bv->bet_id;

                    // market_bet_name: 判斷用戶語系資料是否為空,若是則用en就好
                    if (isset($bv->mb_name_locale)) {  // market name
                        $market_bet_name = $bv->mb_name_en;
                    } else {
                        $market_bet_name = $bv->mb_name_locale;
                    }

                    $arrLeagues[$league_id]['list'][$fixture_id]['list'][$market_id]['list'][$market_bet_id] = array(
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

        $arrRet = array();  //用於回傳結果
        $arrRet['early'][$sport_id] = array(  //目前都只回傳early 早盤
            'sport_id' => $sport_id,
            'sport_name' => $sport_name,
            'list' => $arrLeagues,
        );
        $arrRet['living'] = array();  //living 走地目前都設空

        $data = $arrRet;

        ///////////////////////////////
        // gzip
        $data = $this->gzip($data);

        $this->ApiSuccess($data, "01", true);
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

        $league_id = $return['league_id'];
        $home_id = $return['home_id']; 
        $away_id = $return['away_id']; 

        // 取得聯盟
        $league_data = LsportLeague::where("league_id",$league_id)->first();
        if ($league_data['status'] != 1) {
          $this->ApiError("10");
      }
        //////////////////////////////////////////
        // order data
        $order['league_id'] = $league_id;
        $order['league_name'] = $league_data[$name_columns]; 
        $order['fixture_id'] = $fixture_id;
        $order['sport_id'] = $league_data['sport_id'];
        //////////////////////////////////////////

        // 取得隊伍資料

        // 主
        $team_data = LsportTeam::where("team_id",$home_id)->first();
        if ($team_data === false) {
          $this->ApiError("11");
        }
        $order['home_team_id'] = $home_id;
        $order['home_team_name'] = $team_data[$name_columns];
        
        // 客
        $team_data = LsportTeam::where("team_id",$away_id)->first();
        if ($team_data === false) {
          $this->ApiError("12");
        }

        $order['away_team_id'] = $away_id;
        $order['away_team_name'] = $team_data[$name_columns];

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

        $arrEssentialOrderCols = array(  // 必要的注單欄位
            'player_id',
            'player_name',
            'currency_type',
            'agent_id',
            'agent_name',
            'league_id',
            'league_name',
            'fixture_id',
            'sport_id',
            'home_team_id',
            'home_team_name',
            'away_team_id',
            'away_team_name',
            'market_id',
            'market_bet_id',
            'market_bet_line',
            'market_name',
            'market_bet_name',
            'market_priority',
            'bet_rate',
            'player_rate',
            'better_rate',
            'bet_amount',
            'status',
            'create_time',
            'approval_time',
        );

        /**
前端傳來:
    player,
    sport_id,
    bet_amount,
    better_rate,
    bet_data: [
        fixture_id,
        market_id,
        market_bet_id,
        bet_rate,
    ]
         */

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

        $bet_data = json_decode($input['bet_data'], true);

        $order = array();
        
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

        // 取第一筆串關注單做為串關id 
        $m_order_id = false;

        // 取第一筆串關注單的game_id
        $m_game_id = false;

        // 串關批量處理訂單
        foreach ($bet_data AS $k => $v) {
            // 取得必要參數
            $fixture_id = $v['bet_match'];
            $bet_type_id = $v['bet_type'];
            $bet_type_item_id = $v['bet_type_item'];
            $player_rate = $v['bet_rate'];
            $bet_amount = $input['bet_amount'];
            $is_better_rate = $input['better_rate'];

            $order = array();
        
            // 參數檢查 TODO - 初步 隨便弄弄
            if ($bet_amount <= 0) {
                $this->ApiError("07");
            }
    
            // // 取得用戶資料
            // $return = Player::where("id", $player_id)->first();
            // if ($return == false) {
            //     $this->ApiError("08");
            // }
    
            // // 如果用戶已停用
            // if ($return['status'] == 0) {
            //     $this->ApiError("09");
            // }
    
            // $player_account = $return['account'];
            // $currency_type = $return['currency_type'];
            // $agent_id = $return['agent_id'];
            // $player_balance = $return['balance'];
    
            // // 判斷餘額是否足夠下注
            // if ($player_balance < $bet_amount) {
            //     $this->ApiError("10");
            // }
            
            //////////////////////////////////////////
            // order data
            // $order['player_id'] = $player_id;
            // $order['player_name'] = $player_account;
            // $order['currency_type'] = $currency_type;
            //////////////////////////////////////////
    
            // 取得商戶資料
            $return = Agent::where("id", $agent_id)->first();
            if ($return == false) {
                $this->ApiError("11");
            }
    
            // 如果商戶已停用
            if ($return['status'] == 0) {
                $this->ApiError("12");
            }
    
            $agent_account = $return['account'];
    
            //////////////////////////////////////////
            // order data
            $order['agent_id'] = $agent_id;
            $order['agent_name'] = $agent_account;
            //////////////////////////////////////////
    
            // 取得賽事資料
            $arrFixtures = LsportFixture::where("fixture_id", $fixture_id)->where("sport_id", $sport_id)->first();
            if ($arrFixtures == false) {
                $this->ApiError("13");
            }
    
            // 判斷注單 是否為同一game_id
            if ($m_game_id === false) {
                $m_game_id = $arrFixtures['sport_id'];
            } else {
                if ($m_game_id != $arrFixtures['sport_id']) {
                    $this->ApiError("14");
                }
            }

            //match status : 1未开始、2进行中、3已结束、4延期、5中断、99取消
            // 串關只能賽前注單
            if ($arrFixtures['status'] != 1) {
                $this->ApiError("15");
            }
    
            // decode 聯盟
            $series_data = json_decode($arrFixtures['league'], true);
            //////////////////////////////////////////
            // order data
            $order['league_id'] = $series_data['league_id'];
            $order['league_name'] = $series_data['name_cn'];    // 'name_' . $this->agent_lang;
            $order['fixture_id'] = $fixture_id;
            $order['sport_id'] = $arrFixtures['sport_id'];
            //////////////////////////////////////////
    
            // decode 隊伍
            $teams_data = json_decode($arrFixtures['teams'], true);

            //////////////////////////////////////////
            // order data
            if ($teams_data[0]['index'] == 1) {
                $order['home_team_id'] = $teams_data[0]['team']['id'];
                $order['home_team_name'] = $teams_data[0]['team']['name_cn'];    // 'name_' . $this->agent_lang;
            } else {
                $order['away_team_id'] = $teams_data[0]['team']['id'];
                $order['away_team_name'] = $teams_data[0]['team']['name_cn'];    // 'name_' . $this->agent_lang;
            }
            
            if ($teams_data[1]['index'] == 1) {
                $order['home_team_id'] = $teams_data[1]['team']['id'];
                $order['home_team_name'] = $teams_data[1]['team']['name_cn'];    // 'name_' . $this->agent_lang;
            } else {
                $order['away_team_id'] = $teams_data[1]['team']['id'];
                $order['away_team_name'] = $teams_data[1]['team']['name_cn'];    // 'name_' . $this->agent_lang;
            }
            //////////////////////////////////////////
    
            // 取得賠率
            $arrOdds = LsportMarketBet::where("id", $bet_type_id)->where("fixture_id", $fixture_id)->first();
            if ($arrOdds === false) {
                $this->ApiError("16");
            }
            
            $rate_status = $arrOdds['status'];
            $type_priority = $arrOdds['game_priority'];

    
            // decode 賠率
            $rate_data = json_decode($arrOdds['items'], true);
    
            foreach ($rate_data AS $k => $v) {
                if ($v['id'] == $bet_type_item_id) {
                    $rate_data = $v;
                }
            }
            $current_rate = $rate_data['rate'];
            $current_rate_status = $rate_data['status'];
    
            // 非開盤狀態 1开、2锁、4封、5结算、99取消
            if (($rate_status != 1) || ($current_rate_status != 1)) {
                $this->ApiError("21");
            }
    
            //////////////////////////////////////////
            // order data
            if ($m_order_id !== false) {
                $order['m_id'] = $m_order_id;
            }
            $order['m_order'] = 1;
            $order['type_id'] = $bet_type_id;
            $order['type_item_id'] = $bet_type_item_id;
            $order['type_name'] = $arrOdds['name_cn'];
            $order['type_item_name'] = $rate_data['name_cn'];
            $order['type_priority'] = $type_priority;
            $order['bet_rate'] = $rate_data['rate'];
            
            $order['player_rate'] = $player_rate;
            $order['better_rate'] = $is_better_rate;
            
            //////////////////////////////////////////
    
            // 判斷 is_better_rate
            if (($is_better_rate == 1) && ($current_rate < $player_rate)) {
                $this->ApiError("17");
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
                $this->ApiError("18");
            }

            // 設定串關id , 這是第一筆注單
            if ($m_order_id === false) {
                $m_order_id = $return;
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
        $tmp['currency_type'] = $currency_type;
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
     * 取得特定球種的賽事狀態(球隊,比賽結果等)。
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

        //////////////////////////////////////////
        // 輸入判定
        if (!isset($input['sport']) || ($input['sport'] == "")) {
            $input['sport'] = 1;  // 預設1 , 足球
        }
        $sport_id = $input['sport'];

        if (!isset($input['page']) || ($input['page'] == "")) {
            $input['page'] = 1; // 預設1 
        }
        $page = $input['page'];

    	/////////////////////////
        // Search 區用

        // 狀態
        $arrFixtureStatus = array(
            -1 => "異常",
             1 => "等待開賽",
             2 => "進行中",
             3 => "已結束",
             4 => "延期",
             5 => "中斷",
            99 => "取消"
        );

        /////////////////////////
        $page_limit = $this->page_limit;
        $skip = ($page-1)*$page_limit;

        // 取得比賽資料
        $data = DB::table('lsport_league as l')
            ->join('lsport_sport as s', 'l.sport_id', '=', 's.sport_id')
            ->join('lsport_fixture as f', 'l.league_id', '=', 'f.league_id')
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
                'f.fixture_id', 'f.sport_id', 'f.league_id', 'f.start_time', 'f.livescore_extradata', 'f.periods', 'f.scoreboard', 'f.status AS f_status', 'f.last_update AS f_last_update',  //'f.home_id', 'f.away_id',
                'th.team_id AS th_team_id', 'th.name_en AS th_name_en', 'th.'.$langCol.' AS th_name_locale',
                'ta.team_id AS ta_team_id', 'ta.name_en AS ta_name_en', 'ta.'.$langCol.' AS ta_name_locale'
            )
            ->where('l.status', 1)
            ->where('l.sport_id', $sport_id)
            ->where('s.sport_id', $sport_id)
            ->where('f.status', '>=', 2)  //賽事狀態: 進行中(2)或以上
            ->where("th.sport_id", $sport_id)
            ->where("th.sport_id", $sport_id)
            ->orderBy('f.start_time', 'DESC')
            ->skip($skip)
            ->take($page_limit)
            ->get();
            
        $pagination = $data->count();
        
        if ($data === false) {
            $this->ApiError('02');
        }
/*
    "series_name":"\u6cd5\u570b\u7c43\u7403\u7532\u7d1a\u806f\u8cfd",
         "series_logo":"https:\\sporta.asgame.net\uploads\series_219.png?v=1_2_35",
    "id":67140,
    "match_id":293735,
    "game_id":2,
    "series_id":219,
    "start_time":"2023-09-18 01:00:00",
    "end_time":"1970-01-01 08:00:00",
    "status":"\u5df2\u7d50\u675f",
    "stat":[],
    "home_team_name":"\u827e\u65af\u7dad\u723e\u91cc\u6602\u7dad\u52d2\u73ed",
        "home_team_logo":"https:\\sporta.asgame.net\uploads\team_1648.png?v=1_2_35",
    "home_team_score":"89",
    "away_team_name":"\u52d2\u8292\u85a9\u723e\u7279",
        "away_team_logo":"https:\\sporta.asgame.net\uploads\team_3589.png?v=1_2_35",
    "away_team_score":"75"
 */
        $arrRet = array();
        foreach ($data as $dk => $dv) {

            //判斷用戶語系資料是否為空,若是則用en就好
            // league_name: 
            if (!strlen($dv->l_name_locale)) {  // league name
                $league_name = $dv->l_name_en;
            } else {
                $league_name = $dv->l_name_locale;
            }
            // sport_name: 
            if (!strlen($dv->s_name_locale)) {  // sport name
                $sport_name = $dv->s_name_en;
            } else {
                $sport_name = $dv->s_name_locale;
            }
            // home_team_name: 
            if (!strlen($dv->th_name_locale)) {  // sport name
                $home_team_name = $dv->th_name_en;
            } else {
                $home_team_name = $dv->th_name_locale;
            }
            // away_team_name: 
            if (!strlen($dv->ta_name_locale)) {  // sport name
                $away_team_name = $dv->ta_name_en;
            } else {
                $away_team_name = $dv->ta_name_locale;
            }

            $arrTemp = array(
                'fixture_id' => $dv->fixture_id,
                'start_time' => $dv->start_time,
                'status' => $dv->f_status,
                'last_update' => $dv->f_last_update,
                'sport_id' => $dv->sport_id,
                'sport_name' => $sport_name,
                'league_id' => $dv->league_id,
                'league_name' => $league_name,
                'home_team_name' => $home_team_name,
                'away_team_name' => $away_team_name,
            );
            $arrRet[] = $arrTemp;
        }

        // gzip
        $data = $this->gzip($data);

        $this->ajaxSuccess("success_result_index_01", $data);
    }
    // public function ResultIndexOld(Request $request) {
      
    // 	$input = $this->getRequest($request);

    //     $checkToken = $this->checkToken($input);
    //     if ($checkToken === false) {
    //         $this->ApiError("PLAYER_RELOGIN", true);
    //     }

    //     /////////////////////////
    //     // 取得語系
    //     $langCol = 'name_' . $this->agent_lang;

    //     //////////////////////////////////////////
    //     // 輸入判定
    //     if (!isset($input['sport']) || ($input['sport'] == "")) {
    //         $input['sport'] = 1;  // 預設1 , 足球
    //     }
    //     $sport_id = $input['sport'];

    //     if (!isset($input['page']) || ($input['page'] == "")) {
    //         $input['page'] = 1; // 預設1 
    //     }
    //     $page = $input['page'];

    // 	/////////////////////////
    //     // Search 區用

    //     // 狀態
    //     $status = [
    //         -1 => "異常",
    //          1 => "等待開賽",
    //          2 => "進行中",
    //          3 => "已結束",
    //          4 => "延期",
    //          5 => "中斷",
    //         99 => "取消"
    //     ];

    //     /////////////////////////

    //     // 取得比賽資料

    //     $page_limit = $this->page_limit;
    //     $skip = ($page-1)*$page_limit;

    //     // form search
    //     // $AntMatchList = AntMatchList::where("game_id", $sport_id)->where("status", ">=",2);
    //     $LsFixture = LsportFixture::where("sport_id", $sport_id)
    //         ->where("status", ">=",2);

    //     $return = $LsFixture
    //         ->skip($skip)
    //         ->take($page_limit)
    //         ->orderBy('start_time', 'DESC')
    //         ->get();
    //     $pagination = $LsFixture->count();

    //     ////////////////////
    //     $columns = array(
    //         "id",
    //         "fixture_id",
    //         "game_id",
    //         "league_id",
    //         "start_time",
    //         "end_time",
    //         "status"
    //     );

    //     $data = array();
    //     foreach ($return as $k => $v) {

    //         $tmp = array();
            
    //         $series = json_decode($v['league'], true);
    //         $league_id = $series['league_id'];
    //         $sport_id = $series['sport_id'];
    //         // $tmp_logo = AntSeriesList::where("series_id", $series_id)->where("game_id", $sport_id)->where("status",1)->first();
    //         $tmp_logo = LsportLeague::where("league_id", $league_id)
    //             ->where("sport_id", $sport_id)
    //             ->where("status",1)
    //             ->first();
    //         if ($tmp_logo === false) {
    //             $this->ApiError("01");
    //         }
    //         if ($tmp_logo == null) {
    //             continue;
    //         }

    //         $tmp['league_name'] = $tmp_logo[$langCol];
    //         $tmp['series_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'] . "?v=" . $this->system_config['version'];

    //         foreach ($columns as $kk => $vv) {
    //             $tmp[$vv] = $v[$vv]; 
    //         }

    //         // stat
    //         $stat = json_decode($v['stat'], true);
    //         unset($stat['stat']['fixture_id']);
    //         unset($stat['stat']['time']);
    //         if ($v['stat'] == "") {
    //             $tmp['stat'] = [];
    //         } else {
    //             $tmp['stat'] = $stat['stat'];
    //         }

    //         $tmp['status'] = $status[$v['status']];
        
    //         $teams = json_decode($v['teams'], true);

    //         $teams = json_decode($v['teams'], true);

    //         foreach ($teams as $key => $value) {
    //             $team_id = $value['team']['id'];
    //             // $tmp_logo = AntTeamList::where("team_id", $team_id)->where("game_id", $sport_id)->first();
    //             $tmp_logo = LsportTeam::where("team_id", $team_id)
    //                 ->where("sport_id", $sport_id)
    //                 ->first();
    //             if ($tmp_logo === false) {
    //                 $this->error(__CLASS__, __FUNCTION__, "05");
    //             }
                
    //             if ($tmp_logo == null) {
    //                 continue;
    //             }

    //             /////////////////////////////////

    //             $teams[$key]['team']['name'] =  $tmp_logo[$langCol];
    //             $teams[$key]['team']['logo'] =  $this->system_config['image_url'] . $tmp_logo['local_logo'] . "?v=" . $this->system_config['version'];
            
    //         }

    //         foreach ($columns as $kk => $vv) {
    //             $tmp[$vv] = $v[$vv]; 
    //         }
            
    //         $tmp['status'] = $status[$v['status']];
        
    //         foreach ($teams as $kk => $vv) {
    //             if ($vv['index'] == 1) {
    //                 $tmp['home_team_name'] = $vv['team']['name'];
    //                 $tmp['home_team_logo'] = $vv['team']['logo'];
    //                 $tmp['home_team_score'] = $vv['total_score'];
    //             } else {
    //                 $tmp['away_team_name'] = $vv['team']['name'];
    //                 $tmp['away_team_logo'] = $vv['team']['logo'];
    //                 $tmp['away_team_score'] = $vv['total_score'];
    //             }
    //         }

    //         $data[] = $tmp;
    //     }

    //     // gzip
    //     $data = $this->gzip($data);

    //     $this->ajaxSuccess("success_result_index_01", $data);
    // }

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

        $sport_id = $input['sport_id'];
        $fixture_id = $input['fixture_id'];

        if (($fixture_id+0 != $fixture_id) && ($fixture_id+0 == 0)) {
            $this->ApiError("01");
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
        //$data = $this->gzip($data);

        //$this->ApiSuccess($data, "01", true);
        $this->ApiSuccess($data, "01", false);
    }

    /**
     * CommonOrder
     *
     * 抓取玩家的投注紀錄。
     * 
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # *player: 玩家的ID。 Required. Represents the player ID.
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
            
            // 已結算
            if ($input['result'] == 1) {
                $GameOrder = $GameOrder->where("status",4);
                $groupedData = $groupedData->where("status",4);
            }
        }
            

        $return = $GameOrder->groupBy('m_id')->skip($skip)->take($page_limit)->orderBy('m_id', 'DESC')->get();
        if ($return === false) {
            $this->ApiError("01");
        }

        $status_message = array(0=>"已取消",1=>"等待審核",2=>"等待開獎",3=>"等待派獎",4=>"已開獎");
        $data = array();
        $tmp = array();

        //!!!!!!!!!!!!!!!

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

            $tmp[$k]['status'] = $status_message[$v['status']];
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

                    $league_id = $vvv['league_id'];

                    $tmp_d = LsportLeague::where("league_id", $league_id)->where("sport_id", $vvv['sport_id'])->first();
                    if ($tmp_d === null) {
                    $tmp_bet_data['league_name'] = $vvv['league_name'];
                    } else {
                    $tmp_bet_data['league_name'] = $tmp_d[$langCol];
                    }
        
                    $type_id = $vvv['type_id'];

                    $tmp_d = LsportMarketBet::where("id", $type_id)->where("sport_id", $vvv['sport_id'])->first();
                    if ($tmp_d === null) {
                        $tmp_bet_data['type_name'] = $vvv['type_name'];
                    } else {
                        $tmp_bet_data['type_name'] = $tmp_d[$langCol];
                    }
                    
                    $replace_lang = array();
        
                    $home_team_id = $vvv['home_team_id'];

                    $tmp_d = LsportTeam::where("team_id", $home_team_id)->where("sport_id", $vvv['sport_id'])->first();
                    if ($tmp_d === null) {
                        $tmp_bet_data['home_team_name'] = $vvv['home_team_name'];
                    } else {
                        $tmp_bet_data['home_team_name'] = $tmp_d[$langCol];
                        $replace_lang[0]['tw'] = $tmp_d['name_tw'];
                        $replace_lang[0]['cn'] = $tmp_d['name_cn'];
                    }
        
                    $away_team_id = $vvv['away_team_id'];

                    $tmp_d = LsportTeam::where("team_id", $away_team_id)->where("sport_id", $vvv['sport_id'])->first();
                    if ($tmp_d === null) {
                        $tmp_bet_data['away_team_name'] = $vvv['away_team_name'];
                    } else {
                        $tmp_bet_data['away_team_name'] = $tmp_d[$langCol];
                        $replace_lang[1]['tw'] = $tmp_d['name_tw'];
                        $replace_lang[1]['cn'] = $tmp_d['name_cn'];
                    }
        
                    // rate item 顯示轉化
                    $item_name = $vvv['type_item_name']; // 預設
                    $replace_lang[] = array("cn" => "单", "tw" => "單");
                    $replace_lang[] = array("cn" => "双", "tw" => "雙");
                    foreach ($replace_lang as $lang_k => $lang_v) {
                        $item_name = str_replace($lang_v['cn'], $lang_v['tw'], $item_name);
                    }
                    $tmp_bet_data['type_item_name'] = $item_name;

                    $tmp_bet_data['bet_rate'] = $vvv['bet_rate'];
                    $tmp_bet_data['status'] = $status_message[$vvv['status']];
                    $tmp_bet_data['type_priority'] = $vvv['type_priority'];

                    $tmp_bet_data['home_team_logo'] = "";
                    $tmp_bet_data['away_team_logo'] = "";
                    
                    // 取得隊伍logo

                    $tmp_logo = LsportTeam::where("team_id", $home_team_id)->where("sport_id", $sport_id)->first();
                    if (($tmp_logo === false) || ($tmp_logo == null)) {
                        continue;
                    }
                    if (isset($tmp_logo['local_logo'])) {
                        $tmp_bet_data['home_team_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'];
                    }
            

                    $tmp_logo = LsportTeam::where("team_id", $away_team_id)->where("sport_id", $sport_id)->first();
                    if (($tmp_logo === false) || ($tmp_logo == null)) {
                        continue;
                    }
                    if (isset($tmp_logo['local_logo'])) {
                        $tmp_bet_data['away_team_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'];
                    }

                    $tmp[$k]['bet_data'][] = $tmp_bet_data;
                }
            } else {
                $tmp_bet_data = array();

                $league_id = $v['league_id'];

                $tmp_d = LsportLeague::where("league_id", $league_id)->where("sport_id", $v['sport_id'])->first();
                if ($tmp_d === null) {
                    $tmp_bet_data['league_name'] = $v['league_name'];
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
                    $replace_lang[0]['tw'] = $tmp_d['name_tw'];
                    $replace_lang[0]['cn'] = $tmp_d['name_cn'];
                }

                $away_team_id = $v['away_team_id'];

                $tmp_d = LsportTeam::where("team_id", $away_team_id)->where("sport_id", $v['sport_id'])->first();
                if ($tmp_d === null) {
                    $tmp_bet_data['away_team_name'] = $v['away_team_name'];
                } else {
                    $tmp_bet_data['away_team_name'] = $tmp_d[$langCol];
                    $replace_lang[1]['tw'] = $tmp_d['name_tw'];
                    $replace_lang[1]['cn'] = $tmp_d['name_cn'];
                }

                // rate item 顯示轉化
                $item_name = $v['type_item_name']; // 預設
                $replace_lang[] = array("cn" => "单", "tw" => "單");
                $replace_lang[] = array("cn" => "双", "tw" => "雙");
                foreach ($replace_lang as $lang_k => $lang_v) {
                    $item_name = str_replace($lang_v['cn'], $lang_v['tw'], $item_name);
                }
                $tmp_bet_data['type_item_name'] = $item_name;

                $tmp_bet_data['bet_rate'] = $v['bet_rate'];
                $tmp_bet_data['status'] = $status_message[$v['status']];
                $tmp_bet_data['type_priority'] = $v['type_priority'];
                $tmp[$k]['bet_data'][] = $tmp_bet_data;

                $tmp_bet_data['home_team_logo'] = "";
                $tmp_bet_data['away_team_logo'] = "";

                // 取得隊伍logo

                $tmp_logo = LsportTeam::where("team_id", $home_team_id)->where("sport_id", $sport_id)->first();
                if (($tmp_logo === false) || ($tmp_logo == null)) {
                    continue;
                }
                if (isset($tmp_logo['local_logo'])) {
                    $tmp_bet_data['home_team_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'];
                }
        
                $tmp_logo = LsportTeam::where("team_id", $away_team_id)->where("game_id", $sport_id)->first();
                if (($tmp_logo === false) || ($tmp_logo == null)) {
                    continue;
                }
                if (isset($tmp_logo['local_logo'])) {
                    $tmp_bet_data['away_team_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'];
                }

                $tmp[$k]['bet_data'][] = $tmp_bet_data;
            }

        }

        $data['list'] = $tmp;
        ////////////////////////

        // gzip
        $data = $this->gzip($data);

        $this->ApiSuccess($data, "01", true);

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
            $this->error(__CLASS__, __FUNCTION__, "04");
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

        $this->ApiSuccess($data, "01", true); 

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

        //除錯後修正!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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
    protected function ApiSuccess($data, $message, $gzip = false) {


/************************
* /api/v2/match_index
* (LsportApiController::MatchIndex)
* 接口說明
*/
/*
array:2 [▼
"early" => array:1 [▼   // 早盤資料
154914 => array:3 [▼  // key = 球種ID
  "sport_id" => "154914"  // 球種ID
  "sport_name" => "棒球"  // 球種名稱 (自動依據玩家語系抓取,若無資料則會以en版的填入)
  "leagues" => array:4 [▼  // 各聯賽(聯盟)
    183 => array:3 [▼  // key = 聯賽ID
      "league_id" => 183  // 聯賽ID
      "league_name" => "美國職業棒球聯賽"  // 聯賽名稱 (自動依據玩家語系抓取,若無資料則會以en版的填入)
      "fixtures" => array:3 [▼  // 各賽事
        11387255 => array:9 [▼  // key = 賽事ID
          "fixture_id" => 11387255  // 賽事ID
          "start_time" => "2023-09-19 02:10:00"  // 賽事開始時間
          "status" => 3  // 賽事狀態
          "last_update" => "2023-09-19 05:54:56"  // 賽事最後更新時間
          "home_team_id" => 77603  // 主隊隊伍ID
          "home_team_name" => "洛杉磯道奇"  // 主隊隊伍名稱 (自動依據玩家語系抓取,若無資料則會以en版的填入)
          "away_team_id" => 77587  // 客隊隊伍ID
          "away_team_name" => "底特律老虎"  // 客隊隊伍名稱 (自動依據玩家語系抓取,若無資料則會以en版的填入)
          "markets" => array:5 [▼  // 各玩法
            28 => array:3 [▼  // key = 玩法ID
              "market_id" => 28  // 玩法ID
              "market_name" => "全場大小"  // 玩法名稱 (自動依據玩家語系抓取,若無資料則會以en版的填入)
              "market_bets" => array:2 [▼  // 各賠率
                54930042711387256 => array:7 [▼  // key = 賠率ID
                  "market_bet_id" => 54930042711387256  // 賠率ID
                  "market_bet_name" => "Over"  // 賠率名稱 (自動依據玩家語系抓取,若無資料則會以en版的填入)
                  "base_line" => "8.0"  // base_line
                  "line" => "8.0"  // line
                  "price" => "2.0484"  // price
                  "status" => 3  // 賠率狀態
                  "last_update" => "2023-09-19 04:06:38"  // 賠率最後更新時間
                ]
                182175272511387260 => array:7 [▶]  // 另一個 賠率
              ]
            ]
            226 => array:3 [▶]  // 另一個 玩法
            236 => array:3 [▶]  // 另一個 玩法
            281 => array:3 [▶]  // 另一個 玩法
            342 => array:3 [▶]  // 另一個 玩法
          ]
        ]
        11391624 => array:9 [▶]  // 另一個 賽事
        11391647 => array:9 [▶]  // 另一個 賽事
      ]
    ]
    4146 => array:3 [▶]  // 另一個 聯賽
    5540 => array:3 [▶]  // 另一個 聯賽
    7807 => array:3 [▶]  // 另一個 聯賽
  ]
]
]
"living" => []  // 走地資料. 目前都空
]
*/

        $success_code = strtoupper("SUCCESS_" . $this->controller . "_" . $this->function . "_" . $message);

        $tmp = array();
        $tmp['status'] = 1;
        $tmp['data'] = $data;
        $tmp['message'] = $success_code;
        $tmp['gzip'] = 0;
        if ($gzip) {
            $tmp['gzip'] = 1;
        }
        
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
    protected function ApiError($message , $is_common = false, $gzip = false) {

        if (! $is_common) {  // CLASS_FUNCTION ONLY
            $error_code = strtoupper("ERROR_" . $this->controller . "_" . $this->function . "_" . $message);
        } else {  // 通用錯誤類
            $error_code = strtoupper("ERROR_COMMON_" . $message);
        }      
        
        $tmp = array();
        $tmp['status'] = 0;
        $tmp['data'] = null;
        $tmp['message'] = $error_code;
        $tmp['gzip'] = 0;
        if ($gzip) {
            $tmp['gzip'] = 1;
        }
        
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

}

