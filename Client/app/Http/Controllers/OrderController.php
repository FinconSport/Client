<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;

use App\Models\GameOrder;
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

    ///////////////////////////////////////////////
    ///////////////////////////////////////////////
    ///////////////////////////////////////////////

}

