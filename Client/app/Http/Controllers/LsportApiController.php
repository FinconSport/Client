<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redis;
use DB;

// LSport
use App\Models\LsportFixture;
use App\Models\LsportLeague;
use App\Models\LsportSport;
use App\Models\LsportTeam;
use App\Models\LsportMarket;
use App\Models\LsportMarketBet;
use App\Models\LsportNotice;
use App\Models\LsportRisk;

use App\Models\PlayerOnline;
use App\Models\Player;
use App\Models\Agent;
use App\Models\GameOrder;
use App\Models\PlayerBalanceLogs;
use App\Models\ClientMarquee;
// use App\Models\SystemConfig;

class LsportApiController extends Controller {
    
    protected $page_limit = 20;

    protected $agent_lang;  
    protected $lsport_sport_id = array(
        'baseball' => 154914,
        'basketball' => 48242,
        'football' => 6046,
        'icehockey' => 35232,
        'american football' => 131506
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

    // index
    public function index(Request $request) {
        return view('match.index', $this->data);
    }

    // CommonAccount
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

        $player_data = $return;

        // 獲取agent的limit資料
        $agent_id = $return['agent_id'];
        $return = Agent::where("id",$agent_id)->fetch();
        if ($return === false) {
            $this->ApiError("03");
        }

        $limit = json_decode($return['limit_data'],true);

        ///////////////////////////////////
        $data = array(
            'account' => $player_data['account'],
            'balance' => $player_data['balance'],
            'limit'   => $limit
        );
        
        $this->ApiSuccess($data, "01");
    }

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
            $sport_id = $v['sport_id'];
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

    // 首頁賽事統計
    public function IndexMatchList(Request $request) {

        $input = $this->getRequest($request);

        $checkToken = $this->checkToken($input);
        if ($checkToken === false) {
            $this->ApiError("PLAYER_RELOGIN", true);
        }

        //////////////////////
        // 取得代理的語系
        $player_id = $input['player'];
        $agent_lang = $this->getAgentLang($player_id);

        ////////////////////////////////////////
    
        $sport_list = [154914,48242,6046,35232,131506];

        ////////////////////////////////////////
        $data = [];
        foreach ($sport_list as $k => $v) {
            $sport_id = $v;

            ////////////////////////////////////////
            $key = $sport_id . "_" . $agent_lang;

            ////////////////////////////////////////

            $return = Redis::hget('lsport_match_list', $key);
            $return = json_decode($return,true);

            if ($sport_id  == 48242) {
            //    dd($return['early']);
            }

            $sport_name = LsportSport::getName(['sport_id'=>$sport_id, 'api_lang'=>$agent_lang]);

            /////////////////////////

            $early_count = 0;
            foreach ($return['early'][$sport_id]['list'] as $k => $v) {
                $early_count += count($v['list']);
            }

            if ($early_count > 0) { 
                if (!isset($data['early']['total'])) {
                    $data['early']['total'] = 0;
                }
                $data['early']['items'][$sport_id]['count'] = $early_count;
                $data['early']['items'][$sport_id]['name']  = $sport_name;
                $data['early']['total'] += $early_count;
            }

            ////////////////////

            $living_count = 0;
            foreach ($return['living'][$sport_id]['list'] as $k => $v) {
                $living_count += count($v['list']);
            }

            if ($living_count > 0) {
                if (!isset($data['living']['total'])) {
                    $data['living']['total'] = 0;
                }
                $data['living']['items'][$sport_id]['count'] = $living_count;
                $data['living']['items'][$sport_id]['name']  = $sport_name;                
                $data['living']['total'] += $living_count;
            }
        }

        // gzip
        if (!isset($input['is_gzip']) || ($input['is_gzip']==1)) {  // 方便測試觀察輸出可以開關gzip
            $data = $this->gzip($data);
            $this->ApiSuccess($data, "01", true);
        } else {
            $this->ApiSuccess($data, "01", false);
        }
    }

/****************************************
 *    
 *    賽事列表頁
 *    
****************************************/

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

        ///////////////////////////////////
        //  夾帶聯賽資料

        $league_mode = false;
        if (isset($input['league_mode'])) {
            $league_mode = true;
        }

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

