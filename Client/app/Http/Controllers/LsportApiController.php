<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Session;
use DB;


// LSport
use App\Models\LsportFixture;
use App\Models\LsportLeague;
use App\Models\LsportSport;
use App\Models\LsportTeam;
use App\Models\LsportMarket;
use App\Models\LsportMarketBet;
use App\Models\LsportNotice;

use App\Models\PlayerOnline;
use App\Models\Player;
use App\Models\Agent;
use App\Models\GameOrder;
use App\Models\PlayerBalanceLogs;
use App\Models\ClientMarquee;
// use App\Models\SystemConfig;


/**
 * LsportApiController
 * 
 * Client端的前端所需的資料接口。對應號源:LSports。
 * Providing data sources needed by the Client front-end. Corresponding dataset source: LSports.
 */

class LsportApiController extends Controller {
    
    protected $page_limit = 20;

    protected $agent_lang;  // 玩家的代理的語系. 選擇相對應的DB翻譯欄位時會用到.

    protected $default_sport_id = 154914;  //預設的 sport_id (棒球)
    protected $lsport_sport_id = array(
        'baseball' => 154914,
        'basketball' => 48242,
        'football' => 6046,
    );
    
    //lsport_fixture.status 賽事狀態
    protected $fixture_status = array(
        'early' => 1,  // 未開賽
        'living' => 2,  // 賽事中
        'about_to_start' => 9,  // 即將開賽
    );
    
    //game_order.status 賽事狀態
    protected $game_order_status = array(
        'delay_bet' => 1,  // 新的延時注單
        'wait_for_result' => 2,  // 等待開獎的注單
        'wait_for_payment' => 3,  // 等待派彩的注單
        'finished' => 4,  // 已派彩的注單 (結束)
        'wait_for_audit' => 5,  // 等待審核的注單(風控大單)
    );

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
        $return = Player::where("id", $player_id)->fetch();
        if ($return === false) {
            $this->ApiError("01");
        }

        if ($return['status'] != 1) {
            $this->ApiError("02");
        }

        ///////////////////////////////////
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

        ///////////////////////////////////
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
        // 取得代理的語系
        $player_id = $input['player'];
        $agent_lang = $this->getAgentLang($player_id);
        $lang_col = 'name_' . $agent_lang;

        //---------------------------------
        // 自DB取出有效的Client端系統公告(其實也就是Client端跑馬燈)
        $notice_list = array();

        // 系統公告
        $return = ClientMarquee::getList(['status'=>1]);
        if ($return === false) {
            $this->ApiError("01");
        }

        foreach ($return as $k => $v) {
            $notice_list[] = $v['marquee'];
        }

        //---------------------------------
        // 自DB取出LsportNotice
        $week_before =  date('Y-m-d 00:00:00', strtotime('-1 week'));
        $return = LsportNotice::getList(["create_time" => $week_before]);
        if ($return === false) {
            $this->ApiError("02");
        }

        $notice_data = $return;

        foreach ($notice_data as $k => $v) {
            $sport_id = $v['sport_id'];
            $league_id = $v['league_id'];
            $fixture_id = $v['fixture_id'];
            $notice_type = $v['type'];

            // cache
            $sport_name = LsportSport::getName(['sport_id'=>$sport_id, 'api_lang'=>$agent_lang]);

            // cache
            $league_name = LsportLeague::getName(['league_id'=>$league_id, 'api_lang'=>$agent_lang]);

            //對於跑馬燈只要抓前後一天內的賽事資料就好
            $fixture_start_time = date('Y-m-d H:i:s', time()-60*60*24);
            
            // fixture -----
            $return = LsportFixture::where('fixture_id', $fixture_id)->fetch();
            if ($return === false) {
                $this->ApiError("02");
            }

            $fixture = $return;

            $fixture_start_time = $fixture['start_time'];
            $home_team_id = $fixture['home_id'];
            $away_team_id = $fixture['away_id'];

            // cache
            $home_team_name = LsportTeam::getName(['team_id'=>$home_team_id, 'api_lang'=>$agent_lang]);

            // cache
            $away_team_name = LsportTeam::getName(['team_id'=>$away_team_id, 'api_lang'=>$agent_lang]);

            // 處理 Duplication of <FIXTURE_ID> 的翻譯問題
            if (strpos($notice_type, 'Duplication of') !== false) {
                $arr_notice_type = explode(' ', $notice_type);
                $notice_type = "{$arr_notice_type[0]} {$arr_notice_type[1]}";
                $fixture_id = $arr_notice_type[2];
            }

            $title = trans('notice.fixture_cancellation_reasons.'.'title:'.$notice_type, [
                'sport_name' => $sport_name,
                'league_name' => $league_name,
            ]);
            
            $fixture_start_time2 = date(
                trans('notice.fixture_cancellation_reasons.date_time_to_hour'),
                strtotime($fixture_start_time)
            );

            $context = trans('notice.fixture_cancellation_reasons.'.$notice_type, [
                'sport_name' => $sport_name,
                'league_name' => $league_name,
                'fixture_start_time' => $fixture_start_time2,
                'home_team_name' => $home_team_name,
                'away_team_name' => $away_team_name,
                'fixture_id' => $fixture_id,
            ]);
    
            $notice_list[] = $title . " : " . $context;
            
        }

        ///////////////////////////////////

        $data = $notice_list;
        // gzip
        if (!isset($input['is_gzip']) || ($input['is_gzip']==1)) {  // 方便測試觀察輸出可以開關gzip
            $data = $this->gzip($data);
            $this->ApiSuccess($data, "01", true);
        } else {
            $this->ApiSuccess($data, "01", false);
        }
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
        // 取得代理的語系
        $player_id = $input['player'];
        $agent_lang = $this->getAgentLang($player_id);
        $lang_col = 'name_' . $agent_lang;

        //---------------------------------
        // 自DB取出有效的Client端系統公告(其實也就是Client端跑馬燈)
        $notice_list = array();

        // 系統公告
        $return = ClientMarquee::getList(['status'=>1]);
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

        //---------------------------------
        // 自DB取出LsportNotice
        $week_before =  date('Y-m-d 00:00:00', strtotime('-1 week'));
        $return = LsportNotice::getList(["create_time" => $week_before]);
        if ($return === false) {
            $this->ApiError("02");
        }

        $notice_data = $return;

        foreach ($notice_data as $k => $v) {
            $sport_id = $v['sport_id'];
            $league_id = $v['league_id'];
            $fixture_id = $v['fixture_id'];
            $notice_type = $v['type'];

            // sport -----
            $sport_name = LsportSport::getName(['sport_id'=>$sport_id, 'api_lang'=>$agent_lang]);

            // league -----
            $league_name = LsportLeague::getName(['league_id'=>$league_id, 'api_lang'=>$agent_lang]);

            // fixture -----
            $return = LsportFixture::where('fixture_id', $fixture_id)->fetch();
            if ($return === false) {
                $this->ApiError("02");
            }

            $fixture = $return;
            $fixture_start_time = $fixture['start_time'];
            $home_team_id = $fixture['home_id'];
            $away_team_id = $fixture['away_id'];

            // team: home team -----
            $home_team_name = LsportTeam::getName(['team_id'=>$home_team_id, 'api_lang'=>$agent_lang]);

            // team: away team -----
            $away_team_name = LsportTeam::getName(['team_id'=>$away_team_id, 'api_lang'=>$agent_lang]);

            // 處理 Duplication of <FIXTURE_ID> 的翻譯問題
            if (strpos($notice_type, 'Duplication of') !== false) {
                $arr_notice_type = explode(' ', $notice_type);
                $notice_type = "{$arr_notice_type[0]} {$arr_notice_type[1]}";
                $fixture_id = $arr_notice_type[2];
            }
    
            $title = trans('notice.fixture_cancellation_reasons.'.'title:'.$notice_type, [
                'sport_name' => $sport_name,
                'league_name' => $league_name,
            ]);
            
            $fixture_start_time2 = date(
                trans('notice.fixture_cancellation_reasons.date_time_to_hour'),
                strtotime($fixture_start_time)
            );

            $context = trans('notice.fixture_cancellation_reasons.'.$notice_type, [
                'sport_name' => $sport_name,
                'league_name' => $league_name,
                'fixture_start_time' => $fixture_start_time2,
                'home_team_name' => $home_team_name,
                'away_team_name' => $away_team_name,
                'fixture_id' => $fixture_id,
            ]);

            $create_time = $v['create_time'];
    
            $notice_list[$sport_id][] = [
                "sport_id" => $sport_id,
                "title" => $title,
                "context" => $context,
                "create_time" => $create_time,
            ];
        }

        ///////////////////////////////////

