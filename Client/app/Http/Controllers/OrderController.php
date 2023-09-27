<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;

// use App\Models\AntGameList;
// use App\Models\AntMatchList;
// use App\Models\AntRateList;
// use App\Models\AntSeriesList;
// use App\Models\AntTeamList;
// use App\Models\AntTypeList;
// use App\Models\GameOrder;

use App\Models\LsportSport;
use App\Models\LsportLeague;
use App\Models\LsportFixture;
use App\Models\LsportMarket;
use App\Models\LsportMarketBet;
use App\Models\LsportTeam;

use App\Models\SystemConfig;
use App\Models\Player;
use App\Models\Agent;
use App\Models\PlayerBalanceLogs;

class OrderController extends PcController {
    
    // 首頁
    public function index(Request $request) {

    	$input = $this->getRequest($request);

		  $session = Session::all();

    	/////////////////////////

      // 判斷是否登入
      if (isset($session['is_login'])) {
        if ($session['is_login'] != 1) {
          $this->error(__CLASS__, __FUNCTION__, "01");
        }
      } else {
        $this->error(__CLASS__, __FUNCTION__, "01");
      }

      // 輸入判定
      if ((!isset($input['menu'])) || ($input['menu'] == "")) {
        $input['menu'] = 0;  // 預設未結算
      }

      if ((!isset($input['sport'])) || ($input['sport'] == "")) {
        $input['sport'] = 1;  // 預設1 , 足球
      }

      if ((!isset($input['page'])) || ($input['page'] == "")) {
        $input['page'] = 1; // 預設1 
      }

      // 格式處理
      $input_columns = array("page","order_id","type_id","sport",'league_id',"status");
      foreach ($input_columns as $k => $v) {
        if (isset($input[$v])) {
          if ($input[$v] != "") {
            $input[$v] = intval($input[$v]);
          } else {
            unset($input[$v]);
          }
        }
      }

      $this->assign("search",$input);
      
      $this->getCurrentTime();
      
      // 輸出玩家資料
      $this->setPlayerInfo($session);


    	/////////////////////////
      // 取得語系
      $player_id = $session['player']['id'];
      $api_lang = $this->getAgentLang($player_id);
      if ($api_lang === false) {
        $this->error(__CLASS__, __FUNCTION__, "02");
      }
      $this->assign("lang",$api_lang);

    	/////////////////////////
      // Search 區用

      // 取得體育種類列表
      $this->getGameList($api_lang);
      
      // 取得公告資料
      $this->getNoticeList($api_lang);

      // 取得熱門聯賽列表
      $this->getHotSeriesList($input['sport'],$api_lang);

      // 玩法
      $type = [
        1 => "全場大小",
        2 => "全場讓球",
        3 => "上半場大小",
        4 => "上半場讓球",
        5 => "全場獨贏",
        6 => "上半場獨贏",
        7 => "全場波膽",
        8 => "上半場波膽",
      ];

      $this->assign("type_list",$type);
      
      // 狀態
      $status = [
        0 => "已取消",
        1 => "等待審核",
        2 => "等待開獎",
        3 => "等待派獎",
        4 => "已派獎"
      ];

      $this->assign("status_list",$status);
    	/////////////////////////

      $page_limit = $this->page_limit;
      $page = $input['page'];
      $skip = ($page-1)*$page_limit;

      $sport_id = $input['sport'];

      // form search
      $GameOrder = GameOrder::where("player_id",$session['player']['id']);
      $groupedData = GameOrder::select('m_id')->where("player_id",$session['player']['id']);
        
      if (isset($input['order_id']) && ($input['order_id'] != "")) {
        $GameOrder = $GameOrder->where('id',$input['order_id']);
        $groupedData = $groupedData->where('id',$input['order_id']);
      }
        
      if (isset($input['sport']) && ($input['sport'] != "")) {
        $GameOrder = $GameOrder->where('sport_id',$input['sport']);
        $groupedData = $groupedData->where('sport_id',$input['sport']);
      }
  
      if (isset($input['league_id']) && ($input['league_id'] != "")) {
        $GameOrder = $GameOrder->where('league_id',$input['league_id']);
        $groupedData = $groupedData->where('league_id',$input['league_id']);
      }
  
      if (isset($input['type_id']) && ($input['type_id'] != "")) {
        $GameOrder = $GameOrder->where("type_id",$input['type_id']);
        $groupedData = $groupedData->where("type_id",$input['type_id']);
      }
  
      if (isset($input['status']) && ($input['status'] != "")) {
        $GameOrder = $GameOrder->where("status",$input['status']);
        $groupedData = $groupedData->where("status",$input['status']);
      }
  
      if (isset($input['start_time']) && ($input['start_time'] != "")) {
        $GameOrder = $GameOrder->where("create_time",">=",$input['start_time']);
        $groupedData = $groupedData->where("create_time",">=",$input['start_time']);
      }
  
      if (isset($input['end_time']) && ($input['end_time'] != "")) {
        $GameOrder = $GameOrder->where("create_time","<",$input['end_time']);
        $groupedData = $groupedData->where("create_time","<",$input['end_time']);
      }

      $groupedData = $groupedData->groupBy('m_id')->pluck('m_id');
      $pagination = count($groupedData);

      $return = $GameOrder->skip($skip)->take($page_limit)->groupBy('m_id')->orderBy('m_id', 'DESC')->get();

      ///////////////////////

      $columns = array(
        'id',
        "m_id",
        "m_order",
        "bet_amount",
        "result_data",
        "result_amount",
        "create_time",
        "result_time",
        "status"
      );

      $total = array();
      $total['bet_amount'] = 0;
      $total['result_amount'] = 0;
      $data = array();
      $tmp = array();
      foreach ($return as $k => $v) {

        foreach ($columns as $kk => $vv) {
          $tmp[$k][$vv] = $v[$vv]; 
        }

        // 有串關資料
        if ($v['m_order'] == 1) {

          $cc = GameOrder::where("m_id",$v['m_id'])->get();
          if ($cc === false) {
            $this->error(__CLASS__, __FUNCTION__, "03");
          }

          foreach ($cc as $kkk => $vvv) {
            
            $name_columns = "name_".$api_lang;
            $tmp_bet_data = array();

            $league_id = $vvv['league_id'];
            $tmp_d = LsportLeague::where('league_id',$league_id)->where('sport_id',$vvv['sport_id'])->first();
            if ($tmp_d === false) {
              $this->error(__CLASS__, __FUNCTION__, "04");
            }
            if ($tmp_d === null) {
              $tmp_bet_data['league_name'] = $vvv['league_name'];
            } else {
              $tmp_bet_data['league_name'] = $tmp_d[$name_columns];
            }
  
            $type_id = $vvv['type_id'];
            $tmp_d = LsportMarketBet::where('bet_id',$type_id)->where('sport_id',$vvv['sport_id'])->first();
            if ($tmp_d === false) {
              $this->error(__CLASS__, __FUNCTION__, "05");
            }
            if ($tmp_d === null) {
              $tmp_bet_data['type_name'] = $vvv['type_name'];
            } else {
              $tmp_bet_data['type_name'] = $tmp_d[$name_columns];
            }
            
            $replace_lang = array();
  
            $home_team_id = $vvv['home_team_id'];
            $tmp_d = LsportTeam::where("team_id",$home_team_id)->where('sport_id',$vvv['sport_id'])->first();
            if ($tmp_d === false) {
              $this->error(__CLASS__, __FUNCTION__, "06");
            }
            if ($tmp_d === null) {
              $tmp_bet_data['home_team_name'] = $vvv['home_team_name'];
            } else {
              $tmp_bet_data['home_team_name'] = $tmp_d[$name_columns];
              $replace_lang[0]['tw'] = $tmp_d['name_tw'];
              $replace_lang[0]['cn'] = $tmp_d['name_cn'];
            }
  
            $away_team_id = $vvv['away_team_id'];
            $tmp_d = LsportTeam::where("team_id",$away_team_id)->where('sport_id',$vvv['sport_id'])->first();
            if ($tmp_d === false) {
              $this->error(__CLASS__, __FUNCTION__, "07");
            }
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

            $tmp_bet_data['home_team_score'] = $vvv['home_team_score'];
            $tmp_bet_data['away_team_score'] = $vvv['away_team_score'];
            $tmp_bet_data['bet_rate'] = $vvv['bet_rate'];
            $tmp_bet_data['status'] = $status[$vvv['status']];
            $tmp_bet_data['type_priority'] = $vvv['type_priority'];
            $tmp[$k]['bet_data'][] = $tmp_bet_data;
          }
        } else {

          $name_columns = "name_".$api_lang;
          $tmp_bet_data = array();

          $league_id = $v['league_id'];
          $tmp_d = LsportLeague::where('league_id',$league_id)->where('sport_id',$v['sport_id'])->first();
          if ($tmp_d === false) {
            $this->error(__CLASS__, __FUNCTION__, "08");
          }
          if ($tmp_d === null) {
            $tmp_bet_data['league_name'] = $v['league_name'];
          } else {
            $tmp_bet_data['league_name'] = $tmp_d[$name_columns];
          }

          $type_id = $v['type_id'];
          $tmp_d = LsportMarketBet::where('bet_id',$type_id)->where('sport_id',$v['sport_id'])->first();
          if ($tmp_d === false) {
            $this->error(__CLASS__, __FUNCTION__, "09");
          }
          if ($tmp_d === null) {
            $tmp_bet_data['type_name'] = $v['type_name'];
          } else {
            $tmp_bet_data['type_name'] = $tmp_d[$name_columns];
          }
          
          $replace_lang = array();

          $home_team_id = $v['home_team_id'];
          $tmp_d = LsportTeam::where("team_id",$home_team_id)->where('sport_id',$v['sport_id'])->first();
          if ($tmp_d === false) {
            $this->error(__CLASS__, __FUNCTION__, "10");
          }
          if ($tmp_d === null) {
            $tmp_bet_data['home_team_name'] = $v['home_team_name'];
          } else {
            $tmp_bet_data['home_team_name'] = $tmp_d[$name_columns];
            $replace_lang[0]['tw'] = $tmp_d['name_tw'];
            $replace_lang[0]['cn'] = $tmp_d['name_cn'];
          }

          $away_team_id = $v['away_team_id'];
          $tmp_d = LsportTeam::where("team_id",$away_team_id)->where('sport_id',$v['sport_id'])->first();
          if ($tmp_d === false) {
            $this->error(__CLASS__, __FUNCTION__, "11");
          }
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

          $tmp_bet_data['home_team_score'] = $v['home_team_score'];
          $tmp_bet_data['away_team_score'] = $v['away_team_score'];
          $tmp_bet_data['bet_rate'] = $v['bet_rate'];
          $tmp_bet_data['status'] = $status[$v['status']];
          $tmp_bet_data['type_priority'] = $v['type_priority'];
          $tmp[$k]['bet_data'][] = $tmp_bet_data;
        }

        $total['bet_amount'] += $v['bet_amount'];
        $total['result_amount'] += $v['result_amount'];

      }

      $data['list'] = $tmp;
      $data['total'] = $total;
      $this->assign("data",$data);

      // pagination

      $tmp = array();
      $tmp['max_count'] = $pagination;
      $tmp['max_page'] = ceil($pagination/$page_limit)+0;
      $tmp['current_page'] = $page+0;

      $this->assign("pagination",$tmp);

      /////////////////////////

      // 菜單統計
      $return = $this->menu_count($session['player']['id']);
      if ($return === false) {
        $this->error(__CLASS__, __FUNCTION__, "12");
      }

      $this->assign("menu_count",$return);

      return view('order.index',$this->data);
    }

