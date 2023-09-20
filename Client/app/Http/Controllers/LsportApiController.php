<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;

use App\Models\GameMatch;
use App\Models\GameResult;
use App\Models\GameOrder;

//  ANT號源已棄用
// use App\Models\AntGameList;
// use App\Models\AntMatchList;
// use App\Models\AntRateList;
// use App\Models\AntSeriesList;
// use App\Models\AntTeamList;
// use App\Models\AntTypeList;
// use App\Models\AntNoticeList;

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
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
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
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
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
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
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
        foreach ($return AS $k => $v) {
            $tmp = array();
            foreach ($arrColsToReturn AS $key => $val) {
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
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
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
        foreach ($return AS $k => $v) {
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
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
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

        foreach ($return AS $k => $v) {
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

        //---------------------------------
        // 自DB取出AntNoticeList的各球種公告
        // 由於ANT號源已不再使用且以無求種公告故以下廢棄.

        // $timestamp = time() - (1 * 24 * 60 * 60); 
        // $previous_day = date('Y-m-d 00:00:00', $timestamp); 

        // $return = AntNoticeList::where('create_time', ">=", $previous_day)->orderBy("create_time", "DESC")->get();
        // if ($return === false) {
        //     $this->ApiError("02");
        // }

        // foreach ($return AS $k => $v) { 
        //     $game_id = $v['game_id'];
        //     $title = $v['title_'.$this->agent_lang];
        //     $context = $v['context_'.$this->agent_lang];
        //     $create_time = $v['create_time'];

        //     $notice_list[$game_id][] = [
        //     "game_id" => $game_id,
        //     "title" => $title,
        //     "context" => $context,
        //     "create_time" => $create_time,
        //     ];
        // }

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
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
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
        //$return = AntGameList::where("status", 1)->get();
        $arrSports = LsportSport::get();
        if ($arrSports === false) {
            $this->ApiError("01");
        }

        $sport_type = array();
        foreach ($arrSports AS $k => $v) {
            $sport_type[$v['sport_id']] = $v[$langCol];
        }

        $menu_type = [
            0 => "living",  //走地
            1 => "early",  //早盤
        ];

        $data = array();
        //$total = 0;

        foreach ($menu_type AS $k => $v) {
            switch ($k) {
                case 0:  // 進行中
                    // $return = AntMatchList::join('ant_rate_list', 'ant_match_list.match_id', '=', 'ant_rate_list.match_id')
                    // ->join('ant_series_list', function ($join) {
                    //     $join->on('ant_match_list.game_id', '=', 'ant_series_list.game_id')
                    //          ->on('ant_match_list.series_id', '=', 'ant_series_list.series_id');
                    // })
                    // ->selectRaw('ant_match_list.game_id, COUNT(DISTINCT ant_match_list.id) AS count,COUNT(*) AS rate_count')
                    // ->where('ant_rate_list.is_active', '=', 1)
                    // ->where('ant_match_list.status', 2)
                    // ->where('ant_series_list.status', 1)
                    // ->groupBy('ant_match_list.game_id')
                    // ->having('rate_count', '>', 0)
                    // ->get();

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
                    foreach ($arrFixtures AS $kk => $vv) {
                        $tmp["items"][$vv['sport_id']]['name'] = $sport_type[$vv['sport_id']];
                        $tmp["items"][$vv['sport_id']]['count'] = $vv['count'];
                        $total += $vv['count'];
                    }

                    $tmp['total'] = $total;
                    $data[$v] = $tmp;
                    break;

                case 1:  // 早盤
                    // $return = AntMatchList::join('ant_rate_list', 'ant_match_list.match_id', '=', 'ant_rate_list.match_id')
                    // ->join('ant_series_list', function ($join) {
                    //     $join->on('ant_match_list.game_id', '=', 'ant_series_list.game_id')
                    //          ->on('ant_match_list.series_id', '=', 'ant_series_list.series_id');
                    // })
                    // ->selectRaw('ant_match_list.game_id, COUNT(DISTINCT ant_match_list.id) AS count,COUNT(*) AS rate_count')
                    // ->where('ant_rate_list.is_active', '=', 1)
                    // ->where('ant_match_list.status', 1)
                    // ->where('ant_series_list.status', 1)
                    // ->groupBy('ant_match_list.game_id')
                    // ->having('rate_count', '>', 0)
                    // ->get();

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
                    foreach ($arrFixtures AS $kk => $vv) {
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
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
     * @return ApiSuccess($data = ARRAY 球種列表) | ApiError
     */
    // 賽事列表-分類
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
        //$return = AntGameList::where("status", 1)->get();
        $arrLsportSports = LsportSport::get();
        if ($arrLsportSports === false) {
            $this->ApiError("01");
        }

        $arrAllSports = array();
        foreach ($arrLsportSports AS $k => $v) {
            $arrAllSports[] = array(
                'id' => $v['id'],
                'name' => $v[$langCol],
            );
        }

        $this->ApiSuccess($arrAllSports, "01"); 

    }

    /**
     * MatchIndex
     *
     * 取得某一指定球種的含賽事資料(賠率、聯盟等)的賽事列表
     * 
     * @param Request $request: 前端傳入的使用者請求，
     *                              # 必須包含player代表玩家的ID。User requests passed in by the front-end. Key 'player' is essential, which represents the player ID.
     *                              # 必須包含sport_id代表球種的ID。Key 'sport_id' is essential, which represents the sport ID.
     * @return ApiSuccess($data = ARRAY 該指定球種的含賽事資料(賠率、聯盟等)的賽事列表) | ApiError
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

        // 新的LIST
        $data = array();
        $today = time();
        $after_tomorrow = $today + 2 * 24 * 60 * 60; 
        $after_tomorrow = date('Y-m-d 00:00:00', $after_tomorrow); 

        //////////////////////////////
        // 早盤

/*
SELECT
    l.name_en AS l_name_en, l.name_tw AS l_name_locale,
    f.fixture_id, f.sport_id, f.league_id, f.start_time, f.home_id, f.away_id, f.livescore_extradata, f.periods, f.scoreboard, f.status AS f_status, f.last_update AS f_last_update,
    th.team_id AS th_team_id, th.name_en AS th_name_en, th.name_tw AS th_name_locale,
    ta.team_id AS ta_team_id, ta.name_en AS ta_name_en, ta.name_tw AS ta_name_locale,
    m.market_id, m.name_en AS m_name_en, m.name_tw AS m_name_locale, m.priority, m.main_line,
    --mb.bet_id, mb.base_line, mb.line, mb.name_en AS mb_name_en, mb.name_tw AS mb_name_tw, mb.price, mb.status AS mb_status, mb.last_update AS mb_last_update

FROM lsport_league AS l

JOIN lsport_fixture AS f on (l.league_id = f.league_id) 
JOIN lsport_team AS th on (f.home_id = th.team_id AND l.league_id=th.league_id)
JOIN lsport_team AS ta on (f.away_id = ta.team_id AND l.league_id=ta.league_id)
JOIN lsport_market AS m on (m.fixture_id = f.fixture_id) 
--JOIN lsport_market_bet AS mb on (mb.market_id = m.market_id AND mb.fixture_id = f.fixture_id)

WHERE
    l.sport_id = 154914
    AND l.status = 1
    AND f.start_time >= '2023-09-20 00:00:00'
    AND f.sport_id = 154914
    AND th.sport_id = 154914
    AND ta.sport_id = 154914

ORDER BY
    l.league_id ASC,
    f.fixture_id ASC,
    m.market_id ASC,
    --mb.bet_id ASC

 */

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
            'l.name_en AS l_name_en', 'l.name_'.$this->agent_lang.' AS l_name_locale',
            's.name_en AS s_name_en', 's.name_'.$this->agent_lang.' AS s_name_locale',
            'f.fixture_id', 'f.sport_id', 'f.league_id', 'f.start_time', 'f.home_id', 'f.away_id', 'f.livescore_extradata', 'f.periods', 'f.scoreboard', 'f.status AS f_status', 'f.last_update AS f_last_update',
            'm.market_id', 'm.name_en AS m_name_en', 'm.name_'.$this->agent_lang.' AS m_name_locale', 'm.priority', 'm.main_line',
            'th.team_id AS th_team_id', 'th.name_en AS th_name_en', 'th.name_'.$this->agent_lang.' AS th_name_locale',
            'ta.team_id AS ta_team_id', 'ta.name_en AS ta_name_en', 'ta.name_'.$this->agent_lang.' AS ta_name_locale'
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
    $arrFixtureAndMarkets = array();  //將用於稍後SQL查詢market_bet資料
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
            if (!strlen($dv->s_name_locale)) {  // league name
                $sport_name = $dv->s_name_en;
            } else {
                $sport_name = $dv->s_name_locale;
            }
        }

        //儲存 fixture_id, market_id 及 main_line
        $arrFixtureAndMarkets["{$fixture_id}|{$market_id}|{$main_line}"] = array(
            'fixture_id' => $fixture_id,
            'market_id' => $market_id,
            'main_line' => $main_line,
            //'market_name' => $dv->m_name_en,
        );

        // league 層
        if (!isset($arrLeagues[$league_id]) || !sizeof($arrLeagues[$league_id])) {

            // league_name: 判斷用戶語系資料是否為空,若是則用en就好
            if (!strlen($dv->th_name_locale)) {  // league name
                $league_name = $dv->l_name_en;
            } else {
                $league_name = $dv->l_name_locale;
            }

            // 包入 league 聯賽資料
            $arrLeagues[$league_id] = array(
                'league_id' => $dv->league_id,
                'league_name' => $league_name,
                'fixtures' => array(),
            );
        }

        // fixture 層
        if (!isset($arrLeagues[$league_id]['fixtures'][$fixture_id]) || !sizeof($arrLeagues[$league_id]['fixtures'][$fixture_id])) {

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
            $arrLeagues[$league_id]['fixtures'][$fixture_id] = array(
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
                'markets' => array(),
            );
        }

        //market 層
        if (!isset($arrLeagues[$league_id]['fixtures'][$fixture_id]['markets'][$market_id]) ||
            !sizeof($arrLeagues[$league_id]['fixtures'][$fixture_id]['markets'][$market_id])) {

            // market_name: 判斷用戶語系資料是否為空,若是則用en就好
            if (!strlen($dv->m_name_locale)) {  // market name
                $market_name = $dv->m_name_en;
            } else {
                $market_name = $dv->m_name_locale;
            }

            // 包入 market 玩法資料
            $arrLeagues[$league_id]['fixtures'][$fixture_id]['markets'][$market_id] = array(
                'market_id' => $dv->market_id,
                'market_name' => $market_name,
                'market_bets' => array(),
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

                // merket_bet_name: 判斷用戶語系資料是否為空,若是則用en就好
                if (isset($bv->mb_name_locale)) {  // market name
                    $merket_bet_name = $bv->mb_name_en;
                } else {
                    $merket_bet_name = $bv->mb_name_locale;
                }

                $arrLeagues[$league_id]['fixtures'][$fixture_id]['markets'][$market_id]['market_bets'][$market_bet_id] = array(
                    'merket_bet_id' => $market_bet_id,
                    'base_line' => $bv->base_line,
                    'line' => $bv->line,
                    'merket_bet_name' => $merket_bet_name,
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
        'leagues' => $arrLeagues,
    );
    $arrRet['living'] = array();  //living 走地目前都設空

    $data = $arrRet;
    

        ///////////////////////////////

        // gzip
        // $data = $this->gzip($data);

        $this->ApiSuccess($data, "01");
    }

    /**
     * GameBet
     *
     * 投注接口
     * 
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
     * @return ApiSuccess($data = ???) | ApiError
     */
    public function GameBet(Request $request) {
      
    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        // 取得語系
        $langCol = 'name_' . $this->agent_lang;

        //////////////////////////////////////////

        // 取得系統參數
        $return = SystemConfig::where("name", "risk_order")->first();
        if ($return['value'] > 0) {
            $default_order_status = 1;
            $default_approval_time = null;
        } else {
            // 預設通過
            $default_order_status = 2;
            $default_approval_time = date("Y-m-d H:i:s");
        }

        // 取得必要參數
        // $player_id = $input['player'];
        // $fixture_id = $input['bet_match'];
        // $bet_type_id = $input['bet_type'];
        // $bet_type_item_id = $input['bet_type_item'];
        // $player_rate = $input['bet_rate'];
        // $bet_amount = $input['bet_amount'];
        // $is_better_rate = $input['better_rate'];

        $player_id = $input['player'];
        $fixture_id = $input['bet_match'];  //ant_match_list.match_id
        $bet_type_id = $input['bet_type'];  //ant_rate_list.rate_id
        $bet_type_item_id = $input['bet_type_item'];  //JSON_DECODE(ant_rate_list.item).id
        $player_rate = $input['bet_rate'];  //前端傳來的賠率
        $bet_amount = $input['bet_amount'];  //投注金額
        $is_better_rate = $input['better_rate'];  //受否自動接受更好的賠率(若不接受則在伺服器端賠率較佳時會退回投注)

        $sport_id = 1;  //球種ID
        if (isset($input['sport_id'])) {
            $sport_id = $input['sport_id'];
        }
        
        $order = array();
        
        // 參數檢查 TODO - 初步 隨便弄弄
        if ($bet_amount <= 0) {
            $this->ApiError("01");
        }

        // 取得用戶資料
        $return = Player::where("id", $player_id)->first();
        if ($return == false) {
            $this->ApiError("02");
        }

        // 如果用戶已停用
        if ($return['status'] == 0) {
            $this->ApiError("03");
        }

        $player_account = $return['account'];
        $currency_type = $return['currency_type'];
        $agent_id = $return['agent_id'];
        $player_balance = $return['balance'];

        // 判斷餘額是否足夠下注
        if ($player_balance < $bet_amount) {
            $this->ApiError("04");
        }
        
        //////////////////////////////////////////
        // order data
        $order['player_id'] = $player_id;
        $order['player_name'] = $player_account;
        $order['currency_type'] = $currency_type;
        //////////////////////////////////////////

        // 取得商戶資料
        $return = Agent::where("id", $agent_id)->first();
        if ($return == false) {
            $this->ApiError("05");
        }

        // 如果商戶已停用
        if ($return['status'] == 0) {
            $this->ApiError("06");
        }

        $agent_account = $return['account'];

        //////////////////////////////////////////
        // order data
        $order['agent_id'] = $agent_id;
        $order['agent_name'] = $agent_account;
        //////////////////////////////////////////


        // 取得賽事資料
        //   $return = AntMatchList::where("match_id", $match_id)->where("game_id", $game_id)->first();
        $arrFixtures = LsportFixture::where("fixture_id", $fixture_id)
            ->where("sport_id", $sport_id)
            ->first();
        if ($arrFixtures == false) {
            $this->ApiError("07");
        }

        //match status : 1未开始、2进行中、3已结束、4延期、5中断、99取消
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!! 修改 !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //以下到結尾有許多欄位都需要修改
        if ($arrFixtures['status'] >= 3) {
            $this->ApiError("08");
        }

        // decode 聯盟
        $series_data = json_decode($arrFixtures['league'], true);
        //////////////////////////////////////////
        // order data
        $order['league_id'] = $series_data['league_id'];
        $order['league_name'] = $series_data[$langCol];
        $order['fixture_id'] = $fixture_id;
        $order['sport_id'] = $arrFixtures['sport_id'];
        //////////////////////////////////////////

        // decode 隊伍
        $teams_data = json_decode($arrFixtures['teams'], true);
        //////////////////////////////////////////
        // order data
        if ($teams_data[0]['index'] == 1) {
            $order['home_team_id'] = $teams_data[0]['team']['id'];
            $order['home_team_name'] = $teams_data[0]['team']["$langCol}"];
        } else {
            $order['away_team_id'] = $teams_data[0]['team']['id'];
            $order['away_team_name'] = $teams_data[0]['team']['name_' . $this->agent_lang];
        }
        
        if ($teams_data[1]['index'] == 1) {
            $order['home_team_id'] = $teams_data[1]['team']['id'];
            $order['home_team_name'] = $teams_data[1]['team'][$langCol];
        } else {
            $order['away_team_id'] = $teams_data[1]['team']['id'];
            $order['away_team_name'] = $teams_data[1]['team'][$langCol];
        }
        //////////////////////////////////////////

        // 取得賠率
        // $return = AntRateList::where("rate_id", $bet_type_id)->where("match_id", $match_id)->first();
        $arrOdds = LsportMarketBet::where("id", $bet_type_id)->where("fixture_id", $fixture_id)->first();
        if ($arrOdds == false) {
            $this->ApiError("09");
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
            $this->ApiError("14");
        }

        //////////////////////////////////////////
        // order data
        $order['type_id'] = $bet_type_id;
        $order['type_item_id'] = $bet_type_item_id;
        $order['type_name'] = $arrOdds['name_cn'];  // 'name_' . $this->agent_lang;
        $order['type_item_name'] = $rate_data['name_cn'];  // 'name_' . $this->agent_lang;
        $order['type_priority'] = $type_priority;
        $order['bet_rate'] = $rate_data['rate'];
        
        $order['player_rate'] = $player_rate;
        $order['better_rate'] = $is_better_rate;
        
        //////////////////////////////////////////

        // 判斷 is_better_rate
        if (($is_better_rate == 1) && ($current_rate < $player_rate)) {
            $this->ApiError("10");
        }

        //////////////////////////////////////////
        // order data
        $order['bet_amount'] = $bet_amount;
        $order['status'] = $default_order_status;
        $order['create_time'] = date("Y-m-d H:i:s");
        $order['approval_time'] = $default_approval_time;
        
        //////////////////////////////////////////

        // 新增注單資料
        $newOrderId = GameOrder::insertGetId($order);      
        if ($newOrderId == false) {
            $this->ApiError("11");
        }

        $order_id = $newOrderId;
        // 設定m_id 
        $return = GameOrder::where("id", $order_id)->update([
            "m_id" => $order_id
        ]);      
        if ($return == false) {
            $this->ApiError("12");
        }
        
        // 扣款
        $before_amount = $player_balance;
        $change_amount = $bet_amount;
        $after_amount = $before_amount - $change_amount;

        $return = Player::where("id", $player_id)->update([
            "balance" => $after_amount
        ]);      
        if ($return == false) {
            $this->ApiError("13");
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
     * mGameBet
     *
     * 串關投注接口
     * 
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
     * @return ApiSuccess($data = ???) | ApiError
     */
    public function mGameBet(Request $request) {
      
    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        //////////////////////////////////////////

        // 取得系統參數
        $return = SystemConfig::where("name", "risk_order")->first();
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
        $bet_amount = $input['bet_amount'];
        $is_better_rate = $input['better_rate'];

        $sport_id = 1;
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
        $return = Player::where("id", $player_id)->first();
        if ($return == false) {
            $this->ApiError("02");
        }

        // 如果用戶已停用
        if ($return['status'] == 0) {
            $this->ApiError("03");
        }

        $player_account = $return['account'];
        $currency_type = $return['currency_type'];
        $agent_id = $return['agent_id'];
        $player_balance = $return['balance'];

        // 判斷餘額是否足夠下注
        if ($player_balance < $bet_amount) {
            $this->ApiError("04");
        }

        // 判斷下注額度是否超過限額
        
        
        //////////////////////////////////////////
        // order data
        $order['player_id'] = $player_id;
        $order['player_name'] = $player_account;
        $order['currency_type'] = $currency_type;
        //////////////////////////////////////////

        // 取得商戶資料
        $return = Agent::where("id", $agent_id)
            ->first();
        if ($return == false) {
            $this->ApiError("05");
        }

        // 如果商戶已停用
        if ($return['status'] == 0) {
            $this->ApiError("06");
        }

        $agent_account = $return['account'];

        //////////////////////////////////////////
        // order data
        $order['agent_id'] = $agent_id;
        $order['agent_name'] = $agent_account;
        //////////////////////////////////////////


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
    
            // 取得用戶資料
            $return = Player::where("id", $player_id)->first();
            if ($return == false) {
                $this->ApiError("08");
            }
    
            // 如果用戶已停用
            if ($return['status'] == 0) {
                $this->ApiError("09");
            }
    
            $player_account = $return['account'];
            $currency_type = $return['currency_type'];
            $agent_id = $return['agent_id'];
            $player_balance = $return['balance'];
    
            // 判斷餘額是否足夠下注
            if ($player_balance < $bet_amount) {
                $this->ApiError("10");
            }
            
            //////////////////////////////////////////
            // order data
            $order['player_id'] = $player_id;
            $order['player_name'] = $player_account;
            $order['currency_type'] = $currency_type;
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
            // $return = AntMatchList::where("match_id", $match_id)->where("game_id", $game_id)->first();
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
            // $return = AntRateList::where("rate_id", $bet_type_id)->where("match_id", $match_id)->first();
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
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
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

        if (!isset($input['page']) || ($input['page'] == "")) {
            $input['page'] = 1; // 預設1 
        }

    	/////////////////////////
      
        // Search 區用

        // 狀態
        $status = [
            -1 => "異常",
            1 => "等待開賽",
            2 => "進行中",
            3 => "已結束",
            4 => "延期",
            5 => "中斷",
            99 => "取消"
        ];

            /////////////////////////

        // 取得比賽資料

        $page_limit = $this->page_limit;
        $page = $input['page'];
        $skip = ($page-1)*$page_limit;

        $sport_id = $input['sport'];


        // form search
        // $AntMatchList = AntMatchList::where("game_id", $sport_id)->where("status", ">=",2);
        $LsFixture = LsportFixture::where("sport_id", $sport_id)
            ->where("status", ">=",2);

        $return = $LsFixture
            ->skip($skip)
            ->take($page_limit)
            ->orderBy('start_time', 'DESC')
            ->get();
        $pagination = $LsFixture->count();

        ////////////////////
        $columns = array(
            "id",
            "fixture_id",
            "game_id",
            "league_id",
            "start_time",
            "end_time",
            "status"
        );

        $data = array();
        foreach ($return AS $k => $v) {

            $tmp = array();
            
            $series = json_decode($v['league'], true);
            $league_id = $series['league_id'];
            $sport_id = $series['sport_id'];
            // $tmp_logo = AntSeriesList::where("series_id", $series_id)->where("game_id", $sport_id)->where("status",1)->first();
            $tmp_logo = LsportLeague::where("league_id", $league_id)
                ->where("sport_id", $sport_id)
                ->where("status",1)
                ->first();
            if ($tmp_logo === false) {
                $this->ApiError("01");
            }
            if ($tmp_logo == null) {
                continue;
            }

            $tmp['league_name'] = $tmp_logo[$langCol];
            $tmp['series_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'] . "?v=" . $this->system_config['version'];

            foreach ($columns AS $kk => $vv) {
                $tmp[$vv] = $v[$vv]; 
            }

            // stat
            $stat = json_decode($v['stat'], true);
            unset($stat['stat']['fixture_id']);
            unset($stat['stat']['time']);
            if ($v['stat'] == "") {
                $tmp['stat'] = [];
            } else {
                $tmp['stat'] = $stat['stat'];
            }

            $tmp['status'] = $status[$v['status']];
        
            $teams = json_decode($v['teams'], true);

            $teams = json_decode($v['teams'], true);

            foreach ($teams AS $key => $value) {
                $team_id = $value['team']['id'];
                // $tmp_logo = AntTeamList::where("team_id", $team_id)->where("game_id", $sport_id)->first();
                $tmp_logo = LsportTeam::where("team_id", $team_id)
                    ->where("sport_id", $sport_id)
                    ->first();
                if ($tmp_logo === false) {
                    $this->error(__CLASS__, __FUNCTION__, "05");
                }
                
                if ($tmp_logo == null) {
                    continue;
                }

                /////////////////////////////////

                $teams[$key]['team']['name'] =  $tmp_logo[$langCol];
                $teams[$key]['team']['logo'] =  $this->system_config['image_url'] . $tmp_logo['local_logo'] . "?v=" . $this->system_config['version'];
            
            }

            foreach ($columns AS $kk => $vv) {
                $tmp[$vv] = $v[$vv]; 
            }
            
            $tmp['status'] = $status[$v['status']];
        

            foreach ($teams AS $kk => $vv) {
                if ($vv['index'] == 1) {
                    $tmp['home_team_name'] = $vv['team']['name'];
                    $tmp['home_team_logo'] = $vv['team']['logo'];
                    $tmp['home_team_score'] = $vv['total_score'];
                } else {
                    $tmp['away_team_name'] = $vv['team']['name'];
                    $tmp['away_team_logo'] = $vv['team']['logo'];
                    $tmp['away_team_score'] = $vv['total_score'];
                }
            }

            $data[] = $tmp;
        }

        // gzip
        $data = $this->gzip($data);

        $this->ajaxSuccess("success_result_index_01", $data);
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
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
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

        //$return = AntMatchList::where("match_id", $match_id)->where("game_id", $sport_id)->get();
        $return = LsportFixture::where("fixture_id", $fixture_id)
            ->where("sport_id", $sport_id)
            ->get();
        if ($return === false) {
            $this->ApiError("03");
        }
        
        // $tmp = $this->rebuild($return, $this->agent_lang, $sport_id);

        $data = $tmp;

        /**************************************/

        // gzip
        $data = $this->gzip($data);

        $this->ApiSuccess($data, "01", true); 
    }

    /**
     * CommonOrder
     *
     * ?????
     * 
     * @param Request $request: 前端傳入的使用者請求。User requests passed in by the front-end.
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
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

        foreach ($return AS $k => $v) {

            foreach ($columns AS $kk => $vv) {
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

                foreach ($cc AS $kkk => $vvv) {
                    $tmp_bet_data = array();

                    $league_id = $vvv['league_id'];
                    //$tmp_d = AntSeriesList::where("series_id", $series_id)->where("game_id", $vvv['game_id'])->first();
                    $tmp_d = LsportLeague::where("league_id", $league_id)->where("sport_id", $vvv['sport_id'])->first();
                    if ($tmp_d === null) {
                    $tmp_bet_data['league_name'] = $vvv['league_name'];
                    } else {
                    $tmp_bet_data['league_name'] = $tmp_d[$langCol];
                    }
        
                    $type_id = $vvv['type_id'];
                    // $tmp_d = AntRateList::where("rate_id", $type_id)->where("game_id", $vvv['game_id'])->first();
                    $tmp_d = LsportMarketBet::where("id", $type_id)->where("sport_id", $vvv['sport_id'])->first();
                    if ($tmp_d === null) {
                    $tmp_bet_data['type_name'] = $vvv['type_name'];
                    } else {
                    $tmp_bet_data['type_name'] = $tmp_d[$langCol];
                    }
                    
                    $replace_lang = array();
        
                    $home_team_id = $vvv['home_team_id'];
                    // $tmp_d = AntTeamList::where("team_id", $home_team_id)->where("game_id", $vvv['game_id'])->first();
                    $tmp_d = LsportTeam::where("team_id", $home_team_id)->where("sport_id", $vvv['sport_id'])->first();
                    if ($tmp_d === null) {
                    $tmp_bet_data['home_team_name'] = $vvv['home_team_name'];
                    } else {
                    $tmp_bet_data['home_team_name'] = $tmp_d[$langCol];
                    $replace_lang[0]['tw'] = $tmp_d['name_tw'];
                    $replace_lang[0]['cn'] = $tmp_d['name_cn'];
                    }
        
                    $away_team_id = $vvv['away_team_id'];
                    // $tmp_d = AntTeamList::where("team_id", $away_team_id)->where("game_id", $vvv['game_id'])->first();
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
                    foreach ($replace_lang AS $lang_k => $lang_v) {
                    $item_name = str_replace($lang_v['cn'], $lang_v['tw'], $item_name);
                    }
                    $tmp_bet_data['type_item_name'] = $item_name;

                    $tmp_bet_data['bet_rate'] = $vvv['bet_rate'];
                    $tmp_bet_data['status'] = $status_message[$vvv['status']];
                    $tmp_bet_data['type_priority'] = $vvv['type_priority'];

                    $tmp_bet_data['home_team_logo'] = "";
                    $tmp_bet_data['away_team_logo'] = "";
                    
                    // 取得隊伍logo
                    // $tmp_logo = AntTeamList::where("team_id", $home_team_id)->where("game_id", $game_id)->first();
                    $tmp_logo = LsportTeam::where("team_id", $home_team_id)->where("sport_id", $sport_id)->first();
                    if (($tmp_logo === false) || ($tmp_logo == null)) {
                    continue;
                    }
                    if (isset($tmp_logo['local_logo'])) {
                    $tmp_bet_data['home_team_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'];
                    }
            
                    // $tmp_logo = AntTeamList::where("team_id", $away_team_id)->where("game_id", $game_id)->first();
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
                // $tmp_d = AntSeriesList::where("series_id", $series_id)->where("game_id", $v['game_id'])->first();
                $tmp_d = LsportLeague::where("league_id", $league_id)->where("sport_id", $v['sport_id'])->first();
                if ($tmp_d === null) {
                    $tmp_bet_data['league_name'] = $v['league_name'];
                } else {
                    $tmp_bet_data['league_name'] = $tmp_d[$langCol];
                }

                $type_id = $v['type_id'];
                // $tmp_d = AntRateList::where("rate_id", $type_id)->where("game_id", $v['game_id'])->first();
                $tmp_d = LsportMarketBet::where("id", $type_id)->where("sport_id", $v['sport_id'])->first();
                if ($tmp_d === null) {
                    $tmp_bet_data['type_name'] = $v['type_name'];
                } else {
                    $tmp_bet_data['type_name'] = $tmp_d[$langCol];
                }
                
                $replace_lang = array();

                $home_team_id = $v['home_team_id'];
                // $tmp_d = AntTeamList::where("team_id", $home_team_id)->where("game_id", $v['game_id'])->first();
                $tmp_d = LsportTeam::where("team_id", $home_team_id)->where("sport_id", $v['sport_id'])->first();
                if ($tmp_d === null) {
                    $tmp_bet_data['home_team_name'] = $v['home_team_name'];
                } else {
                    $tmp_bet_data['home_team_name'] = $tmp_d[$langCol];
                    $replace_lang[0]['tw'] = $tmp_d['name_tw'];
                    $replace_lang[0]['cn'] = $tmp_d['name_cn'];
                }

                $away_team_id = $v['away_team_id'];
                // $tmp_d = AntTeamList::where("team_id", $away_team_id)->where("game_id", $v['game_id'])->first();
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
                foreach ($replace_lang AS $lang_k => $lang_v) {
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
                // $tmp_logo = AntTeamList::where("team_id", $home_team_id)->where("game_id", $game_id)->first();
                $tmp_logo = LsportTeam::where("team_id", $home_team_id)->where("sport_id", $sport_id)->first();
                if (($tmp_logo === false) || ($tmp_logo == null)) {
                    continue;
                }
                if (isset($tmp_logo['local_logo'])) {
                    $tmp_bet_data['home_team_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'];
                }
        
                // $tmp_logo = AntTeamList::where("team_id", $away_team_id)->where("game_id", $game_id)->first();
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
     *                          # player: 必要。玩家的ID。 Required. Represents the player ID.
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
        foreach ($return AS $k => $v) {

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

        return $data;

        //除錯後修正!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // $data = json_encode($data, true);
        // $compressedData = gzcompress($data);  // 使用 gzcompress() 函數進行壓縮
        // $base64Data = base64_encode($compressedData);  // 使用 base64_encode() 函數進行 base64 編碼

        // return $base64Data;
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

        $success_code = strtoupper("SUCCESS_" . $this->controller . "_" . $this->function . "_" . $message);

        $tmp = array();
        $tmp['status'] = 1;
        $tmp['data'] = $data;
        $tmp['message'] = $success_code;
        $tmp['gzip'] = 0;
        if ($gzip) {
            //除錯後修正!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            //$tmp['gzip'] = 1;
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