        $data = $notice_list;
        // gzip
        if (!isset($input['is_gzip']) || ($input['is_gzip']==1)) {  // 方便測試觀察輸出可以開關gzip
            $data = $this->gzip($data);
            $this->ApiSuccess($data, "01", true);
        } else {
            $this->ApiSuccess($data, "01", false);
        }
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

        //---------------------------------
        // 取得代理的語系
        $player_id = $input['player'];
        $agent_lang = $this->getAgentLang($player_id);

    	//---------------------------------

        $today = time();
        $after_tomorrow_es = $today + 2 * 24 * 60 * 60; 
        $after_tomorrow_es = '"'.date('Y-m-d', $after_tomorrow_es).'T00:00:00"'; // 這個「"」不能拿掉, es會報錯

    	//---------------------------------

        $return = LsportFixture::select('sport_id', 'status', DB::raw('COUNT(*) as count'))
        ->whereIn("status",[1,2,9])
        ->where("start_time","<=", $after_tomorrow_es)   
        ->groupBy('sport_id', 'status')
        ->total();    

        if ($return === false) {
            $this->ApiError("01");
        }

        // 整理統計 , 回傳格式取決於SQL
        $list = array();
        $status_name = ["","early","living"];

        foreach ($return as $k => $v) {
            foreach ($v['buckets'] as $kk => $vv) {
                $sport_id = $vv['key'];  
                foreach ($vv as $kkk => $vvv) {
                    if (!in_array($kkk,['key','doc_count'])) {
                        foreach ($vvv['buckets'] as $kkkk => $vvvv) {
                            $status = $vvvv['key'];
                            if ($status == 9) { // 即將開始視為滾球
                                $status = 2;
                            }

                            $current_status_name = $status_name[$status];

                            $tmp_count = $vvvv['count']['value'];
                            $list[$current_status_name]['items'][$sport_id]['count'] = $tmp_count;
                            if (isset($list[$current_status_name]['total'])) {
                                $list[$current_status_name]['total'] += $tmp_count;
                            } else {
                                $list[$current_status_name]['total'] = $tmp_count;
                            }

                            // 取得體育名稱
                            $sport_name = LsportSport::getName(['sport_id'=>$sport_id, 'api_lang'=>$agent_lang]);
                            $list[$current_status_name]['items'][$sport_id]['name'] = $sport_name;
                        }
                    }
                }
            }
        }

        ///////////////////////////////////
        $data = $list;

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

        //---------------------------------
        // 取得代理的語系
        $player_id = $input['player'];
        $agent_lang = $this->getAgentLang($player_id);
        $lang_col = 'name_' . $agent_lang;

        //---------------------------------
        // 取得球種資料
        $return = LsportSport::where('status', 1)->orderBy('id', 'ASC')->list();
        if ($return === false) {
            $this->ApiError("01");
        }

        $sports = $return;

        $data = array();
        foreach ($sports as $k => $v) {

            $sport_name = $v['name_en'];
            if ($v[$lang_col] != "") {
                $sport_name = $v[$lang_col];
            }

            $data[] = array(
                'sport_id' => $v['sport_id'],
                'name' => $sport_name
            );
        }

        ///////////////////////////////////

