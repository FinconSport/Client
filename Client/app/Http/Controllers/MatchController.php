<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;

// use App\Models\GameOrder;

class MatchController extends PcController {
    
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

      if ((!isset($input['sport'])) || ($input['sport'] == "")) {
        $input['sport'] = 1;  // 預設1 , 足球
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

    //   // 取得體育種類列表
    //   $this->getGameList($api_lang);
      
    //   // 取得公告資料
    //   $this->getNoticeList($api_lang);

    //   // 取得熱門聯賽列表
    //   $this->getHotSeriesList($input['sport'],$api_lang);

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

      $this->assign("status_list",$status);
    	/////////////////////////

      // 取得比賽資料

      $page_limit = $this->page_limit;
      $page = $input['page'];
      $skip = ($page-1)*$page_limit;

      $sport_id = $input['sport'];

      // form search
        
      ////////////////////////////////////
      
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

      $this->assign("data",$data);

      ////////////////////////////////////

      return view('match.index',$this->data);
    }

}