            if ($league_mode) {

                // 取得聯賽資料
                $return = LsportLeague::where("sport_id",$v['sport_id'])->where('status', 1)->orderBy('id', 'ASC')->list();
                if ($return === false) {
                    $this->ApiError("02");
                }

                // league data
                $league_list = array();
                foreach ($return as $kk => $vv) {
                    $tmp = array();
                    $tmp['league_id'] = $vv['league_id'];
                    $tmp['name'] = $vv['name_en'];
                    if ($vv[$lang_col] != "") {
                        $tmp['name'] = $vv[$lang_col];
                    }
                    $league_list[] = $tmp;
                }
        
                $data[] = array(
                    'sport_id' => $v['sport_id'],
                    'name' => $sport_name,
                    'league' => $league_list
                );
            } else {

                $data[] = array(
                    'sport_id' => $v['sport_id'],
                    'name' => $sport_name
                );
            }

        }

        ///////////////////////////////////

        $this->ApiSuccess($data, "01");

    }

    // Match
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

        //////////////////////////////////////////

        if (!isset($input['sport_id'])) {
            $this->ApiError("01");
        }
        
        $sport_id = $input['sport_id'];

        ////////////////////////////////////////
        $key = $sport_id . "_" . $agent_lang;

        ////////////////////////////////////////

        $data = Redis::hget('lsport_risk_match_list', $key);
        $data = json_decode($data,true);

        foreach ($data as $k => $v) {
            foreach ($v as $sport_id => $sport) {
                foreach ($sport['list'] as $league_id => $league) {
                    foreach ($league['list'] as $fixture_id => $fixture) {
        
                        $return = LsportRisk::where("fixture_id",$fixture_id)->first();
                        $risk_data = json_decode($return['data'],true);
        
                        $market_bet_count = 0;
                        // 部份比賽, 沒有market
                        if (!isset($fixture['list'])) {
                            continue;
                        }
        
                        // 填入risk資料
                        foreach ($fixture['list'] as $market_id => $market) {
                            if (isset($data[$k][$sport_id]['list'][$league_id]['list'][$fixture_id]['list'][$market_id])) {
                                $market_data = $data[$k][$sport_id]['list'][$league_id]['list'][$fixture_id]['list'][$market_id];

                                $market_main_line = $market_data['main_line'];
                                $base_main_line = $market_data['base_main_line'];
    
                                foreach ($market_data['list'] as $line => $bet_data) {

                                    // match_index 限定邏輯
                                    if ($line != $base_main_line) {
                                        unset($data[$k][$sport_id]['list'][$league_id]['list'][$fixture_id]['list'][$market_id]['list'][$line]);
                                        continue;
                                    } 

                                    if (isset($risk_data[$market_id])) {
                                        foreach ($risk_data[$market_id] as $risk_key => $risk_config) {
                                            if ($risk_config !== null) {
                                                $data[$k][$sport_id]['list'][$league_id]['list'][$fixture_id]['list'][$market_id]['list'][$line][$risk_key]['status'] = $risk_config;
                                            }
                                        }
                                    }
                                }

                            }

                            // 計算多少玩法
                            $cc = count($market_data['list']);
                            $market_bet_count += $cc; 
                            
                        }
        
                        $data[$k][$sport_id]['list'][$league_id]['list'][$fixture_id]['market_bet_count'] = $market_bet_count;
                    }
                }
            }
        }
        
        // gzip
        if (!isset($input['is_gzip']) || ($input['is_gzip']==1)) {  // 方便測試觀察輸出可以開關gzip
            $data = $this->gzip($data);
            $this->ApiSuccess($data, "01", true);
        } else {
            $this->ApiSuccess($data, "01", false);
        }

    } 

    // GameBet
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

        // 限額資料
        $agent_limit = json_decode($return['limit_data'],true);

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

        // 判斷賽事狀態是否可下注 + 限額判斷
        $fixture_status = $fixture_data['status'];
        if ($fixture_status == 1) {
            // 早盤
            $limit = $agent_limit['early'][$sport_id];

            if ($bet_amount < $limit['min']) {
                $this->ApiError("21");
            }
            if ($bet_amount > $limit['max']) {
                $this->ApiError("22");
            }
        } elseif (($fixture_status == 2) || ($fixture_status == 9)) {
            // 滾球
            $limit = $agent_limit['living'][$sport_id];
            if ($bet_amount < $limit['min']) {
                $this->ApiError("21");
            }
            if ($bet_amount > $limit['max']) {
                $this->ApiError("22");
            }
        } else {
            // 賽事狀態不允許下注
            $this->ApiError("20");
        }

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
        $order['better_rate'] = $is_better_rate;
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

                $default_delay_datetime = $delay_time;
            } else {  // 風控大單,延時投注均未啟動
                // 通過
                $default_order_status = $this->game_order_status['wait_for_result'];
                $default_approval_time = time();
                $default_delay_datetime = null;
            }
        }

        // 取得賠率
        $market_bet_data = LSportMarketBet::where("fixture_id", $fixture_id)
        ->where("bet_id", $market_bet_id)
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
        // 水位調整

        $status_type = ["","early","living"];
        if ($fixture_status == 9) {
            $fixture_status = 2;
        }
        $status_type_name = $status_type[$fixture_status];
        

        // 根據水位調整賠率
        $d_market_bet_data = $this->getAdjustedRate($status_type_name, $sport_id, $fixture_id, $market_id, $market_bet_id, $market_bet_line);
        if ($d_market_bet_data !== false) {
            $market_bet_data = $d_market_bet_data;
        }

        //////////////////////////////////////////
        
        // 取得風控設定
        $return = LsportRisk::where("fixture_id",$fixture_id)->first();
        $risk_data = json_decode($return['data'],true);

        $name_en = $market_bet_data['name_en'];
        $pos_name_en = [["1","Odd","Over"],["2","Even","Under"]];

        $risk_config = null;
        if (in_array($name_en, $pos_name_en[0])) {
            // pos = 0
            if (isset($risk_data[$market_id][0])) {
                $risk_config = $risk_data[$market_id][0];
            }
        } elseif (in_array($name_en, $pos_name_en[1])) {
            // pos = 1
            if (isset($risk_data[$market_id][1])) {
                $risk_config = $risk_data[$market_id][1];
            }
        }

        if ($risk_config === 0) {
            $this->ApiError("151");
        }

        //////////////////////////////////////////
        // 建立延時注單時或風控大單以下欄位應該留空: approval_time, bet_rate

        if ($is_risk_order == true) {
            if ($is_bet_delay == true) {
                //////////////////////////////////////////
                // order data
                $order['bet_rate'] = null;
            } else {
                //////////////////////////////////////////
                // order data
                $order['bet_rate'] = null;
            }

            // 設定risk 
            $this->riskOrderLock($fixture_id, $market_id);

        } else {
            if ($is_bet_delay == true) {
                //////////////////////////////////////////
                // order data
                $order['bet_rate'] = null;
            } else {
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
        $order['create_time'] = time();

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
        $tmp['balance_type'] = "game_bet";
        $tmp['change_balance'] = $change_amount;
        $tmp['before_balance'] = $before_amount;
        $tmp['after_balance'] = $after_amount;
        $tmp['create_time'] = time();
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
        $is_better_rate = $input['better_rate'];  //是否自動接受更好的賠率(若不接受則在伺服器端賠率較佳時會退回投注)

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

        // 限額資料
        $agent_limit = json_decode($return['limit_data'],true);

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
                $default_delay_datetime = $delay_time;
            } else {  // 風控大單,延時投注均未啟動
                // 通過
                $default_order_status = $this->game_order_status['wait_for_result'];
                $default_approval_time = time();
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
                'better_rate' => $is_better_rate,
                'bet_amount' => $bet_amount,
                'status' => $default_order_status,
                'create_time' => time(),
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
                
            // 判斷賽事狀態是否可下注 + 限額判斷
            $fixture_status = $fixture_data['status'];
            if ($fixture_status == 1) {
                // 早盤
                $limit = $agent_limit['early'][$sport_id];

                if ($bet_amount < $limit['min']) {
                    $this->ApiError("25");
                }
                if ($bet_amount > $limit['max']) {
                    $this->ApiError("26");
                }
            } elseif (($fixture_status == 2) || ($fixture_status == 9)) {
                // 滾球
                $limit = $agent_limit['living'][$sport_id];
                if ($bet_amount < $limit['min']) {
                    $this->ApiError("25");
                }
                if ($bet_amount > $limit['max']) {
                    $this->ApiError("26");
                }
            } else {
                // 賽事狀態不允許下注
                $this->ApiError("27");
            }
    
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
            // 取得風控設定
            $return = LsportRisk::where("fixture_id",$fixture_id)->first();
            $risk_data = json_decode($return['data'],true);

            $name_en = $market_bet_data['name_en'];
            $pos_name_en = [["1","Odd","Over"],["2","Even","Under"]];

            $risk_config = null;
            if (in_array($name_en, $pos_name_en[0])) {
                // pos = 0
                if (isset($risk_data[$market_id][0])) {
                    $risk_config = $risk_data[$market_id][0];
                }
            } elseif (in_array($name_en, $pos_name_en[1])) {
                // pos = 1
                if (isset($risk_data[$market_id][1])) {
                    $risk_config = $risk_data[$market_id][1];
                }
            }

            if ($risk_config === 0) {
                $this->ApiError("151");
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
                } else {
                    //////////////////////////////////////////
                    // order data
                    $order['bet_rate'] = null;
                    //////////////////////////////////////////
                }
                
                // 設定risk 
                $this->riskOrderLock($fixture_id, $market_id);
            } else { // $is_risk_order: false
                if ($is_bet_delay == true) {
                    //////////////////////////////////////////
                    // order data
                    $order['bet_rate'] = null;
                    //////////////////////////////////////////
                } else {
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
        $tmp['balance_type'] = "game_bet";
        $tmp['change_balance'] = $change_amount;
        $tmp['before_balance'] = $before_amount;
        $tmp['after_balance'] = $after_amount;
        $tmp['create_time'] = time();
        PlayerBalanceLogs::insert($tmp);

        ///////////////////////////////////
        $data = $m_order_id;

        $this->ApiSuccess($data, "01");

    }

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
            $input['sport'] = $this->system_config['default_sport'];  // 預設1 , 足球
        }

        if (!isset($input['page']) || ($input['page'] == "")) {
            $input['page'] = 1; // 預設1 
        }

        // 開始時間
        if (!isset($input['start_time']) || ($input['start_time'] == "")) {
            $input['start_time'] = date("Y-m-d 00:00:00", strtotime("-1 day")); // 預設昨天
        }

        // 結束時間
        if (!isset($input['end_time']) || ($input['end_time'] == "")) {
            $input['end_time'] = date("Y-m-d", strtotime("+1 day")); // 預設明天
        } else {    
            $input['end_time'] = date("Y-m-d", strtotime($input['end_time'] . " +1 day")); // 預設明天
        }

        // 聯賽
        if (!isset($input['league_id']) || ($input['league_id'] == "")) {
            $input['league_id'] = false; // 預設1 
        }
    	/////////////////////////
        // Search 區用
        $sport_id = $input['sport'];
        $page = $input['page'];
        $start_time = $input['start_time'];
        $end_time = $input['end_time'];
        $league_id = $input['league_id'];
        
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
        
        $return = LsportFixture::getResultList($input);
        if ($return === false) {
            $this->ApiError('02');
        }

        $fixture_data = $return;

        $reponse = array();
        foreach ($fixture_data as $k => $v) {

            $tmp = array();
            $tmp['fixture_id']  = $v['fixture_id'];
            $tmp['start_time']  = date("Y-m-d H:i:s",$v['start_time']);
            $tmp['status']      = $v['status'];
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

        /////////////////////////////////////////////////////////////////
        // gzip
        if (!isset($input['is_gzip']) || ($input['is_gzip']==1)) {  // 方便測試觀察輸出可以開關gzip
            $data = $this->gzip($data);
            $this->ApiSuccess($data, "01", true);
        } else {
            $this->ApiSuccess($data, "01", false);
        }
    }

/****************************************
 *    
 *    遊戲頁
 *    
****************************************/

    // 遊戲頁 . 改
    public function GameIndex(Request $request) {
        
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

        ////////////////////////////////////////
        $key = $sport_id . "_" . $agent_lang;

        ////////////////////////////////////////

        $data = Redis::hget('lsport_risk_match_list', $key);
        $data = json_decode($data,true);
        
        //////////////////////////////////
        // 取得賽事資料
        $return = LsportFixture::where("fixture_id",$input['fixture_id'])->first();
        if ($return === false) {
            $this->ApiError("02");
        }

        $current_league_id = $return['league_id'];

        //////////////////////////////////

        foreach ($data as $k => $v) {
            foreach ($v as $sport_id => $sport) {
              foreach ($sport['list'] as $league_id => $league) {
                
                // game_index 限定
                if ($current_league_id != $league_id) {
                    unset($data[$k][$sport_id]['list'][$league_id]);
                    continue;
                }
                
                foreach ($league['list'] as $fixture_id => $fixture) {
      
                    // game_index 限定邏輯
                    if ($fixture_id != $input['fixture_id']) {
                        unset($data[$k][$sport_id]['list'][$league_id]['list'][$fixture_id]);
                        continue;
                    }

                    $return = LsportRisk::where("fixture_id",$fixture_id)->first();
                    $risk_data = json_decode($return['data'],true);
      
                    // 部份比賽, 沒有market
                    if (!isset($fixture['list'])) {
                        continue;
                    }
      
                    // 填入risk資料
                    foreach ($fixture['list'] as $market_id => $market) {
                        if (isset($data[$k][$sport_id]['list'][$league_id]['list'][$fixture_id]['list'][$market_id])) {
                            $market_data = $data[$k][$sport_id]['list'][$league_id]['list'][$fixture_id]['list'][$market_id];

                            $market_main_line = $market_data['main_line'];

                            foreach ($market_data['list'] as $line => $bet_data) {

                                if (isset($risk_data[$market_id])) {
                                    foreach ($risk_data[$market_id] as $risk_key => $risk_config) {
                                        if ($risk_config !== null) {
                                            $data[$k][$sport_id]['list'][$league_id]['list'][$fixture_id]['list'][$market_id]['list'][$line][$risk_key]['status'] = $risk_config;
                                        }
                                    }
                                }
                            }

                        }
                    }
      
                }
              }
            }
          }

        // game_index 限定 , 合併 early , living
        $tmp_data = array();
        foreach ($data as $k => $v) {
            foreach ($v as $sport_id => $vv) {
                foreach ($vv['list'] as $league_id => $vvv) {
                   
                    $tmp_data['league_id'] = $vvv['league_id'];
                    $tmp_data['league_name'] = $vvv['league_name'];
                    foreach ($vvv['list'] as $fixture_id => $vvvv) {
                        $tmp_data['list'][$fixture_id] = $vvvv;
                    }
                }
                
            }
        }

        $data = $tmp_data;

        // gzip
        if (!isset($input['is_gzip']) || ($input['is_gzip']==1)) {  // 方便測試觀察輸出可以開關gzip
            $data = $this->gzip($data);
            $this->ApiSuccess($data, "01", true);
        } else {
            $this->ApiSuccess($data, "01", false);
        }

    }

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

        //////////////////////////////////////////

        if (!isset($input['page'])) {
            $input['page'] = 1;
        }

        if (!isset($input['result'])) {
            $input['result'] = -1;
        }
        
        if (!isset($input['start_time'])) {
            $input['start_time'] = date('Y-m-d', strtotime('-6 day'));
        }

        // 結束時間
        if (!isset($input['end_time']) || ($input['end_time'] == "")) {
            $input['end_time'] = date("Y-m-d", strtotime("+1 day")); // 預設明天
        } else {    
            $input['end_time'] = date("Y-m-d", strtotime($input['end_time'])); // 預設明天
        }
        
        //////////////////////////////////////////

        $page_limit = $this->page_limit;
        $page = $input['page'];
        $skip = ($page-1)*$page_limit;

        //////////////////////////////////////////
        // 獲取注單資料

        if (isset($input['debug'])) {

        }
        
        $return = GameOrder::getOrderList([
            "player_id"     => $player_id, 
            "result"        => $input['result'],
            "start_time"    => $input['start_time'],
            "end_time"      => $input['end_time'],
            "skip"          => $skip, 
            "page_limit"    => $page_limit
        ]);
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
            "active_bet",
            "create_time",
            "result_time",
            "status"
        );

        $round_columns = ['bet_amount','result_amount','active_bet','bet_rate'];

        foreach ($order_data as $k => $v) {
            foreach ($columns as $kk => $vv) {
                $tmp[$k][$vv] = $v[$vv]; 
            }
            
            // 轉時間格式
            $time_columns = ["start_time","create_time","approval_time","result_time"];
            foreach ($time_columns as $kkkk => $vvvv) {
                if (isset($tmp[$k][$vvvv])) { 
                    $tmp[$k][$vvvv] = date("Y-m-d H:i:s",$tmp[$k][$vvvv]);
                }
            }

            $tmp[$k]['status'] = $v['status'];
            $tmp[$k]['m_order'] = $v['m_order'];

            $sport_id = $v["sport_id"];
            $home_team_id = $v["home_team_id"];
            $away_team_id = $v["away_team_id"];
            
            // 關於小數點處理
            foreach ($round_columns as $kkkk => $vvvv) {
                if (isset($tmp[$k][$vvvv])) {
                    if ($tmp[$k][$vvvv] != null) {
                        $tmp[$k][$vvvv] = $tmp[$k][$vvvv]+0;
                    //    $tmp[$k][$vvvv] = intval($tmp[$k][$vvvv]*100)/100;
                        $tmp[$k][$vvvv] = $tmp[$k][$vvvv]."";
                    }
                }
            }

            // 有串關資料
            if ($v['m_order'] == 1) {

                $return = GameOrder::where("m_id", $v['m_id'])->list();
                if ($return === false) {
                    $this->ApiError("02");
                }

                foreach ($return as $kkk => $vvv) {

                    $tmp_bet_data = $vvv;

                    // 取得market_bet name_en
                    $market_bet_id = $vvv['market_bet_id'];
                    $return = LsportMarketBet::where("bet_id",$market_bet_id)->fetch();
                    if ($return === false) {
                        $this->ApiError("04");
                    }
                    $tmp_bet_data['market_bet_name_en'] = $return['name_en'];

                    // 取得賽事開始時間
                    $fixture_id = $vvv['fixture_id'];
                    $return = LsportFixture::where("fixture_id",$fixture_id)->fetch();
                    if ($return === false) {
                        $this->ApiError("03");
                    }
                    $tmp_bet_data['start_time'] = $return['start_time'];
                    
                    // 轉時間格式
                    $time_columns = ["start_time","create_time","approval_time","result_time"];
                    foreach ($time_columns as $kkkk => $vvvv) {
                        if (isset($tmp_bet_data[$vvvv])) { 
                            $tmp_bet_data[$vvvv] = date("Y-m-d H:i:s",$tmp_bet_data[$vvvv]);
                        }
                    }

                    // 滾球/早盤字樣判定
                    $market_type = 0;
                    if ($return['start_time'] < strtotime($vvv['create_time'])) {
                        $market_type = 1;
                    }
                    $tmp_bet_data['market_type'] = $market_type;
                    
                    // 關於小數點處理
                    foreach ($round_columns as $kkkk => $vvvv) {
                        if (isset($tmp_bet_data[$vvvv])) {
                            if ($tmp_bet_data[$vvvv] != null) {
                                $tmp_bet_data[$vvvv] = $tmp_bet_data[$vvvv]+0;
                            //    $tmp_bet_data[$vvvv] = intval($tmp_bet_data[$vvvv]*100)/100;
                                $tmp_bet_data[$vvvv] = $tmp_bet_data[$vvvv]."";
                            }
                        }
                    }
                    
                    // 處理 1/4分盤顯示
                    $tmp_bet_data['market_bet_line'] = $this->displayMainLine($tmp_bet_data['market_bet_line']);
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

                // 轉時間格式
                $time_columns = ["start_time","create_time","approval_time","result_time"];
                foreach ($time_columns as $kkkk => $vvvv) {
                    if (isset($tmp_bet_data[$vvvv])) { 
                        $tmp_bet_data[$vvvv] = date("Y-m-d H:i:s",$tmp_bet_data[$vvvv]);
                    }
                }

                // 滾球/早盤字樣判定
                $market_type = 0;
                if ($return['start_time'] < strtotime($v['create_time'])) {
                    $market_type = 1;
                }
                $tmp_bet_data['market_type'] = $market_type;

                // 關於小數點處理
                    foreach ($round_columns as $kkkk => $vvvv) {
                        if (isset($tmp_bet_data[$vvvv])) {
                            if ($tmp_bet_data[$vvvv] != null) {
                                $tmp_bet_data[$vvvv] = $tmp_bet_data[$vvvv]+0;
                                $tmp_bet_data[$vvvv] = $tmp_bet_data[$vvvv]."";
                            }
                        }
                    }

                // 取得market_bet name_en
                $market_bet_id = $v['market_bet_id'];
                $return = LsportMarketBet::where("bet_id",$market_bet_id)->fetch();
                if ($return === false) {
                    $this->ApiError("04");
                }
                $tmp_bet_data['market_bet_name_en'] = $return['name_en'];

                // 處理 1/4分盤顯示
                $tmp_bet_data['market_bet_line'] = $this->displayMainLine($tmp_bet_data['market_bet_line']);
                    
                $tmp[$k]['bet_data'][] = $tmp_bet_data;
            }

        }

        $data['list'] = $tmp;

        ////////////////////////
        // gzip
        if (!isset($input['is_gzip']) || ($input['is_gzip']==1)) {  // 方便測試觀察輸出可以開關gzip
            $data = $this->gzip($data);
            $this->ApiSuccess($data, "01", true);
        } else {
            $this->ApiSuccess($data, "01", false);
        }
    }

    // BalanceLogs
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

        // 開始時間
        if (!isset($input['start_time']) || ($input['start_time'] == "")) {
            $input['start_time'] = date("Y-m-d", strtotime("-1 day")); // 預設昨天
        }

        // 結束時間
        if (!isset($input['end_time']) || ($input['end_time'] == "")) {
            $input['end_time'] = date("Y-m-d", strtotime("+1 day")); // 預設明天
        } else {    
            $input['end_time'] = date("Y-m-d", strtotime($input['end_time'] . " +1 day")); // 預設明天
        }

        // 聯賽
        if (!isset($input['balance_type']) || ($input['balance_type'] == "")) {
            $input['balance_type'] = false; // 預設
        }

        if (!isset($input['page'])) {
            $input['page'] = 1;
        }

        ///////////////////////////////////////////

        $page_limit = $this->page_limit;
        $page = $input['page'];
        $skip = ($page-1)*$page_limit;

        $input['skip'] = $skip;
        $input['page_limit'] = $page_limit;

        ///////////////////////////////////////////

        // 帳變類型
        $typeList = trans("pc.BalanceLogs_TypeList");
        
        ///////////////////////////////////////////

        $return = PlayerBalanceLogs::getBalanceLogsList($input);
        if ($return === false) {
            $this->ApiError("01");
        }

        $list = array();
        foreach ($return as $k => $v) {
            $v['type'] = $typeList[$v['balance_type']];
            $v['create_time'] = date("Y-m-d H:i:s",$v['create_time']);
            $list[] = $v;
        } 

        $data = array();
        $data['list'] = $list;

        ////////////////////////
        // gzip
        if (!isset($input['is_gzip']) || ($input['is_gzip']==1)) {  // 方便測試觀察輸出可以開關gzip
            $data = $this->gzip($data);
            $this->ApiSuccess($data, "01", true);
        } else {
            $this->ApiSuccess($data, "01", false);
        }

    }

    //////////////////////////

    protected function gzip($data) {

        $data = json_encode($data, true);
        $compressedData = gzcompress($data);  // 使用 gzcompress() 函數進行壓縮
        $base64Data = base64_encode($compressedData);  // 使用 base64_encode() 函數進行 base64 編碼

        return $base64Data;
    }

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

    protected function getMatchScoreboard($sport_id, $fixture_status, $periods, $scoreboard) {

        // 如果還未開賽就回傳null
        if ($fixture_status < $this->fixture_status['living']) {
            return null;
        }

        // 目前只處理特定類型
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

    protected function getMatchPeriods($sport_id, $fixture_status, $scoreboard, $livescore_extradata) {

        // 如果還未開賽就回傳null
        $fixture_status = intval($fixture_status);
        if ($fixture_status < $this->fixture_status['living']) {
            return null;
        }

        // 目前只處理特定類型
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

    // 切換1/4 分盤顯示
    protected function displayMainLine($main_line) {

        // 分數處理
        $score = "";
        $hasSpace = strpos($main_line, ' ') !== false;
        if ($hasSpace) {
            $fields = explode(' ', $main_line);
            $main_line = $fields[0];
            $score = " " . $fields[1];
        }
        
        $number = (float)$main_line;
        $is_neg = false;
        if ($number < 0) {
            $is_neg = true;
        }

        $number = abs($number);

        $integerPart = floor($number); // 取整數部分
        $decimalPart = $number - $integerPart; // 取小數部分

        switch ($decimalPart) {
            case 0.25:
                $a = $integerPart;
                $b = $integerPart+0.5;
                $main_line = $a . "/" . $b;
                if ($is_neg) {  // 如果是負數
                    $main_line = "-".$main_line;
                }
                break;
            case 0.75:
                $a = $integerPart+0.5;
                $b = $integerPart+1;
                $main_line = $a . "/" . $b;
                if ($is_neg) {  // 如果是負數
                    $main_line = "-".$main_line;
                }
                break;
            default:
        }
        
        return $main_line.$score;
    }
    
    // 計算 水位調整後的賠率 , for game_bet, m_game_bet
    protected function getAdjustedRate($status, $sport_id, $fixture_id, $market_id, $market_bet_id, $market_main_line) {

        // 取得配置
        $default_market_bet_llimit = json_decode($this->system_config['default_market_bet_llimit'], true);
        
        // 沒有配置的
        if (!isset($default_market_bet_llimit[$status][$sport_id][$market_id])) {
        return false;
        }

        $market_bet_rate = $default_market_bet_llimit[$status][$sport_id][$market_id];

        // 取得market_bet
        $return = LsportMarketBet::where('fixture_id',$fixture_id)
            ->where("market_id",$market_id)
            ->where("base_line.keyword",'"'.$market_main_line.'"')  // main line 有時是空值, 要帶 "
            ->orderBy("name_en.keyword","ASC")
            ->list();
        if ($return === false) {
            return 2;
        }

        $data = $return;

        $tmp = array();
        foreach ($data as $k => $v) {
        $tmp[] = $v['price'];
        }

        if (count($tmp) >= 2) {
        $dd = $this->adjustNumbers($tmp, $market_bet_rate);
        foreach ($data as $k => $v) {
            $data[$k]['price'] = $dd[$k] . "";
        }
        }

        // 找出bet_id 並return
        
        foreach ($data as $k => $v) {
            $bet_id = $v['bet_id'];
            if ($bet_id == $market_bet_id) {
                return $v;
            }
        }
        
        return false;
    }

    protected function adjustNumbers($numbers, $targetValue) {
        while (max($numbers) < $targetValue) {
            $maxValue = max($numbers);
            $diff = $targetValue - $maxValue;
            
            for ($i = 0; $i < count($numbers); $i++) {
                $numbers[$i] += $diff;
            }
        }
        
        return $numbers;
    }

    // 大單自動鎖盤
    protected function riskOrderLock($fixture_id, $market_id) {

        $return = LsportRisk::where("fixture_id",$fixture_id)->first();
        if ($return === false) {
            return false;
        }

        $json = json_decode($return['data'],true);

        $json[$market_id] = [0,0];
        if ($market_id == 1) {  // 足球全場獨贏 , 1x2限定
            $json[$market_id] = [0,0,0];
        }
        
        $return = LsportRisk::where("fixture_id",$fixture_id)->update([
            "data" => $json
        ]);
        if ($return === false) {
            return false;
        }

        return true;
    }
}