        $this->ApiSuccess($data, "01");

    }

    /**
     * MatchSport
     *
     * 取回一指定球種的近期賽事(2天內開賽)的列表包含賠率及聯賽等。
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

        //---------------------------------
        // 取得代理的語系
        $player_id = $input['player'];
        $agent_lang = $this->getAgentLang($player_id);
        $lang_col = 'name_' . $agent_lang;

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

        //////////////////////////////////////////
        // DB取出賽事

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
                'l.name_en AS l_name_en', 'l.'.$lang_col.' AS l_name_locale',
                's.name_en AS s_name_en', 's.'.$lang_col.' AS s_name_locale',
                'f.fixture_id', 'f.sport_id', 'f.league_id', 'f.start_time', 'f.livescore_extradata', 'f.periods', 'f.scoreboard', 'f.status AS f_status', 'f.last_update AS f_last_update',
                'th.team_id AS th_team_id', 'th.name_en AS th_name_en', 'th.'.$lang_col.' AS th_name_locale',
                'ta.team_id AS ta_team_id', 'ta.name_en AS ta_name_en', 'ta.'.$lang_col.' AS ta_name_locale'
            )
            ->where('s.status', 1)
            ->where('l.status', 1)
            ->where('l.sport_id', $sport_id)
            ->whereIn('f.status', [1, 2, 9])  //可區分:未開賽及走地中. 9=即將開賽(大概半小時內)
            ->where('f.start_time', "<=", $after_tomorrow)
            ->orderBy('l.league_id', 'ASC')
            ->orderBy('f.fixture_id', 'ASC')
            // ->orderBy('m.market_id', 'ASC')
            ->get();

        if ($data === false) {
            $this->ApiError('02');
        }

        //儲存league-fixture-market的階層資料
        $arrLeagues = array(
            $this->fixture_status['early'] => array(),  // 早盤
            $this->fixture_status['living'] => array(),  // 走地
            // $this->fixture_status['about_to_start'] => array(),  // 即將開賽
        );
        //$arrFixtureAndMarkets = array();  //將用於稍後SQL查詢market_bet資料
        $sport_name = null;  //儲存球種名稱



        //////////////////////////////////////////
        // 開始loop 賽事資料

        foreach ($data as $dk => $dv) {
            $league_id = $dv->league_id;
            $fixture_id = $dv->fixture_id;

            $fixture_status = intval($dv->f_status);
            $real_fixture_status = $fixture_status;  // 傳遞給前端讓前端知道賽事真實狀態

            // 處理即將開賽歸類於走地的問題
            if ($fixture_status == $this->fixture_status['about_to_start']) {
                $fixture_status = $this->fixture_status['living'];
            }

            // $market_id = $dv->market_id;
            // $market_priority = $dv->priority;
            // $market_main_line = $dv->main_line;

            // sport_name: 判斷用戶語系資料是否為空,若是則用en就好
            if (empty($sport_name)) {  //只須設定一次
                if (empty($dv->s_name_locale)) {  // sport name
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
                if (empty($dv->l_name_locale)) {  // league name
                    $league_name = $dv->l_name_en;
                } else {
                    $league_name = $dv->l_name_locale;
                }

                // 包入 league 聯賽資料
                $arrLeagues[$fixture_status][$league_id] = array(
                    'league_id' => $league_id,
                    'league_name' => $league_name,
                    'list' => array(),
                );
            }

            // fixture 層 ----------------------------
            if (!isset($arrLeagues[$fixture_status][$league_id]['list'][$fixture_id])
                || !sizeof($arrLeagues[$fixture_status][$league_id]['list'][$fixture_id])
                ) {

                // home_team_name: 判斷用戶語系資料是否為空,若是則用en就好
                if (empty($dv->th_name_locale)) {  // home team
                    $home_team_name = $dv->th_name_en;
                } else {
                    $home_team_name = $dv->th_name_locale;
                }
                // away_team_name: 判斷用戶語系資料是否為空,若是則用en就好
                if (empty($dv->ta_name_locale)) {  // away_team
                    $away_team_name = $dv->ta_name_en;
                } else {
                    $away_team_name = $dv->ta_name_locale;
                }

                // 比分板跟各局得分
                $livescore_extradata = $dv->livescore_extradata;
                $periods = $dv->periods;
                $scoreboard = $dv->scoreboard;

                // 解析以回傳
                $parsed_periods = $this->getMatchPeriods($sport_id, $fixture_status, $scoreboard, $livescore_extradata);
                $parsed_scoreboard = $this->getMatchScoreboard($sport_id, $fixture_status, $periods, $scoreboard);

                // 包入 fixture 賽事資料 ---------------
                $arrLeagues[$fixture_status][$league_id]['list'][$fixture_id] = array(
                    'fixture_id' => $dv->fixture_id,
                    'start_time' => $dv->start_time,
                    'order_by' => strtotime($dv->start_time),
                    'status' => $real_fixture_status,
                    'last_update' => $dv->f_last_update,
                    'home_team_id' => $dv->th_team_id,
                    'home_team_name' => $home_team_name,
                    'away_team_id' => $dv->ta_team_id,
                    'away_team_name' => $away_team_name,
                    'periods' => $parsed_periods,
                    'scoreboard' => $parsed_scoreboard,
                    'market_bet_count' => 0,
                    'list' => array(),
                );
            }

            //market 層 ----------------------------
            //取出[賽事]的賠率 ----------------------------
            $market_data = DB::table('lsport_market as m')
                ->select(
                    'm.market_id',
                    'm.name_en AS m_name_en',
                    'm.'.$lang_col.' AS m_name_locale',
                    'm.priority',
                    'm.main_line',
                )
                ->where('m.fixture_id', $fixture_id)
                ->orderBy('m.market_id', 'ASC')
                ->get();

            if ($market_data === false) {
                $this->ApiError('03');
            }

            // 開始繞玩法資料
            foreach ($market_data as $k => $v) {
                $market_id = $v->market_id;
                $market_priority = $v->priority;
                $market_main_line = $v->main_line;

                // market_name: 判斷用戶語系資料是否為空,若是則用en就好
                if (empty($v->m_name_locale)) {
                    $market_name = $v->m_name_en;
                } else {
                    $market_name = $v->m_name_locale;
                }

                //取出[賽事+玩法+玩法.base_line]的賠率 ----------------------------
                $market_bet_data = DB::table('lsport_market_bet as mb')
                    ->select(
                        'mb.bet_id',
                        'mb.base_line',
                        'mb.line',
                        'mb.name_en AS mb_name_en',
                        'mb.'.$lang_col.' AS mb_name_locale',
                        'mb.price',
                        'mb.status AS status',
                        'mb.last_update AS last_update',
                    )
                    ->where('mb.fixture_id', $fixture_id)
                    ->whereIn('mb.provder_bet_id', [0,8]) // 號源
                    ->where('mb.market_id', $market_id)
                    ->where('mb.base_line', $market_main_line)  //這邊用 base_line 或 line 都可以
                    //->orderBy('mb.bet_id', 'ASC')  //注意排序
                    ->orderBy('mb.name_en', 'ASC')  //注意排序
                    ->get();

                if ($market_bet_data === false) {
                    $this->ApiError('04');
                }

                $arr_market_bet = array();

                // 開始繞賠率資料
                foreach ($market_bet_data as $k2 => $v2) {
                    $market_bet_id = $v2->bet_id;

                    // 加總 market_bet_count
                    $arrLeagues[$fixture_status][$league_id]['list'][$fixture_id]['market_bet_count'] += 1;

                    // $market_bet_name = $v2->mb_name_locale;
                    // market_bet_name: 判斷用戶語系資料是否為空,若是則用en就好
                    if (empty($v2->mb_name_locale)) {
                        $market_bet_name = $v2->mb_name_en;
                    } else {
                        $market_bet_name = $v2->mb_name_locale;
                    }

                    // 包入 market_bet 賠率資料 ---------------
                    //$arrLeagues[$fixture_status][$league_id]['list'][$fixture_id]['list'][$market_id]['list'][$market_bet_id] = array(
                    $arr_market_bet[] = array(
                        'market_bet_id' => $market_bet_id,
                        'market_bet_name' => $market_bet_name,
                        'market_bet_name_en' => $v2->mb_name_en,
                        //'base_line' => $v2->base_line,
                        'line' => $v2->line,
                        'price' => $v2->price,
                        'status' => $v2->status,
                        'last_update' => $v2->last_update,
                    );
                }

                // 包入 market_bet 賠率資料 ---------------
                //$arrLeagues[$fixture_status][$league_id]['list'][$fixture_id]['list'][$market_id]['list'][$market_bet_id] = array(
                $arrLeagues[$fixture_status][$league_id]['list'][$fixture_id]['list'][$market_id] = array(
                    'market_id' => $market_id,
                    'market_name' => $market_name,
                    'priority' => $market_priority,
                    'main_line' => $market_main_line,
                    'list' => $arr_market_bet,
                );

            }

        }

        ///////////////////////////////

        $arrRet = array();  //用於回傳結果

        // early 早盤
        $arrRet['early'][$sport_id] = array(
            'sport_id' => $sport_id,
            'sport_name' => $sport_name,
            'list' => $arrLeagues[$this->fixture_status['early']],
        );

        // living 走地
        $arrRet['living'][$sport_id] = array(
            'sport_id' => $sport_id,
            'sport_name' => $sport_name,
            'list' => $arrLeagues[$this->fixture_status['living']],
        );


        ///////////////////////////////
        $data = $arrRet;

        // gzip
        if (!isset($input['is_gzip']) || ($input['is_gzip']==1)) {  // 方便測試觀察輸出可以開關gzip
            $data = $this->gzip($data);
            $this->ApiSuccess($data, "01", true);
        } else {
            $this->ApiSuccess($data, "01", false);
        }
    }

    public function MatchIndex2(Request $request) {
      
    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        //---------------------------------
        // 取得代理的語系
        $player_id = $input['player'];
        $agent_lang = $this->getAgentLang($player_id);

        //////////////////////////////////////////

        if (!isset($input['sport_id'])) {
            $this->ApiError("01");
        }
        $sport_id = $input['sport_id'];


        //////////////////////////////////////////

        //取2天內賽事
        $today = time();
        $after_tomorrow_es = $today + 2 * 24 * 60 * 60; 
        $after_tomorrow_es = '"'.date('Y-m-d', $after_tomorrow_es).'T00:00:00"'; // 這個「"」不能拿掉, es會報錯

        //////////////////////////////////////////
        // ES取得賽事

        $return = LsportFixture::query()
        ->from('es_lsport_fixture')
        ->where('start_time',"<=", $after_tomorrow_es)
        ->whereIn('status',[1,2,9])
        ->whereIn('sport_id', function($query) {
            $query->select('sport_id')
                  ->from('es_lsport_sport')
                  ->where('sport_id', $sport_id)
                  ->where('status', 1);
        })
        ->whereIn('league_id', function($query) {
            $query->select('league_id')
                  ->from('es_lsport_league')
                  ->where('status', 1);
        })
        ->orderBy("start_time","ASC")
        ->orderBy("league_id", "ASC")

        ->list(5);
        if ($return === false) {
            $this->ApiError('02');
        }

        $fixture_data = $return;

        //////////////////////////////////////////
        
        $status_type = ["","early","living"];

        //////////////////////////////////////////
        $data = array();
        foreach ($fixture_data as $k => $v) {

            $sport_id = $v['sport_id'];
            $league_id = $v['league_id'];
            $fixture_id = $v['fixture_id'];
            $home_team_id = $v['home_id'];
            $away_team_id = $v['away_id'];
            $start_time = $v['start_time'];

            // 區分living, early
            $status = $v['status'];
            if ($status == 9) {
                $status = 2;
            }
            $status_type_name = $status_type[$status];
            
            // 取得球類
            $sport_name = LsportSport::getName(['sport_id' => $sport_id, "api_lang" => $agent_lang]);
            $data[$status_type_name][$sport_id]['sport_id'] = $sport_id;
            $data[$status_type_name][$sport_id]['sport_name'] = $sport_name;

            // 取得聯賽
            $league_name = LsportLeague::getName(['league_id' => $league_id, "api_lang" => $agent_lang]);
            $data[$status_type_name][$sport_id]['list'][$league_id]['league_id'] = $league_id;
            $data[$status_type_name][$sport_id]['list'][$league_id]['league_name'] = $league_name;

            // fixture columns
            $columns = ["fixture_id","start_time","status","last_update"];
            foreach ($columns as $kk => $vv) {
                $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id][$vv] = $v[$vv];
            }

            // home_team_name
            $team_name = LsportTeam::getName(['team_id' => $home_team_id, "api_lang" => $agent_lang]);
            $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['home_team_id'] = $home_team_id;
            $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['home_team_name'] = $team_name;

            // away_team_name
            $team_name = LsportTeam::getName(['team_id' => $away_team_id, "api_lang" => $agent_lang]);
            $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['away_team_id'] = $away_team_id;
            $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['away_team_name'] = $team_name;

            // 比分版資料
            $livescore_extradata = $v['livescore_extradata'];
            $periods = $v['periods'];
            $scoreboard = $v['scoreboard'];

            $parsed_periods = $this->getMatchPeriods($sport_id, $status, $scoreboard, $livescore_extradata);
            $parsed_scoreboard = $this->getMatchScoreboard($sport_id, $status, $periods, $scoreboard);

            $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['periods'] = $parsed_periods;
            $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['scoreboard'] = $parsed_scoreboard;

            // market_bet_count , set default = 0 
            $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['market_bet_count'] = 0;

            // order_by
            $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['order_by'] = strtotime($start_time);

            // 取得market 
            $return = LsportMarket::where("fixture_id",$fixture_id)->orderBy('market_id', 'ASC')->list();
            if ($return === false) {
                $this->ApiError('03');
            }

            $market_data = $return;
            foreach ($market_data as $kk => $vv) {
                $market_id = $vv['market_id'];
                $market_main_line = $vv['main_line'];
                $market_priority = $vv['priority'];

                // set data
                $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['list'][$market_id]['market_id'] = $market_id;
                $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['list'][$market_id]['priority'] = $market_priority;
                $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['list'][$market_id]['main_line'] = $market_main_line;

                // 設定market name
                $market_name = $vv['name_en'];
                if (isset($vv['name_'.$agent_lang]) && ($vv['name_'.$agent_lang] != null) && ($vv['name_'.$agent_lang] != "")) { 
                    $market_name = $vv['name_'.$agent_lang];
                } 

                // 取得market_bet
                $return = LsportMarketBet::where('fixture_id',$fixture_id)
                ->where("market_id",$market_id)
                ->where("base_line",'"'.$market_main_line.'"')  // main line 有時是空值, 要帶 "
                ->orderBy("name_en","ASC")
                ->list();
                if ($return === false) {
                    $this->ApiError('04');
                }

                $market_bet_data = $return;
                foreach ($market_bet_data as $kkk => $vvv) {
                    $market_bet_id = $vvv['bet_id'];

                    // TODO
                    $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['market_bet_count']++;

                    // 設定market_bet_name
                    $market_bet_name = $vvv['name_en'];
                    if (isset($vvv['name_'.$agent_lang]) && ($vvv['name_'.$agent_lang] != null) && ($vvv['name_'.$agent_lang] != "")) { 
                        $market_bet_name = $vvv['name_'.$agent_lang];
                    } 

                    $tmp_data = array();
                    $tmp_data['market_bet_id'] = $market_bet_id;
                    $tmp_data['market_bet_name'] = $market_bet_name;
                    $tmp_data['market_bet_name_en'] = $vvv['name_en'];
                    $tmp_data['line'] = $vvv['line'];
                    $tmp_data['price'] = $vvv['price'];
                    $tmp_data['status'] = $vvv['status'];
                    $tmp_data['last_update'] = $vvv['last_update'];
                    
                    $data[$status_type_name][$sport_id]['list'][$league_id]['list'][$fixture_id]['list'][$market_id]['list'][] = $tmp_data;
                }

            }

        }
        
        //////////////////////////////////////////

        // gzip
        if (!isset($input['is_gzip']) || ($input['is_gzip']==1)) {  // 方便測試觀察輸出可以開關gzip
            $data = $this->gzip($data);
            $this->ApiSuccess($data, "01", true);
        } else {
            $this->ApiSuccess($data, "01", false);
        }
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
        $agent_lang = $this->getAgentLang($player_id);
        $lang_col = "name_".$agent_lang;

        //////////////////////////////////////////
        // 取得必要參數
        $player_id = $input['player'];
        $fixture_id = $input['fixture_id'];  
        $market_id = $input['market_id'];  
        $market_bet_id = $input['market_bet_id'];
        $player_rate = $input['bet_rate'];  //前端傳來的賠率
        $bet_amount = $input['bet_amount'];  //投注金額
        $is_better_rate = $input['better_rate'];  //是否自動接受更好的賠率(若不接受則在伺服器端賠率較佳時會退回投注)

        $sport_id = $this->default_sport_id ;  //球種ID
        if (isset($input['sport_id'])) {
            $sport_id = $input['sport_id'];
        }
        
        $order = array();
        
        // 參數檢查 TODO - 初步 隨便弄弄
        if ($bet_amount <= 0) {
            $this->ApiError("03");
        }

        // 取得用戶資料
        $return = Player::where("id", $player_id)->first();
        if ($return === false) {
            $this->ApiError("04");
        }

        // 如果用戶已停用
        if ($return['status'] == 0) {
            $this->ApiError("05");
        }

        $player_account = $return['account'];
        $currency_type = $return['currency_type'];
        $agent_id = $return['agent_id'];
        // 用戶風控級別,決定了注單延時的時間長短
        $player_risk_level = $return['risk_level'];

        // 判斷餘額是否足夠下注
        $player_balance = $return['balance'];
        if ($player_balance < $bet_amount) {
            $this->ApiError("06");
        }
        
        //////////////////////////////////////////
        // order data
        $order['player_id'] = $player_id;
        $order['player_name'] = $player_account;
        $order['currency_type'] = $currency_type;
        //////////////////////////////////////////

        // 取得商戶資料
        $return = Agent::where("id", $agent_id)->fetch();
        if ($return === false) {
            $this->ApiError("07");
        }

        // 如果商戶已停用
        if ($return['status'] == 0) {
            $this->ApiError("08");
        }

        $agent_account = $return['account'];

        //////////////////////////////////////////
        // order data
        $order['agent_id'] = $agent_id;
        $order['agent_name'] = $agent_account;
        //////////////////////////////////////////

        // 取得賽事資料
        $return = LsportFixture::where("fixture_id", $fixture_id)->where("sport_id", $sport_id)->fetch();
        if ($return === false) {
            $this->ApiError("09");
        }

        $fixture_data = $return;
        $league_id = $fixture_data['league_id'];
        $home_team_id = $fixture_data['home_id'];
        $away_team_id = $fixture_data['away_id'];

        //////////////////////////////////////////
        // order data
        $order['sport_id'] = $fixture_data['sport_id'];
        $order['fixture_id'] = $fixture_id;
        //////////////////////////////////////////

        // 取得聯賽資料
        $return = LsportLeague::where("league_id", $league_id)->where("sport_id", $sport_id)->fetch();
        if ($return === false) {
            $this->ApiError("10");
        }

        $league_data = $return;

        //////////////////////////////////////////
        // order data
        $order['league_id'] = $league_id;
        
        if (empty($league_data[$lang_col])) {
            $order['league_name'] = $league_data['name_en'];
        } else {
            $order['league_name'] = $league_data[$lang_col];
        }

        //////////////////////////////////////////
        
        // 主隊
        $return = LsportTeam::where("team_id", $home_team_id)->fetch();
        if ($return === false) {
            $this->ApiError("11");
        }
        //////////////////////////////////////////
        // order data
        $order['home_team_id'] = $return['team_id'];
        if (empty($return[$lang_col])) {
            $order['home_team_name'] = $return['name_en'];
        } else {
            $order['home_team_name'] = $return[$lang_col];
        }
        //////////////////////////////////////////
        
        // 客隊
        $return = LsportTeam::where("team_id", $away_team_id)->fetch();
        if ($return === false) {
            $this->ApiError("12");
        }

        //////////////////////////////////////////
        // order data
        $order['away_team_id'] = $return['team_id'];
        if (empty($return[$lang_col])) {
            $order['away_team_name'] = $return['name_en'];
        } else {
            $order['away_team_name'] = $return[$lang_col];
        }
        //////////////////////////////////////////

        // 取得玩法
        $market_data = LSportMarket::where("market_id", $market_id)->where("fixture_id", $fixture_id)->fetch();
        if ($market_data === false) {
            $this->ApiError("13");
        }

        $market_priority = $market_data['priority'];

        //////////////////////////////////////////
        // order data
        $order['market_id'] = $market_id;
        if (empty($market_data[$lang_col])) {
            $order['market_name'] = $market_data['name_en'];
        } else {
            $order['market_name'] = $market_data[$lang_col];
        }
        $order['market_priority'] = $market_priority;
        $order['player_rate'] = $player_rate;
        $order['better_rate'] = ($is_better_rate)?(1):(0);
        $order['market_bet_id'] = $market_bet_id;
        //////////////////////////////////////////

        // 風控大單
        $risk_order = $this->system_config['risk_order'];

        // 計算風控大單功能是否啟動:
        // 1) 有risk_order這參數 2) 且risk_order大於零 3) 且投注額大於等於risk_order
        $is_risk_order = (!empty($risk_order) && ($risk_order > 0) && ($bet_amount >= $risk_order));

        // 延時投注
        $bet_delay = $this->system_config['bet_delay'];
        $arr_bet_delay = json_decode($bet_delay, true);
        // 延時投注功能是否啟動
        $is_bet_delay = (!empty($bet_delay) && !empty($arr_bet_delay));

        if ($is_risk_order) {  // 風控大單功能已啟動
            $default_order_status = $this->game_order_status['wait_for_audit'];
            $default_approval_time = null;
            $default_delay_datetime = null;
        } else { // 風控大單功能未啟動

            // 延時投注功能(風控大單優先於延時投注)
            if ($is_bet_delay) {  // 延時投注功能已啟動
                $default_order_status = $this->game_order_status['delay_bet'];
                //建立延時注單時以下欄位應該留空: approval_time, bet_rate
                $default_approval_time = null;

                // 寫入延時注單的delay_time
                // 先抓出用戶的風控級別以決定延時秒數
                $arr_bet_delay = json_decode($bet_delay);
                // 計算延時秒數. 在此以防萬一找不到風控秒數時,預設10秒.
                $delay_sec = isset($arr_bet_delay[$player_risk_level]) ? ($arr_bet_delay[$player_risk_level]) : (10);
                // 算出注單的延時到期時間(DB欄位:delay_time)
                $delay_time = (time() + $delay_sec);

                $default_delay_datetime = date('Y-m-d H:i:s', $delay_time);
            } else {  // 風控大單,延時投注均未啟動
                // 通過
                $default_order_status = $this->game_order_status['wait_for_result'];
                $default_approval_time = date("Y-m-d H:i:s");
                $default_delay_datetime = null;
            }
        }

        // 取得賠率
        $market_bet_data = LSportMarketBet::where("fixture_id", $fixture_id)
        ->where("bet_id", $market_bet_id)
        ->whereIn('provder_bet_id', [0,8]) // 號源
        ->fetch();
        if ($market_bet_data === false) {
            $this->ApiError("14");
        }

        //////////////////////////////////////////
        // order data
        $market_bet_line = $market_bet_data['line'];
        $order['market_bet_line'] = $market_bet_line;
        if (empty($market_bet_data[$lang_col])) {
            $order['market_bet_name'] = $market_bet_data['name_en'];
        } else {
            $order['market_bet_name'] = $market_bet_data[$lang_col];
        }
        //////////////////////////////////////////


        //////////////////////////////////////////
        // 建立延時注單時或風控大單以下欄位應該留空: approval_time, bet_rate
        // 風控大單啟動 --> '不'取賠率資料寫入注單, 賠率資料留到運營審核通過當下才會取
        // 延時投注啟動 --> '不'取賠率資料寫入注單, 賠率資料留到delay_time到期才會取

        /*
        風控大單 YES 延時投注 YES: '不'取賠率資料
        風控大單 YES 延時投注 NO:  '不'取賠率資料
        風控大單 NO 延時投注 YES:  '不'取賠率資料
        風控大單 NO 延時投注 NO:  取賠率資料
        */
        if ($is_risk_order == true) {
            if ($is_bet_delay == true) {
                //////////////////////////////////////////
                // order data
                $order['bet_rate'] = null;
                //////////////////////////////////////////
            }
            // $is_risk_order: false AND $is_bet_delay=true
            else {
                //////////////////////////////////////////
                // order data
                $order['bet_rate'] = null;
                //////////////////////////////////////////
            }
        }
        // $is_risk_order: false
        else {
            if ($is_bet_delay == true) {
                //////////////////////////////////////////
                // order data
                $order['bet_rate'] = null;
                //////////////////////////////////////////
            }
            // $is_risk_order: false AND $is_bet_delay=false
            else {
                $current_market_bet_status = $market_bet_data['status'];
                $current_market_bet_rate = $market_bet_data['price'];

                // 賠率非開盤狀態 1开、2锁、3结算
                if (($current_market_bet_status != 1)) {
                    $this->ApiError("15");
                }

                // 判斷 is_better_rate
                if (($is_better_rate == 1) && ($current_market_bet_rate < $player_rate)) {
                    $this->ApiError("16");
                }

                //////////////////////////////////////////
                // order data
                $order['bet_rate'] = $current_market_bet_rate;
                //////////////////////////////////////////
            }
        }

        //////////////////////////////////////////
        // order data
        $order['bet_amount'] = $bet_amount;
        $order['status'] = $default_order_status;
        $order['create_time'] = date("Y-m-d H:i:s");

        $order['approval_time'] = $default_approval_time;
        $order['delay_time'] = $default_delay_datetime;

        //////////////////////////////////////////

        // 新增注單資料
        $return = GameOrder::insertGetId($order);      
        if ($return === false) {
            $this->ApiError("17");
        }

        $order_id = $return;
        // 設定m_id 
        $return = GameOrder::where("id", $order_id)->update([
            "m_id" => $order_id
        ]);
        if ($return === false) {
            $this->ApiError("18");
        }
        
        // 扣款
        $before_amount = $player_balance;
        $change_amount = $bet_amount;
        $after_amount = $before_amount - $change_amount;

        $return = Player::where("id", $player_id)->update([
            "balance" => $after_amount
        ]);
        if ($return === false) {
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

        ///////////////////////////////////
        $data = $order_id;

        $this->ApiSuccess($data, "01");

    }

    public function mGameBet(Request $request) {
      
    	$input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        /////////////////////////
        // 檢查必要input欄位
        $columns = array(
            "token","player","sport_id","bet_data","bet_amount",
        );

        foreach ($columns as $k => $v) {
            if (!isset($input[$v])) {
                $this->ApiError("01");
            }
        }

        //---------------------------------
        // 取得代理的語系
        $player_id = $input['player'];
        $agent_lang = $this->getAgentLang($player_id);
        $lang_col = 'name_' . $agent_lang;

        //////////////////////////////////////////
        // 取得必要參數
        $player_id = $input['player'];
        $bet_amount = $input['bet_amount'];  //投注金額
        $is_better_rate = (empty($input['better_rate']) === false);  //是否自動接受更好的賠率(若不接受則在伺服器端賠率較佳時會退回投注)

        $sport_id = $this->default_sport_id;
        if (isset($input['sport_id'])) {
            $sport_id = $input['sport_id'];
        }

        $arr_bet_data = json_decode($input['bet_data'], true);

        // 參數檢查 TODO - 初步 隨便弄弄

        //串關的注單數不能低於2
        if (sizeof($arr_bet_data) < 2) {
            $this->ApiError("03");
        }

        // 投注金額要超過0
        if ($bet_amount <= 0) {
            $this->ApiError("04");
        }

        // 取得用戶資料
        $return = Player::where("id", $player_id)->fetch();
        if ($return === false) {
            $this->ApiError("05");
        }

        // 如果用戶已停用
        if ($return['status'] == 0) {
            $this->ApiError("06");
        }

        $player_account = $return['account'];
        $player_currency_type = $return['currency_type'];
        $agent_id = $return['agent_id'];
        // 用戶風控級別,決定了注單延時的時間長短
        $player_risk_level = $return['risk_level'];

        // 判斷餘額是否足夠下注
        $player_balance = $return['balance'];
        if ($player_balance < $bet_amount) {
            $this->ApiError("07");
        }

        // 判斷下注額度是否超過限額
        // ...
        
        // 取得商戶資料
        $return = Agent::where("id", $agent_id)->fetch();
        if ($return === false) {
            $this->ApiError("08");
        }

        // 如果商戶已停用
        if ($return['status'] == 0) {
            $this->ApiError("09");
        }

        $agent_account = $return['account'];

        //////////////////////////////////////////
        // 風控大單
        $risk_order = $this->system_config['risk_order'];

        // 計算風控大單功能是否啟動: 
        // 1) 有risk_order這參數 2) 且risk_order大於零 3) 且投注額大於等於risk_order
        $is_risk_order = (!empty($risk_order) && ($risk_order > 0) && ($bet_amount >= $risk_order));

        // 延時投注
        $bet_delay = $this->system_config['bet_delay'];
        $arr_bet_delay = json_decode($bet_delay, true);
        // 延時投注功能是否啟動
        $is_bet_delay = (!empty($bet_delay) && !empty($arr_bet_delay));

        if ($is_risk_order) {  // 風控大單功能已啟動
            $default_order_status = $this->game_order_status['wait_for_audit'];
            $default_approval_time = null;
            $default_delay_datetime = null;
        } else { // 風控大單功能未啟動
            // 延時投注功能(風控大單優先於延時投注)
            if ($is_bet_delay) {  // 延時投注功能已啟動
                $default_order_status = $this->game_order_status['delay_bet'];
                //建立延時注單時以下欄位應該留空: approval_time, bet_rate
                $default_approval_time = null;

                // 寫入延時注單的delay_time
                // 先抓出用戶的風控級別以決定延時秒數
                $arr_bet_delay = json_decode($bet_delay);

                // 計算延時秒數. 在此以防萬一找不到風控秒數時,預設10秒.
                if (isset($arr_bet_delay[$player_risk_level])) {
                    $delay_sec = $arr_bet_delay[$player_risk_level];
                } else {
                    $delay_sec = 10;
                }
                
                // 算出注單的延時到期時間(DB欄位:delay_time)
                $delay_time = (time() + $delay_sec);
                $default_delay_datetime = date('Y-m-d H:i:s', $delay_time);
            } else {  // 風控大單,延時投注均未啟動
                // 通過
                $default_order_status = $this->game_order_status['wait_for_result'];
                $default_approval_time = date("Y-m-d H:i:s");
                $default_delay_datetime = null;
            }
        }

        // 取第一筆注單ID做為串關注單ID
        $m_order_id = false;

        // 取第一筆串關注單的 sport_id
        //用來檢查串關注單是否都是同球種
        $m_sport_id = false;

        // 串關批量處理訂單
        foreach ($arr_bet_data AS $k => $v) {

            // 檢查必要欄位
            $columns = array(
                "fixture_id", "market_id", "market_bet_id", "bet_rate"
            );
    
            foreach ($columns as $kk => $vv) {
                if (!isset($v[$vv])) {
                    $this->ApiError("10");
                }
            }

            // 取得必要參數
            $fixture_id = $v['fixture_id'];
            $market_id = $v['market_id'];  
            $market_bet_id = $v['market_bet_id'];
            $player_rate = $v['bet_rate'];  //前端傳來的賠率

            //////////////////////////////////////////
            // order data
            $order = array(
                'm_order' => 1,  // 1=屬於串關注單
                'm_id' => null,  // 同一串關注單有同一值=第一筆寫入的注單ID

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

                'market_id' => $market_id,
                'market_name' => null,
                'market_priority' => null,

                'market_bet_id' => $market_bet_id,
                'market_bet_name' => null,
                'market_bet_line' => null,
                'bet_rate' => null,

                'player_rate' => $player_rate,
                'better_rate' => ($is_better_rate)?(1):(0),
                'bet_amount' => $bet_amount,
                'status' => $default_order_status,
                'create_time' => date("Y-m-d H:i:s"),
                'approval_time' => $default_approval_time,
                'delay_time' => $default_delay_datetime,
            );

            /////////////////////////////
            // 取得賽事資料
            $fixture_data = LsportFixture::where("fixture_id", $fixture_id)->where("sport_id", $sport_id)->fetch();
            if ($fixture_data === false) {
                $this->ApiError("11");
            }
            //////////////////////////////////////////
            // order data
            $order['fixture_id'] = $fixture_id;

            // 用以判斷注單 是否為同一sport_id
            if ($m_sport_id === false) {
                $m_sport_id = $fixture_data['sport_id'];
            }

            //串關注單全部的sport_id都要一樣 (不能跨球種)
            if ($m_sport_id != $fixture_data['sport_id']) {
                $this->ApiError("12");
            }

            //////////////////////////////////////////
            // order data
            $order['sport_id'] = $fixture_data['sport_id'];

            //fixture status : 1未开始、2进行中 
            // 串關只能賽前注單,不得是走地滾球
            if ($fixture_data['status'] != 1) {
                $this->ApiError("13");
            }
    
            $league_id = $fixture_data['league_id'];
            $home_team_id = $fixture_data['home_id']; 
            $away_team_id = $fixture_data['away_id']; 
    
            // 取得聯賽資料
            $league_name = LsportLeague::getName(['league_id'=>$league_id, 'api_lang'=>$agent_lang]);
            $order['league_id'] = $league_id;
            $order['league_name'] = $league_name;

            //////////////////////////////////////////
            // 取得隊伍資料
            $home_team_name = LsportTeam::getName(['team_id'=>$home_team_id, 'api_lang'=>$agent_lang]);
            $order['home_team_id'] = $home_team_id;
            $order['home_team_name'] = $home_team_name;
            
            // 客隊
            $away_team_name = LsportTeam::getName(['team_id'=>$away_team_id, 'api_lang'=>$agent_lang]);
            $order['away_team_id'] = $away_team_id;
            $order['away_team_name'] = $away_team_name;

            //////////////////////////////////////////
            // 取得玩法
            $market_data = LSportMarket::where("market_id", $market_id)->where("fixture_id", $fixture_id)->fetch();
            if ($market_data === false) {
                $this->ApiError("17");
            }
            $market_priority = $market_data['priority'];

            // 取得賠率
            $market_bet_data = LSportMarketBet::where("fixture_id", $fixture_id)
            ->whereIn("provder_bet_id", [0,8])
            ->where("bet_id", $market_bet_id)
            ->fetch();
            if ($market_bet_data === false) {
                $this->ApiError("18");
            }

            $market_bet_line = $market_bet_data['line'];
            $order['market_bet_line'] = $market_bet_line;
            $order['market_bet_name'] = $market_bet_data['name_en'];
            if (!empty($market_bet_data[$lang_col])) {
                $order['market_bet_name'] = $market_bet_data[$lang_col];
            }

            //////////////////////////////////////////
            // 建立延時注單時或風控大單以下欄位應該留空: approval_time, bet_rate
            // 風控大單啟動 --> '不'取賠率資料寫入注單, 賠率資料留到運營審核通過當下才會取
            // 延時投注啟動 --> '不'取賠率資料寫入注單, 賠率資料留到delay_time到期才會取

            /*
            風控大單 YES 延時投注 YES: '不'取賠率資料
            風控大單 YES 延時投注 NO:  '不'取賠率資料
            風控大單 NO 延時投注 YES:  '不'取賠率資料
            風控大單 NO 延時投注 NO:  取賠率資料
            */
            if ($is_risk_order == true) {
                if ($is_bet_delay == true) {
                    //////////////////////////////////////////
                    // order data
                    $order['bet_rate'] = null;
                    //////////////////////////////////////////
                }
                // $is_risk_order: false AND $is_bet_delay=true
                else {
                    //////////////////////////////////////////
                    // order data
                    $order['bet_rate'] = null;
                    //////////////////////////////////////////
                }
            }
            // $is_risk_order: false
            else {
                if ($is_bet_delay == true) {
                    //////////////////////////////////////////
                    // order data
                    $order['bet_rate'] = null;
                    //////////////////////////////////////////
                }
                // $is_risk_order: false AND $is_bet_delay=false
                else {
                    $current_market_bet_status = $market_bet_data['status'];
                    $current_market_bet_rate = $market_bet_data['price'];
    
                    // 賠率非開盤狀態 1开、2锁、3结算
                    if (($current_market_bet_status != 1)) {
                        $this->ApiError("19");
                    }
    
                    // 判斷 is_better_rate
                    if (($is_better_rate == 1) && ($current_market_bet_rate < $player_rate)) {
                        $this->ApiError("20");
                    }

                    //////////////////////////////////////////
                    // order data
                    $order['bet_rate'] = $current_market_bet_rate;
                    //////////////////////////////////////////
                }
            }

            //////////////////////////////////////////
            // order data
            if (empty($market_data[$lang_col])) {
                $order['market_name'] = $market_data['name_en'];
            } else {
                $order['market_name'] = $market_data[$lang_col];
            }
            $order['market_priority'] = $market_priority;

            //////////////////////////////////////////
            // 判斷 is_better_rate
            if (($is_better_rate == 1) && ($current_market_bet_rate < $player_rate)) {
                $this->ApiError("21");
            }

            //////////////////////////////////////////
            // m_id
            if ($m_order_id !== false) { 
                $order['m_id'] = $m_order_id;  // 同一串關注單m_id均相同(第一筆寫入的注單ID)
            }

            //////////////////////////////////////////
            // 新增注單
            $new_order_id = GameOrder::insertGetId($order);      
            if ($new_order_id === false) {
                $this->ApiError("22");
            }

            // 若是第一筆注單設定m_id
            if ($m_order_id === false) {
                $m_order_id = $new_order_id;
                //更新第一筆注單的m_id = 自己的id
                $return = GameOrder::where("id", $m_order_id)->update(["m_id" => $m_order_id]);
                if ($return === false) {
                    $this->ApiError("23");
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
        if ($return === false) {
            $this->ApiError("24");
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

        ///////////////////////////////////
        $data = $m_order_id;

        $this->ApiSuccess($data, "01");

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

        //---------------------------------
        // 取得代理的語系
        $player_id = $input['player'];
        $agent_lang = $this->getAgentLang($player_id);
        $lang_col = 'name_' . $agent_lang;

        /////////////////////////
        // 輸入判定
        if (!isset($input['sport']) || ($input['sport'] == "")) {
            $input['sport'] = $this->default_sport_id;  // 預設1 , 足球
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
        $return = LsportFixture::where("sport_id", $sport_id)
        ->whereIn("status", [3,4,5,6,7])
        ->orderBy("start_time","DESC")
        ->list();
        if ($return === false) {
            $this->ApiError('02');
        }

        $fixture_data = $return;

        $reponse = array();
        foreach ($fixture_data as $k => $v) {

            $tmp = array();
            $tmp['fixture_id'] = $v['fixture_id'];
            $tmp['start_time'] = $v['start_time'];
            $tmp['status'] =$v['status'];
            $tmp['status_name'] = $fixture_status[$v['status']];
            $tmp['last_update'] = $v['last_update'];

            ///////////////////////

            // league
            $league_id = $v['league_id'];
            $league_name = LsportLeague::getName(['league_id'=>$league_id, 'api_lang'=>$agent_lang]);
            $tmp['league_id'] = $league_id;
            $tmp['league_name'] = $league_name;

            // sport: 
            $sport_id = $v['sport_id'];
            $sport_name = LsportSport::getName(['sport_id'=>$sport_id, 'api_lang'=>$agent_lang]);
            $tmp['sport_id'] = $sport_id;
            $tmp['sport_name'] = $sport_name;

            // home_team: 
            $home_team_id = $v['home_id'];
            $home_team_name = LsportTeam::getName(['team_id'=>$home_team_id, 'api_lang'=>$agent_lang]);
            $tmp['home_team_id'] = $home_team_id;
            $tmp['home_team_name'] = $home_team_name;

            // away_team: 
            $away_team_id = $v['away_id'];
            $away_team_name = LsportTeam::getName(['team_id'=>$away_team_id, 'api_lang'=>$agent_lang]);
            $tmp['away_team_id'] = $away_team_id;
            $tmp['away_team_name'] = $away_team_name;
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
                    if ($type <= 50) {  // 粗暴判斷
                        $scoreboard[] = $d;
                    }
                }
            }

            $tmp['scoreboard'] = $scoreboard;
            ////////////

            $reponse[] = $tmp;
        }

        $data = $reponse;

        ///////////////////////////////////
        // gzip
        $data = $this->gzip($data);

        $this->ApiSuccess($data, "01", true); 
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

        //---------------------------------
        // 處理輸入
        $necessary_inputs = array('player', 'sport_id', 'fixture_id');  // 必要輸入欄位名稱
        foreach ($necessary_inputs as $nk => $input_name) {
            if (empty($input[$input_name])) {
                $this->ApiError('01');
            }
        }
        $player_id = $input['player'];
        $fixture_id = $input['fixture_id'];
        $sport_id = $input['sport_id'];

        //---------------------------------
        // 取得代理的語系
        $agent_lang = $this->getAgentLang($player_id);
        $lang_col = 'name_' . $agent_lang;

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //依照 sport_id, fixture_id 取出單場"賽事+聯賽+球種+主隊+客隊"資料
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
                'l.name_en AS l_name_en', 'l.'.$lang_col.' AS l_name_locale',
                's.name_en AS s_name_en', 's.'.$lang_col.' AS s_name_locale',
                'f.fixture_id', 'f.sport_id', 'f.league_id', 'f.start_time', 'f.livescore_extradata', 'f.periods', 'f.scoreboard', 'f.status AS f_status', 'f.last_update AS f_last_update',
                'th.team_id AS th_team_id', 'th.name_en AS th_name_en', 'th.'.$lang_col.' AS th_name_locale',
                'ta.team_id AS ta_team_id', 'ta.name_en AS ta_name_en', 'ta.'.$lang_col.' AS ta_name_locale'
            )
            ->where('l.status', 1)
            ->where('l.sport_id', $sport_id)
            ->where('f.fixture_id', $fixture_id)
            ->first();

        if ($data === false) {
            $this->ApiError('02');
        }

        // dd($data);

        $sport_name = null;  //儲存球種名稱

        //////////////////////////////////////////
        // 開始loop 賽事資料

        $league_id = $data->league_id;
        $fixture_id = $data->fixture_id;
        // $market_id = $data->market_id;
        // $main_line = $data->main_line;
        $fixture_status = intval($data->f_status);
        $arrFixture = array();

        // sport_name: 判斷用戶語系資料是否為空,若是則用en就好
        if (empty($sport_name)) {  //只須設定一次
            if (empty($data->s_name_locale)) {  // sport name
                $sport_name = $data->s_name_en;
            } else {
                $sport_name = $data->s_name_locale;
            }
        }

        // league 層 ----------------------------

        // league_name: 判斷用戶語系資料是否為空,若是則用en就好
        if (empty($data->l_name_locale)) {  // league name
            $league_name = $data->l_name_en;
        } else {
            $league_name = $data->l_name_locale;
        }

        // 包入 league 聯賽資料
        $arrFixture['series'] = array(
            'league_id' => $league_id,
            'sport_id' => $sport_id,
            'name' => $league_name,
        );

        // fixture 層 ----------------------------

        // home_team_name: 判斷用戶語系資料是否為空,若是則用en就好
        if (empty($data->th_name_locale)) {  // home team
            $home_team_name = $data->th_name_en;
        } else {
            $home_team_name = $data->th_name_locale;
        }
        // away_team_name: 判斷用戶語系資料是否為空,若是則用en就好
        if (empty($data->ta_name_locale)) {  // away_team
            $away_team_name = $data->ta_name_en;
        } else {
            $away_team_name = $data->ta_name_locale;
        }

        // 比分板跟各局得分
        $livescore_extradata = $data->livescore_extradata;
        $periods = $data->periods;
        $scoreboard = $data->scoreboard;
        //解析以回傳
        $parsed_periods = $this->getMatchPeriods($sport_id, $fixture_status, $scoreboard, $livescore_extradata);
        $parsed_scoreboard = $this->getMatchScoreboard($sport_id, $fixture_status, $periods, $scoreboard);

        // 主客隊分數
        $home_team_total_score = null;  //主隊總分
        $arr_home_team_scores = array();  //主隊比分板
        $away_team_total_score = null;  //客隊總分
        $arr_away_team_scores = array();  //客隊比分板

        // for: list.teams.scores & list.teams.total_score
        if ($parsed_scoreboard) {
            //主隊---------

            //總分
            $home_team_total_score = $parsed_scoreboard[1][0];

            //比分板
            foreach ($parsed_scoreboard[1] as $sk => $sv) {
                $stage = intval($sk);
                $score = intval($sv);
                if (($stage >= 1) && ($stage <= 50)) {
                    $arr_home_team_scores[] = array(
                        'stage' => $stage,
                        'score' => $score,
                    );
                }
            }
            //客隊---------

            //總分
            $away_team_total_score = $parsed_scoreboard[2][0];

            //比分板
            foreach ($parsed_scoreboard[2] as $sk => $sv) {
                $stage = intval($sk);
                $score = intval($sv);
                if (($stage >= 1) && ($stage <= 50)) {
                    $arr_away_team_scores[] = array(
                        'stage' => $stage,
                        'score' => $score,
                    );
                }
            }
        }

        // 包入 fixture 賽事資料 ---------------
        $arrFixture['list'] = array(
            'fixture_id' => $data->fixture_id,
            'home_team_id' => $data->th_team_id,
            'home_team_name' => $home_team_name,
            'away_team_id' => $data->ta_team_id,
            'away_team_name' => $away_team_name,
            'start_time' => $data->start_time,
            'status' => $fixture_status,
            'last_update' => $data->f_last_update,
            'periods' => $parsed_periods,
            'scoreboard' => $parsed_scoreboard,
            'series' => array(
                'id' => $data->league_id,
                'sport_id' => $sport_id,
                'name' => $league_name,
            ),
            'teams' => array(
                // 主隊
                array(
                    'index' => 2,
                    'total_score' => $home_team_total_score,
                    'scores' => $arr_home_team_scores,
                    'team' => array(
                        'team_id' => $data->th_team_id,
                        'sport_id' => $sport_id,
                        'name' => $home_team_name,
                    )
                ),
                // 客隊
                array(
                    'index' => 1,
                    'total_score' => $away_team_total_score,
                    'scores' => $arr_away_team_scores,
                    'team' => array(
                        'team_id' => $data->ta_team_id,
                        'sport_id' => $sport_id,
                        'name' => $away_team_name,
                    )
                ),
            ),
            'market' => array(),
        );

        //market 層 ----------------------------

        //取出賽事的玩法 ----------------------------
        $marketData = DB::table('lsport_market as m')
        ->select(
            'm.market_id',
            'm.name_en AS m_name_en',
            'm.'.$lang_col.' AS m_name_locale',
            'm.priority',
            'm.main_line'
        )
        ->where('m.fixture_id', $fixture_id)
        ->orderBy('m.market_id', 'ASC')
        ->get();

        if ($marketData === false) {
            $this->ApiError('03');
        }

        // 開始繞玩法資料
        foreach ($marketData as $k => $v) {

            $market_id = $v->market_id;
            $main_line = $v->main_line;

            // // market_name: 判斷用戶語系資料是否為空,若是則用en就好
            if (empty($v->m_name_locale)) {  // market name
                $market_name = $v->m_name_en;
            } else {
                $market_name = $v->m_name_locale;
            }

            //取出[賽事+玩法+玩法.base_line]的賠率 ----------------------------
            $market_bet_data = DB::table('lsport_market_bet as mb')
            ->select(
                'mb.bet_id',
                'mb.base_line',
                'mb.line',
                'mb.name_en AS mb_name_en',
                'mb.'.$lang_col.' AS mb_name_locale',
                'mb.price',
                'mb.status AS status',
                'mb.last_update AS last_update',
            )
            ->where('mb.fixture_id', $fixture_id)
            ->where('mb.market_id', $market_id)
            ->whereIn("mb.provder_bet_id", [0,8])
            ->where('mb.base_line', $main_line)  //這邊用 base_line 或 line 都可以
            ->orderBy('mb.bet_id', 'ASC')  //注意排序
            ->get();

            if ($market_bet_data === false) {
                $this->ApiError('04');
            }

            // 目前賽事的賠率資料
            $arr_market_bet = array();

            // 開始繞賠率資料
            foreach ($market_bet_data as $bk => $bv) {
                $market_bet_id = $bv->bet_id;

                // market_bet_name: 判斷用戶語系資料是否為空,若是則用en就好
                $market_bet_name_en = $bv->mb_name_en;
                if (empty($bv->mb_name_locale)) {  // market name
                    $market_bet_name = $bv->mb_name_en;
                } else {
                    $market_bet_name = $bv->mb_name_locale;
                }

                // 組合 market_bet 賠率資料 ---------------
                $arr_market_bet[] = array(
                    'market_bet_id' => $market_bet_id,
                    'market_bet_name' => $market_bet_name,
                    'market_bet_name_en' => $market_bet_name_en,
                    //'base_line' => $bv->base_line,
                    'line' => $bv->line,
                    'price' => $bv->price,
                    'status' => $bv->status,
                    'last_update' => $bv->last_update,
                );
            }

            // 包入 market 玩法資料 (含 market_bet 賠率資料) ---------------
            $arrFixture['list']['market'][] = array(
                'market_id' => $v->market_id,
                'market_name' => $market_name,
                'priority' => $v->priority,
                'main_line' => $v->main_line,
                'rate' => $arr_market_bet,
            );

        }

        ///////////////////////////////////
        // gzip
        $data = $arrFixture;
        $data = $this->gzip($data);

        $this->ApiSuccess($data, "01", true);
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

        //---------------------------------
        // 取得代理的語系
        $player_id = $input['player'];
        $agent_lang = $this->getAgentLang($player_id);
        $lang_col = 'name_' . $agent_lang;

        //////////////////////////////////////////

        if (!isset($input['page'])) {
            $input['page'] = 1;
        }

        if (!isset($input['result'])) {
            $input['result'] = 0;
        }

        //////////////////////////////////////////

        $page_limit = $this->page_limit;
        $page = $input['page'];
        $skip = ($page-1)*$page_limit;

        //////////////////////////////////////////

        // 獲取注單資料
        $GameOrder = GameOrder::where("player_id", $input['player']);

        if (isset($input['result']) && ($input['result'] != "")) {
            
            // 未結算
            if ($input['result'] == 0) {
                $GameOrder = $GameOrder->whereIn("status",[0,1,2,3]);
            }
            
            // 已派獎
            if ($input['result'] == 1) {
                $GameOrder = $GameOrder->where("status",4);
            }
        }
            
        $return = $GameOrder
        ->skip($skip)
        ->take($page_limit)
        ->orderBy('m_id', 'DESC')
        ->groupBy('m_id')
        ->list();
        if ($return === false) {
            $this->ApiError("01");
        }

        $order_data = $return;
        $data = array();
        $tmp = array();

        /////////////////////////

        $columns = array(
            "id",
            "m_id",
            "bet_amount",
            "result_amount",
            "create_time",
            "result_time",
            "status"
        );

        foreach ($order_data as $k => $v) {
            foreach ($columns as $kk => $vv) {
                $tmp[$k][$vv] = $v[$vv]; 
            }

            $tmp[$k]['status'] = $v['status'];
            $tmp[$k]['m_order'] = $v['m_order'];

            $sport_id = $v["sport_id"];
            $home_team_id = $v["home_team_id"];
            $away_team_id = $v["away_team_id"];

            // 有串關資料
            if ($v['m_order'] == 1) {

                $return = GameOrder::where("m_id", $v['m_id'])->list();
                if ($return === false) {
                    $this->ApiError("02");
                }

                foreach ($return as $kkk => $vvv) {

                    $fixture_id = $vvv['fixture_id'];
                    $return = LsportFixture::where("fixture_id",$fixture_id)->fetch();
                    if ($return === false) {
                        $this->ApiError("03");
                    }
                 
                    $tmp_bet_data = $vvv;
                    $tmp_bet_data['start_time'] = $return['start_time'];
                    
                    $tmp[$k]['bet_data'][] = $tmp_bet_data;
                }
            } else {
                
                $fixture_id = $v['fixture_id'];
                $return = LsportFixture::where("fixture_id",$fixture_id)->fetch();
                if ($return === false) {
                    $this->ApiError("04");
                }

                $tmp_bet_data = $v;
                $tmp_bet_data['start_time'] = $return['start_time'];

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

        //---------------------------------
        // 取得代理的語系
        $player_id = $input['player'];
        $agent_lang = $this->getAgentLang($player_id);
        $lang_col = 'name_' . $agent_lang;

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
            ->list();  
        if ($return === false) {
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

        $data = json_encode($data, true);
        $compressedData = gzcompress($data);  // 使用 gzcompress() 函數進行壓縮
        $base64Data = base64_encode($compressedData);  // 使用 base64_encode() 函數進行 base64 編碼

        return $base64Data;
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

        $return = PlayerOnline::getToken(["player_id"=>$player_id, "token"=>$token]);

        if ($return === false) {
            return false;
        }
        
        if ($return == null) {
            return false;
        }

        return true;
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

    protected function getMatchScoreboard($sport_id, $fixture_status, $periods, $scoreboard) {

        // 如果還未開賽就回傳null
        if ($fixture_status < $this->fixture_status['living']) {
            return null;
        }

        // 目前只處理特定類型
        //if ($sport_id != default_sport_id) {
        if (!in_array($sport_id, $this->lsport_sport_id)) {
            return null;
        }

        //========================================
        // 處理傳入參數

        if (is_array($periods)) {
            $arr_periods = $periods;
        } else {
            // 如果參數是字串則json_decoe看看
            $arr_periods = json_decode($periods, true);
            // de不出東西就回傳false
            if (!is_array($arr_periods) && !$arr_periods) {
                return false;
            }
        }

        if (is_array($scoreboard)) {
            $arr_scoreboard = $scoreboard;
        } else {
            // 如果參數是字串則json_decoe看看
            $arr_scoreboard = json_decode($scoreboard, true);
            // de不出東西就回傳false
            if (!is_array($arr_scoreboard) && !$arr_scoreboard) {
                return false;
            }
        }

        //========================================

        $ret = array();

        // 處理總分
        $arr_results = $arr_scoreboard['Results'];
        foreach ($arr_results as $rk => $rv) {
            $pos = intval($rv['Position']);
            $total_score = intval($rv['Value']);
            //總分都是放在key=0的位置
            $ret[$pos][0] = $total_score;
        }

        // 各局得分
        // Position=40以上都不要計入
        foreach ($arr_periods as $pk => $pv) {
            $type = intval($pv['Type']);  // type=局數號碼
            $arr_results = $pv['Results'];
            foreach ($arr_results as $rk => $rv) {
                $pos = intval($rv['Position']);
                $score = intval($rv['Value']);
                if ($type <= 50) {  // 40 通常為加時，也要計入 (football的50是罰球)
                    $ret[$pos][$type] = $score;
                }
            }
        }

        //陣列依key值ASC排序,因為有時候type=40的會出現在其他較小的type之前
        foreach ($ret as $rk => &$rv) {
            ksort($rv);
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
     protected function getMatchPeriods($sport_id, $fixture_status, $scoreboard, $livescore_extradata) {

        // 如果還未開賽就回傳null
        $fixture_status = intval($fixture_status);
        if ($fixture_status < $this->fixture_status['living']) {
            return null;
        }

        // 目前只處理特定類型
        //if ($sport_id != default_sport_id) {
        if (!in_array($sport_id, $this->lsport_sport_id)) {
            return null;
        }

        //========================================
        // 處理傳入參數

        if (is_array($scoreboard)) {
            $arr_scoreboard = $scoreboard;
        } else {
            // 如果參數是字串則json_decoe看看
            $arr_scoreboard = json_decode($scoreboard, true);
            // de不出東西就回傳false
            if (!is_array($arr_scoreboard) && !$arr_scoreboard) {
                return false;
            }
        }

        if (is_array($livescore_extradata)) {
            $arr_livescore_extradata = $livescore_extradata;
        } else {
            // 如果參數是字串則json_decoe看看
            $arr_livescore_extradata = json_decode($livescore_extradata, true);
            // de不出東西就回傳false
            if (!is_array($arr_livescore_extradata) && !$arr_livescore_extradata) {
                return false;
            }
        }

        //========================================

        // 以下ex.棒球
        $ret = array(
            "period" => null, // 第幾局
            // "turn" : 1, // 1為下, 2為上
            // "balls" : 1 // 壞球數, 
            // "strikes" : 1 , '' 好球數
            // "outs" : 1 , // 出局數
            // "bases" : "1/1/1"   // 1,2,3 壘是否有人
        );

        // 當前局數
        $ret['period'] = $arr_scoreboard['CurrentPeriod'];

        // 各種比賽狀態(好壞球數,壘包狀態等等)
        foreach ($arr_livescore_extradata as $k => $v) {
            $col_name = $v['Name'];
            $col_value = $v['Value'];
            $ret[$col_name] = $col_value;
        }

        return $ret;

    }

}