    // 注單列表測試頁
    public function test(Request $request) {

      $return = GameOrder::select('*', DB::raw('count(id) as total'))
      ->groupBy('m_id')
      ->get();

      foreach ($return as $k => $v) {
        echo $v['id'] . " - " . $v['m_id'] . " - " . $v['total'] . "<br>";
      }
      

    }

    // 賽事列表- 投注接口
    public function create_order(Request $request) {
      
    	$input = $this->getRequest($request);

		  $session = Session::all();

    	/////////////////////////

      // 判斷是否登入
      if (isset($session['is_login'])) {
        if ($session['is_login'] != 1) {
          $this->error(__CLASS__, __FUNCTION__, "01");
        }
      } else {
        $this->error(__CLASS__, __FUNCTION__, "01");
      }

      //////////////////////////////////////////

      // 判斷輸入參數
      $check_columns = array("bet_match","bet_type","bet_type_item","bet_rate","bet_amount","better_rate");
      foreach ($check_columns as $k => $v) {
        $tmp = $input[$v]+0;
        if ($tmp != $input[$v]) {
          $this->ApiError($v."_13");
        }
      }


      // 取得系統參數
      $return = SystemConfig::where("name","risk_order")->first();
      if ($return['value'] > 0) {
        $default_order_status = 1;
        $default_approval_time = null;
      } else {
        // 預設通過
        $default_order_status = 2;
        $default_approval_time = date('Y-m-d H:i:s');
      }

      // 取得必要參數
      $player_id = $session['player']['id'];
      $fixture_id = $input['bet_match'];
      $bet_type_id = $input['bet_type'];
      $bet_type_item_id = $input['bet_type_item'];
      $player_rate = $input['bet_rate'];
      $bet_amount = $input['bet_amount'];
      $is_better_rate = $input['better_rate'];
      
      $sport_id = 1;
      if (isset($input['sport_id'])) {
        $sport_id = $input['sport_id'];
      }
      
      $order = array();
      
      // 參數檢查 TODO - 初步 隨便弄弄
      if ($bet_amount <= 0) {
        $this->ApiError("01");
      }

      // 取得用戶資料
      $return = Player::where('id',$player_id)->first();
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
      $return = Agent::where('id',$agent_id)->first();
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
      $return = LsportFixture::where('fixture_id',$fixture_id)->where('sport_id',$sport_id)->first();
      if ($return == false) {
        $this->ApiError("07");
      }

      //match status : 1未开始、2进行中、3已结束、4延期、5中断、99取消
      if ($return['status'] >= 3) {
        $this->ApiError("08");
      }

      // decode 聯盟
      $series_data = json_decode($return['series'],true);
      //////////////////////////////////////////
      // order data
      $order['league_id'] = $series_data['id'];
      $order['league_name'] = $series_data['name_cn'];
      $order['fixture_id'] = $fixture_id;
      $order['sport_id'] = $return['sport_id'];
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
      $return = LsportMarketBet::where('bet_id',$bet_type_id)->where('fixture_id',$fixture_id)->first();
      if ($return == false) {
        $this->ApiError("09");
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
      if (($rate_status != 1 ) || ($current_rate_status != 1)) {
        $this->ApiError("14");
      }

      //////////////////////////////////////////
      // order data
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
        $this->ApiError("10");
      }

      //////////////////////////////////////////
      // order data
      $order['bet_amount'] = $bet_amount;
      $order['status'] = $default_order_status;
      $order['create_time'] = date('Y-m-d H:i:s');
      $order['approval_time'] = $default_approval_time;
      
      //////////////////////////////////////////

      // 新增注單資料
      $return = GameOrder::insertGetId($order);      
      if ($return == false) {
        $this->ApiError("11");
      }

      // 填入m_id
      $m_order_id = $return;
      $return = GameOrder::where('id',$m_order_id)->update([
        "m_id" => $m_order_id
      ]);      
      if ($return == false) {
        $this->ApiError("12");
      }

      // 扣款
      $before_amount = $player_balance;
      $change_amount = $bet_amount;
      $after_amount = $before_amount - $change_amount;

      $return = Player::where('id',$player_id)->update([
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
      $tmp['create_time'] = date('Y-m-d H:i:s');
      PlayerBalanceLogs::insert($tmp);

      $this->ApiSuccess($after_amount,"01");

    }

    // 賽事列表- 串關投注接口
    public function m_create_order(Request $request) {
      
    	$input = $this->getRequest($request);

		  $session = Session::all();

    	/////////////////////////

      // 判斷是否登入
      if (isset($session['is_login'])) {
        if ($session['is_login'] != 1) {
          $this->error(__CLASS__, __FUNCTION__, "01");
        }
      } else {
        $this->error(__CLASS__, __FUNCTION__, "01");
      }

      //////////////////////////////////////////

      // 判斷輸入參數
      $check_columns = array("bet_data","bet_amount","better_rate");
      foreach ($check_columns as $k => $v) {
        if (!isset($input[$v]) && ($input[$v] == "")) {
          $this->ApiError($v."_13");
        }
      }

      $bet_data = json_decode($input['bet_data'],true);
      // 串關注數判斷 , 最小min_m_order,  最大max_m_order
      $min_m_order = $this->system_config['min_m_order'];
      $max_m_order = $this->system_config['max_m_order'];
      if ((count($bet_data) < $min_m_order) && (count($bet_data) > $max_m_order)) {
        $this->ApiError("14");
      }

      // 取得系統參數
      $risk_order = $this->system_config['risk_order']; 
      if ($risk_order > 0) {
        $default_order_status = 1;
        $default_approval_time = null;
      } else {
        // 預設通過
        $default_order_status = 2;
        $default_approval_time = date('Y-m-d H:i:s');
      }


      // 取第一筆串關注單做為串關id 
      $m_order_id = false;

      // 取第一筆串關注單的game_id
      $m_game_id = false;

      // 串關批量處理訂單
      foreach ($bet_data as $k => $v) {
        // 取得必要參數
        $player_id = $session['player']['id'];
        $fixture_id = $v['bet_match'];
        $bet_type_id = $v['bet_type'];
        $bet_type_item_id = $v['bet_type_item'];
        $player_rate = $v['bet_rate'];
        $bet_amount = $input['bet_amount'];
        $is_better_rate = $input['better_rate'];

        $sport_id = 1;
        if (isset($input['sport_id'])) {
          $sport_id = $input['sport_id'];
        }

        $order = array();
      
        // 參數檢查 TODO - 初步 隨便弄弄
        if ($bet_amount <= 0) {
          $this->ApiError("01");
        }
  
        // 取得用戶資料
        $return = Player::where('id',$player_id)->first();
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
        $return = Agent::where('id',$agent_id)->first();
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
        $return = LsportFixture::where('fixture_id',$fixture_id)->where('sport_id',$sport_id)->first();
        if ($return == false) {
          $this->ApiError("07");
        }
  
        // 判斷注單 是否為同一game_id
        if ($m_game_id === false) {
          $m_game_id = $return['sport_id'];
        } else {
          if ($m_game_id != $return['sport_id']) {
            $this->ApiError("07");
          }
        }

        //match status : 1未开始、2进行中、3已结束、4延期、5中断、99取消
        // 串關只能賽前注單
        if ($return['status'] != 1) {
          $this->ApiError("08");
        }
  
        // decode 聯盟
        $series_data = json_decode($return['series'],true);
        //////////////////////////////////////////
        // order data
        $order['league_id'] = $series_data['id'];
        $order['league_name'] = $series_data['name_cn'];
        $order['fixture_id'] = $fixture_id;
        $order['sport_id'] = $return['sport_id'];
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
        $return = LsportMarketBet::where('bet_id',$bet_type_id)->where('fixture_id',$fixture_id)->first();
        if ($return === false) {
          $this->ApiError("09");
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
        if (($rate_status != 1 ) || ($current_rate_status != 1)) {
          $this->ApiError("14");
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
          $this->ApiError("10");
        }
  
        //////////////////////////////////////////
        // order data
        $order['bet_amount'] = $bet_amount;
        $order['status'] = $default_order_status;
        $order['create_time'] = date('Y-m-d H:i:s');
        $order['approval_time'] = $default_approval_time;
        
        //////////////////////////////////////////
  
        // 新增注單資料
        $return = GameOrder::insertGetId($order);      
        if ($return == false) {
          $this->ApiError("11");
        }

        // 設定串關id , 這是第一筆注單
        if ($m_order_id === false) {
          $m_order_id = $return;
          $return = GameOrder::where('id',$m_order_id)->update([
            "m_id" => $m_order_id
          ]);      
          if ($return == false) {
            $this->ApiError("12");
          }
        }
      }

      // 扣款
      $before_amount = $player_balance;
      $change_amount = $bet_amount;
      $after_amount = $before_amount - $change_amount;

      $return = Player::where('id',$player_id)->update([
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
      $tmp['create_time'] = date('Y-m-d H:i:s');
      PlayerBalanceLogs::insert($tmp);

      $this->ApiSuccess($after_amount,"01");

    }

    ///////////////////////////////////////////////
    ///////////////////////////////////////////////
    ///////////////////////////////////////////////

}

