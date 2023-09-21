<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;

use App\Models\GameMatch;
use App\Models\GameResult;
use App\Models\GameOrder;

use App\Models\AntGameList;
use App\Models\AntMatchList;
use App\Models\AntRateList;
use App\Models\AntSeriesList;
use App\Models\AntTeamList;
use App\Models\AntTypeList;
use App\Models\AntNoticeList;

use App\Models\PlayerOnline;
use App\Models\Player;
use App\Models\Agent;
use App\Models\PlayerBalanceLogs;
use App\Models\ClientMarquee;
use App\Models\SystemConfig;


//LSport
use App\Models\LsportFixture;
use App\Models\LsportLeague;
use App\Models\LsportSport;
use App\Models\LsportTeam;
use App\Models\LsportMarket;
use App\Models\LsportMarketBet;



class ApiController extends Controller {
    
  protected $page_limit = 20;
/****************************************
 *    
 *    首頁
 *    
****************************************/

    public function index(Request $request) {
      return view('match.index',$this->data);
    }
    
    // 帳號,餘額
    public function CommonAccount(Request $request) {
      
    	$input = $this->getRequest($request);

        $return = $this->checkToken($input);
        if ($return === false) {
            $this->ApiError("PLAYER_RELOGIN",true);
        }

        //////////////////////////////////////////

        // 獲取用戶資料
        $player_id = $input['player'];

        $return = Player::where("id",$player_id)->first();
        if ($return === false) {
            $this->ApiError("01");
        }

        if ($return['status'] != 1) {
            $this->ApiError("02");
        }

        $data = array();
        $data['account'] = $return['account'];
        $data['balance'] = $return['balance'];
        
        $this->ApiSuccess($data,"01");
    }

    // 輪播
    public function IndexCarousel(Request $request) {
      
    	$input = $this->getRequest($request);

        $return = $this->checkToken($input);
        if ($return === false) {
            $this->ApiError("PLAYER_RELOGIN",true);
        }

        //////////////////////////////////////////

        $return = GameResult::where("status",1)->get();
        if ($return === false) {
            $this->ApiError("01");
        }

        $columns = array("id","home","away","home_score","away_score","sell_status","match_time");

        $data = array();
        foreach ($return as $k => $v) {
            $tmp = array();
            foreach ($columns as $kk => $vv) {
            $tmp[$vv] = $v[$vv];
            }

            // 格式重整
            $tmp['match_time'] = date('Y-m-d H:i:s', $tmp['match_time']);
            $data[] = $tmp;
        }

        $this->ApiSuccess($data,"01");

    }

    // 首頁跑馬燈
    public function IndexMarquee(Request $request) {
      
    	$input = $this->getRequest($request);

        $return = $this->checkToken($input);
        if ($return === false) {
            $this->ApiError("PLAYER_RELOGIN",true);
        }

        //////////////////////////////////////////

        $return = ClientMarquee::where("status",1)->get();
        if ($return === false) {
            $this->ApiError("01");
        }

        $data = array();
        foreach ($return as $k => $v) {
            $data[] = $v['marquee'];
        }

        $this->ApiSuccess($data,"01");
    }
  
    // 系統公告接口
    public function IndexNotice(Request $request) {

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
        
        //////////////////////////////////////////

        $notice_list = array();

        // 系統公告
        $return = ClientMarquee::where("status",1)->get();      
        if ($return === false) {
            $this->ApiError("01");
        }

        foreach ($return as $k => $v) {
            $game_id = 0;
            $title = $v['title'];
            $context = $v['marquee'];
            $create_time = $v['create_time'];

            $notice_list[$game_id][] = [
            "game_id" => $game_id,
            "title" => $title,
            "context" => $context,
            "create_time" => $create_time,
            ];
        }

        /////////////////

        $timestamp = time() - (1 * 24 * 60 * 60); 
        $previous_day = date('Y-m-d 00:00:00', $timestamp); 

        $return = AntNoticeList::where('create_time',">=", $previous_day)->orderBy("create_time","DESC")->get();
        if ($return === false) {
            $this->ApiError("02");
        }

        foreach ($return as $k => $v) {
            $game_id = $v['game_id'];
            $title = $v['title_'.$api_lang];
            $context = $v['context_'.$api_lang];
            $create_time = $v['create_time'];

            $notice_list[$game_id][] = [
            "game_id" => $game_id,
            "title" => $title,
            "context" => $context,
            "create_time" => $create_time,
            ];
        }

        // gzip
        $notice_list = $this->gzip($notice_list);

        $this->ApiSuccess($notice_list,"01",true);
    }

