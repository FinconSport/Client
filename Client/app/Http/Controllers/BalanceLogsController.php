<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;

use App\Models\Player;
use App\Models\PlayerBalanceLogs;


class BalanceLogsController extends PcController {

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

      if ((!isset($input['page'])) || ($input['page'] == "")) {
        $input['page'] = 1; // 預設1 
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

      // 帳變類型
      $typeList = trans("pc.BalanceLogs_TypeList");
      $this->assign("type_list",$typeList);

      $page_limit = $this->page_limit;
      $page = $input['page'];
      $skip = ($page-1)*$page_limit;

      //////////////////////////

      $mBalanceLogs = PlayerBalanceLogs::where("player_id",$session['player']['id']);
      if (isset($input['id']) && ($input['id'] != "")) {
        $mBalanceLogs = $mBalanceLogs->where("id",$input['id']);
      }

      if (isset($input['start_time']) && ($input['start_time'] != "")) {
        $mBalanceLogs = $mBalanceLogs->where("create_time",">=",$input['start_time']);
      }

      if (isset($input['end_time']) && ($input['end_time'] != "")) {
        $mBalanceLogs = $mBalanceLogs->where("create_time","<=",$input['end_time']);
      }

      if (isset($input['type']) && ($input['type'] != "")) {
        $mBalanceLogs = $mBalanceLogs->where("type",$input['type']);
      }

      $groupedData = $mBalanceLogs->orderBy('id', 'DESC')->get();
      $pagination = count($groupedData);

      $return = $mBalanceLogs->skip($skip)->take($page_limit)->orderBy('id', 'DESC')->get();
      if ($return === false) {
        $this->error(__CLASS__, __FUNCTION__, "04");
      }

      $list = array();
      foreach ($return as $k => $v) {
        
        if (isset($typeList[$v['type']])) {
          $v['type'] = $typeList[$v['type']];
        }

        $list[] = $v;

      } 

      $this->assign("list",$list);
      
      // pagination

      $tmp = array();
      $tmp['max_count'] = $pagination;
      $tmp['max_page'] = ceil($pagination/$page_limit)+0;
      $tmp['current_page'] = $page+0;

      $this->assign("pagination",$tmp);



      return view('balance_logs.index',$this->data);
      
    }
    
}