    // 首頁賽事
    public function IndexMatchList(Request $request) {
      
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
            $this->error(__CLASS__, __FUNCTION__, "02");
        }
        
        $name_columns = "name_".$api_lang;
        //////////////////////////////////////////

        $menu_type = ["living","early"];

        // 取得GameList 資料
        $return = AntGameList::where("status",1)->get();
        if ($return === false) {
            $this->ApiError("01");
        }

        foreach ($return as $k => $v) {
            $sport_type[$v['id']] = $v[$name_columns];
        }


        $data = array();
        //$total = 0;

        foreach ($menu_type as $k => $v) {
            switch ($k) {
            case 0:
                // 進行中
                $return = AntMatchList::join('ant_rate_list', 'ant_match_list.match_id', '=', 'ant_rate_list.match_id')
                ->join('ant_series_list', function ($join) {
                $join->on('ant_match_list.game_id', '=', 'ant_series_list.game_id')
                    ->on('ant_match_list.series_id', '=', 'ant_series_list.series_id');
                })
                ->selectRaw('ant_match_list.game_id, COUNT(DISTINCT ant_match_list.id) as count,COUNT(*) as rate_count')
                ->where('ant_rate_list.is_active', '=', 1)
                ->where('ant_match_list.status', 2)
                ->where('ant_series_list.status', 1)
                ->groupBy('ant_match_list.game_id')
                ->having('rate_count', '>', 0)
                ->get();
                if ($return === false) {
                $this->ApiError("01");
                }
                
                $tmp = array();
                $total = 0;
                foreach ($return as $kk => $vv) {
                $tmp["items"][$vv['game_id']]['name'] = $sport_type[$vv['game_id']];
                $tmp["items"][$vv['game_id']]['count'] = $vv['count'];
                $total += $vv['count'];
                }

                $tmp['total'] = $total;
                $data[$v] = $tmp;
            break;
            // TODO
            case 1: 
                // 早盤
                $return = AntMatchList::join('ant_rate_list', 'ant_match_list.match_id', '=', 'ant_rate_list.match_id')
                ->join('ant_series_list', function ($join) {
                $join->on('ant_match_list.game_id', '=', 'ant_series_list.game_id')
                    ->on('ant_match_list.series_id', '=', 'ant_series_list.series_id');
                })
                ->selectRaw('ant_match_list.game_id, COUNT(DISTINCT ant_match_list.id) as count,COUNT(*) as rate_count')
                ->where('ant_rate_list.is_active', '=', 1)
                ->where('ant_match_list.status', 1)
                ->where('ant_series_list.status', 1)
                ->groupBy('ant_match_list.game_id')
                ->having('rate_count', '>', 0)
                ->get();
                if ($return === false) {
                $this->ApiError("01");
                }
                
                $tmp = array();
                $total = 0;
                foreach ($return as $kk => $vv) {
                $tmp["items"][$vv['game_id']]['name'] = $sport_type[$vv['game_id']];
                $tmp["items"][$vv['game_id']]['count'] = $vv['count'];
                $total += $vv['count'];
                }

                $tmp['total'] = $total;
                $data[$v] = $tmp;

            break;
            default:
            }
            
            // 處理加總
            $total = array_sum($data[$v]);
            $data[$v]['total'] = $total;
            
        }

        $this->ApiSuccess($data,"01");
    }


/****************************************
 *    
 *    賽事列表頁
 *    
****************************************/


    // 賽事列表-分類
    public function MatchSport(Request $request) {
      
    	$input = $this->getRequest($request);

        $return = $this->checkToken($input);
        if ($return === false) {
            $this->ApiError("PLAYER_RELOGIN",true);
        }

        //////////////////////////////////////////

        $return = AntGameList::where("status",1)->get();
        if ($return === false) {
            $this->ApiError("01");
        }

        $data = array();
        foreach ($return as $k => $v) {

            $tmp = array();

            $tmp['id'] = $v['id'];
            $tmp['name'] = $v['name_cn'];

            $data[] = $tmp;
        }

        $this->ApiSuccess($data,"01"); 

    }

    // 賽事列表-賽事資料
    public function MatchIndex(Request $request) {
      
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
            $this->error(__CLASS__, __FUNCTION__, "01");
        }
        
        $name_columns = "name_".$api_lang;

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
        $return = AntMatchList::join('ant_rate_list', 'ant_match_list.match_id', '=', 'ant_rate_list.match_id')
        ->join('ant_series_list', function ($join) {
                $join->on('ant_match_list.game_id', '=', 'ant_series_list.game_id')->on('ant_match_list.series_id', '=', 'ant_series_list.series_id');
        })
        ->select('ant_match_list.*', DB::raw('COUNT(ant_rate_list.id) as rate_count'))
        ->where('ant_rate_list.is_active', '=', 1)
        ->where('ant_series_list.status', 1)
        ->where('ant_match_list.status', 1)
        ->where('ant_match_list.start_time',"<=", $after_tomorrow)
        ->where("ant_match_list.game_id",$sport_id)
        ->groupBy('ant_match_list.match_id')
        ->having('rate_count', '>', 0)
        ->orderBy("ant_series_list.order_by")->get();

        $tmp = $this->rebuild($return, $api_lang,$sport_id);
        $data['early'] = $tmp;

        //////////////////////////////
        // 滾球

        if ($this->controller == "m_order") {
            // 串關不抓滾球賽事
            $data['living'] = array();
        } else {
            $return = AntMatchList::join('ant_rate_list', 'ant_match_list.match_id', '=', 'ant_rate_list.match_id')
            ->join('ant_series_list', function ($join) {
                    $join->on('ant_match_list.game_id', '=', 'ant_series_list.game_id')->on('ant_match_list.series_id', '=', 'ant_series_list.series_id');
            })
            ->select('ant_match_list.*', DB::raw('COUNT(ant_rate_list.id) as rate_count'))
            ->where('ant_rate_list.is_active', '=', 1)
            ->where('ant_series_list.status', 1)
            ->where('ant_match_list.status', 2)
            ->where('ant_match_list.start_time',"<=", $after_tomorrow)
            ->where("ant_match_list.game_id",$sport_id)
            ->groupBy('ant_match_list.match_id')
            ->having('rate_count', '>', 0)
            ->orderBy("ant_series_list.order_by")->get();
    
            $tmp = $this->rebuild($return, $api_lang,$sport_id);
            $data['living'] = $tmp;
        }
        
        ///////////////////////////////

        // gzip
        $data = $this->gzip($data);

        $this->ApiSuccess($data,"01",true);
    }

    // 賽事列表- 投注接口
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

        $sport_id = 1;
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

        //fixture status : 1未开始、2进行中、3已结束、4延期、5中断、99取消
        if ($return['status'] >= 3) {
            $this->ApiError("09");
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

    // 賽事列表- 串關投注接口
    public function mGameBet(Request $request) {
      
    	$input = $this->getRequest($request);

      $return = $this->checkToken($input);
      if ($return === false) {
        $this->ApiError("PLAYER_RELOGIN",true);
      }

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
      $bet_amount = $input['bet_amount'];
      $is_better_rate = $input['better_rate'];

      $game_id = 1;
      if (isset($input['game_id'])) {
        $game_id = $input['game_id'];
      }
      

      $bet_data = json_decode($input['bet_data'],true);

      
      $order = array();
      
      // 參數檢查 TODO - 初步 隨便弄弄
      if ($bet_amount <= 0) {
        $this->ApiError("01");
      }

      // 取得用戶資料
      $return = Player::where("id",$player_id)->first();
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
      $return = Agent::where("id",$agent_id)->first();
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
      foreach ($bet_data as $k => $v) {
        // 取得必要參數
        $match_id = $v['bet_match'];
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
        $return = Player::where("id",$player_id)->first();
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
        $return = Agent::where("id",$agent_id)->first();
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
        $return = AntMatchList::where("match_id",$match_id)->where("game_id",$game_id)->first();
        if ($return == false) {
          $this->ApiError("13");
        }
  
        // 判斷注單 是否為同一game_id
        if ($m_game_id === false) {
          $m_game_id = $return['game_id'];
        } else {
          if ($m_game_id != $return['game_id']) {
            $this->ApiError("14");
          }
        }

        //match status : 1未开始、2进行中、3已结束、4延期、5中断、99取消
        // 串關只能賽前注單
        if ($return['status'] != 1) {
          $this->ApiError("15");
        }
  
        // decode 聯盟
        $series_data = json_decode($return['series'],true);
        //////////////////////////////////////////
        // order data
        $order['series_id'] = $series_data['id'];
        $order['series_name'] = $series_data['name_cn'];
        $order['match_id'] = $match_id;
        $order['game_id'] = $return['game_id'];
        //////////////////////////////////////////
  
        // decode 隊伍
        $teams_data = json_decode($return['teams'],true);

        //////////////////////////////////////////
        // order data
        if ($teams_data[0]['index'] == 1) {
          $order['home_team_id'] = $teams_data[0]['team']['id'];
          $order['home_team_name'] = $teams_data[0]['team']['name_cn'];
        } else {
          $order['away_team_id'] = $teams_data[0]['team']['id'];
          $order['away_team_name'] = $teams_data[0]['team']['name_cn'];
        }
        
        if ($teams_data[1]['index'] == 1) {
          $order['home_team_id'] = $teams_data[1]['team']['id'];
          $order['home_team_name'] = $teams_data[1]['team']['name_cn'];
        } else {
          $order['away_team_id'] = $teams_data[1]['team']['id'];
          $order['away_team_name'] = $teams_data[1]['team']['name_cn'];
        }
        //////////////////////////////////////////
  
        // 取得賠率
        $return = AntRateList::where("rate_id",$bet_type_id)->where("match_id",$match_id)->first();
        if ($return === false) {
          $this->ApiError("16");
        }
        
        $rate_status = $return['status'];
        $type_priority = $return['game_priority'];

  
        // decode 賠率
        $rate_data = json_decode($return['items'],true);
  
        foreach ($rate_data as $k => $v) {
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
        $order['type_name'] = $return['name_cn'];
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
          $return = GameOrder::where("id",$m_order_id)->update([
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

      $return = Player::where("id",$player_id)->update([
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

      $this->ApiSuccess($return,"01");

    }

    // 賽事結果 
    public function ResultIndex(Request $request) {
      
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
        $this->error(__CLASS__, __FUNCTION__, "01");
      }
      
      $name_columns = "name_".$api_lang;

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
        $AntMatchList = AntMatchList::where("game_id",$sport_id)->where("status",">=",2);

        $return = $AntMatchList->skip($skip)->take($page_limit)->orderBy('start_time', 'DESC')->get();
        $pagination = $AntMatchList->count();

      ////////////////////
      $columns = array(
        "id",
        "match_id",
        "game_id",
        "series_id",
        "start_time",
        "end_time",
        "status"
      );

      $data = array();
      foreach ($return as $k => $v) {

        $tmp = array();
        
        $series = json_decode($v['series'],true);
        $series_id = $series['id'];
        $game_id = $series['game_id'];
        $tmp_logo = AntSeriesList::where("series_id",$series_id)->where("game_id",$sport_id)->where("status",1)->first();
        if ($tmp_logo === false) {
          $this->ApiError("01");
        }
        if ($tmp_logo == null) {
          continue;
        }

        $name_columns = "name_".$api_lang;

        $tmp['series_name'] = $tmp_logo[$name_columns];
        $tmp['series_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'] . "?v=" . $this->system_config['version'];

        foreach ($columns as $kk => $vv) {
          $tmp[$vv] = $v[$vv]; 
        }

        // stat
        $stat = json_decode($v['stat'],true);
        unset($stat['stat']['match_id']);
        unset($stat['stat']['time']);
        if ($v['stat'] == "") {
          $tmp['stat'] = [];
        } else {
          $tmp['stat'] = $stat['stat'];
        }

        $tmp['status'] = $status[$v['status']];
       
        $teams = json_decode($v['teams'],true);

        $teams = json_decode($v['teams'],true);

        foreach ($teams as $key => $value) {
          $team_id = $value['team']['id'];
          $tmp_logo = AntTeamList::where("team_id",$team_id)->where("game_id",$sport_id)->first();
          if ($tmp_logo === false) {
            $this->error(__CLASS__, __FUNCTION__, "05");
          }
          
          if ($tmp_logo == null) {
            continue;
          }

          /////////////////////////////////

          $teams[$key]['team']['name'] =  $tmp_logo[$name_columns];
          $teams[$key]['team']['logo'] =  $this->system_config['image_url'] . $tmp_logo['local_logo'] . "?v=" . $this->system_config['version'];
       
        }

        foreach ($columns as $kk => $vv) {
          $tmp[$vv] = $v[$vv]; 
        }
        
        $tmp['status'] = $status[$v['status']];
       

        foreach ($teams as $kk => $vv) {

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
      //$data = $this->gzip($data);

      $this->ajaxSuccess("success_result_index_01",$data);
    }

/****************************************
 *    
 *    遊戲頁
 *    
****************************************/
    // 遊戲頁
    public function GameIndex(Request $request) {
      
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
            $this->error(__CLASS__, __FUNCTION__, "02");
        }
        
        $name_columns = "name_".$api_lang;

        //////////////////////////////////////////

        $match_id = $input['match_id'];
        $sport_id = $input['sport_id'];

        if (($match_id+0 != $match_id) && ($match_id+0 == 0)) {
            $this->ApiError("01");
        }
        if (($sport_id+0 != $sport_id) && ($sport_id+0 == 0)) {
            $this->ApiError("01");
        }

        $return = AntMatchList::where("match_id",$match_id)->where("game_id",$sport_id)->get();
        if ($return === false) {
            $this->ApiError("02");
        }
        
        $tmp = $this->rebuild($return, $api_lang,$sport_id);

        $data = $tmp;

        /**************************************/

        // gzip
        $data = $this->gzip($data);

        $this->ApiSuccess($data,"01",true); 
    }
    
    // 下注紀錄
    public function CommonOrder(Request $request) {
      
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
        $this->error(__CLASS__, __FUNCTION__, "02");
      }
      
      $name_columns = "name_".$api_lang;

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
      $GameOrder = GameOrder::where("player_id",$input['player']);
      $groupedData = GameOrder::select('m_id')->where("player_id",$input['player']);

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

        //語系
        $tmp[$k]['status'] = $status_message[$v['status']];
        $tmp[$k]['m_order'] = $v['m_order'];

        $game_id = $v["game_id"];
        $home_team_id = $v["home_team_id"];
        $away_team_id = $v["away_team_id"];

        // 有串關資料
        if ($v['m_order'] == 1) {

          $cc = GameOrder::where("m_id",$v['m_id'])->get();
          if ($cc === false) {
            $this->error(__CLASS__, __FUNCTION__, "02");
          }

          foreach ($cc as $kkk => $vvv) {
            $tmp_bet_data = array();

            $series_id = $vvv['series_id'];
            $tmp_d = AntSeriesList::where("series_id",$series_id)->where("game_id",$vvv['game_id'])->first();
            if ($tmp_d === null) {
              $tmp_bet_data['series_name'] = $vvv['series_name'];
            } else {
              $tmp_bet_data['series_name'] = $tmp_d[$name_columns];
            }
  
            $type_id = $vvv['type_id'];
            $tmp_d = AntRateList::where("rate_id",$type_id)->where("game_id",$vvv['game_id'])->first();
            if ($tmp_d === null) {
              $tmp_bet_data['type_name'] = $vvv['type_name'];
            } else {
              $tmp_bet_data['type_name'] = $tmp_d[$name_columns];
            }
            
            $replace_lang = array();
  
            $home_team_id = $vvv['home_team_id'];
            $tmp_d = AntTeamList::where("team_id",$home_team_id)->where("game_id",$vvv['game_id'])->first();
            if ($tmp_d === null) {
              $tmp_bet_data['home_team_name'] = $vvv['home_team_name'];
            } else {
              $tmp_bet_data['home_team_name'] = $tmp_d[$name_columns];
              $replace_lang[0]['tw'] = $tmp_d['name_tw'];
              $replace_lang[0]['cn'] = $tmp_d['name_cn'];
            }
  
            $away_team_id = $vvv['away_team_id'];
            $tmp_d = AntTeamList::where("team_id",$away_team_id)->where("game_id",$vvv['game_id'])->first();
            if ($tmp_d === null) {
              $tmp_bet_data['away_team_name'] = $vvv['away_team_name'];
            } else {
              $tmp_bet_data['away_team_name'] = $tmp_d[$name_columns];
              $replace_lang[1]['tw'] = $tmp_d['name_tw'];
              $replace_lang[1]['cn'] = $tmp_d['name_cn'];
            }
  
            // rate item 顯示轉化
            $item_name = $vvv['type_item_name']; // 預設
            $replace_lang[] = array("cn" => "单","tw" => "單");
            $replace_lang[] = array("cn" => "双","tw" => "雙");
            foreach ($replace_lang as $lang_k => $lang_v) {
              $item_name = str_replace($lang_v['cn'],$lang_v['tw'],$item_name);
            }
            $tmp_bet_data['type_item_name'] = $item_name;

            $tmp_bet_data['bet_rate'] = $vvv['bet_rate'];
            $tmp_bet_data['status'] = $status_message[$vvv['status']];
            $tmp_bet_data['type_priority'] = $vvv['type_priority'];

            $tmp_bet_data['home_team_logo'] = "";
            $tmp_bet_data['away_team_logo'] = "";
            
            // 取得隊伍logo
            $tmp_logo = AntTeamList::where("team_id",$home_team_id)->where("game_id",$game_id)->first();
            if (($tmp_logo === false) || ($tmp_logo == null)) {
              continue;
            }
            if (isset($tmp_logo['local_logo'])) {
              $tmp_bet_data['home_team_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'];
            }
    
            $tmp_logo = AntTeamList::where("team_id",$away_team_id)->where("game_id",$game_id)->first();
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

          $series_id = $v['series_id'];
          $tmp_d = AntSeriesList::where("series_id",$series_id)->where("game_id",$v['game_id'])->first();
          if ($tmp_d === null) {
            $tmp_bet_data['series_name'] = $v['series_name'];
          } else {
            $tmp_bet_data['series_name'] = $tmp_d[$name_columns];
          }

          $type_id = $v['type_id'];
          $tmp_d = AntRateList::where("rate_id",$type_id)->where("game_id",$v['game_id'])->first();
          if ($tmp_d === null) {
            $tmp_bet_data['type_name'] = $v['type_name'];
          } else {
            $tmp_bet_data['type_name'] = $tmp_d[$name_columns];
          }
          
          $replace_lang = array();

          $home_team_id = $v['home_team_id'];
          $tmp_d = AntTeamList::where("team_id",$home_team_id)->where("game_id",$v['game_id'])->first();
          if ($tmp_d === null) {
            $tmp_bet_data['home_team_name'] = $v['home_team_name'];
          } else {
            $tmp_bet_data['home_team_name'] = $tmp_d[$name_columns];
            $replace_lang[0]['tw'] = $tmp_d['name_tw'];
            $replace_lang[0]['cn'] = $tmp_d['name_cn'];
          }

          $away_team_id = $v['away_team_id'];
          $tmp_d = AntTeamList::where("team_id",$away_team_id)->where("game_id",$v['game_id'])->first();
          if ($tmp_d === null) {
            $tmp_bet_data['away_team_name'] = $v['away_team_name'];
          } else {
            $tmp_bet_data['away_team_name'] = $tmp_d[$name_columns];
            $replace_lang[1]['tw'] = $tmp_d['name_tw'];
            $replace_lang[1]['cn'] = $tmp_d['name_cn'];
          }

          // rate item 顯示轉化
          $item_name = $v['type_item_name']; // 預設
          $replace_lang[] = array("cn" => "单","tw" => "單");
          $replace_lang[] = array("cn" => "双","tw" => "雙");
          foreach ($replace_lang as $lang_k => $lang_v) {
            $item_name = str_replace($lang_v['cn'],$lang_v['tw'],$item_name);
          }
          $tmp_bet_data['type_item_name'] = $item_name;

          $tmp_bet_data['bet_rate'] = $v['bet_rate'];
          $tmp_bet_data['status'] = $status_message[$v['status']];
          $tmp_bet_data['type_priority'] = $v['type_priority'];
          $tmp[$k]['bet_data'][] = $tmp_bet_data;

          $tmp_bet_data['home_team_logo'] = "";
          $tmp_bet_data['away_team_logo'] = "";

          // 取得隊伍logo
          $tmp_logo = AntTeamList::where("team_id",$home_team_id)->where("game_id",$game_id)->first();
          if (($tmp_logo === false) || ($tmp_logo == null)) {
            continue;
          }
          if (isset($tmp_logo['local_logo'])) {
            $tmp_bet_data['home_team_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'];
          }
  
          $tmp_logo = AntTeamList::where("team_id",$away_team_id)->where("game_id",$game_id)->first();
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

      $this->ApiSuccess($data,"01",true); 

    }
    
    // 帳變紀錄
    public function BalanceLogs(Request $request) {
      
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
            $this->error(__CLASS__, __FUNCTION__, "02");
        }
        
        $name_columns = "name_".$api_lang;

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

        $return = PlayerBalanceLogs::where("player_id",$player_id)
        ->skip($skip)->take($page_limit)->orderBy('id', 'DESC')->get();
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

        $this->ApiSuccess($data,"01",true); 

    }
  ////////////// ////////////// ////////////// ////////////// //////////////
  


  ////////////// ////////////// ////////////// ////////////// //////////////
  


  ////////////// ////////////// ////////////// ////////////// //////////////
  protected function gzip($data) {

    $data = json_encode($data,true);
    // 使用 gzcompress() 函數進行壓縮
    $compressedData = gzcompress($data);

    // 使用 base64_encode() 函數進行 base64 編碼
    $base64Data = base64_encode($compressedData);

    return $base64Data;
  }

  private static function compareRateValue($a, $b) {
    return strcmp($a["rate_value"], $b["rate_value"]);
  }

  private static function compareRateValueB($a, $b) {
    return strcmp($a["order_by"], $b["order_by"]);
  }

  private static function compareRateValueC($a, $b) {
    return strcmp($a["id"], $b["id"]);
  }

  protected function customExplode($str) {

    $data = array();
    if (strpos($str, ' ') !== false) {
        // 字串包含空白，以空白符分割
        $data['filter'] = 0;
        $tmp = explode(' ', $str);
        $data['value'] = $tmp;
    } elseif (strpos($str, '+') !== false) {
      $data['filter'] = 1;
      $tmp = explode('+', $str);
      $data['value'] = $tmp;
    } elseif (strpos($str, '-') !== false) {
      $data['filter'] = 2;
      $tmp = explode('-', $str);
      $data['value'] = $tmp;
    }

    return $data;
  }
  ////////////// ////////////// ////////////// ////////////// //////////////

  // Api Success
  protected function ApiSuccess($data,$message,$gzip=false) {

    $success_code = strtoupper("SUCCESS_" . $this->controller . "_" . $this->function . "_" . $message);

    $tmp = array();
    $tmp['status'] = 1;
    $tmp['data'] = $data;
    $tmp['message'] = $success_code;
    $tmp['gzip'] = 0;
    if ($gzip) {
      $tmp['gzip'] = 1;
    }
    
    echo json_encode($tmp,true);
    exit();
  }

  // Api Error
  protected function ApiError($message , $is_common = false,$gzip=false) {

    if ($is_common === false) {
      // CLASS_FUNCTION ONLY
      $error_code = strtoupper("ERROR_" . $this->controller . "_" . $this->function . "_" . $message);
    } else {
      // 通用錯誤類
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
    
    echo json_encode($tmp,true);
    exit();

  }
    
  // check token
  protected function checkToken($input) {
    
    $player_id = $input['player'];
    $token = $input['token'];
    
    $return = PlayerOnline::where("player_id",$player_id)->where("token",$token)->where("status",1)->count();
    if ($return == 0) {
      return false;
    }

    return true;

  }

}

