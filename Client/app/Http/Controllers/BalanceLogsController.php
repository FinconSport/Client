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

      return view('balance_logs.index',$this->data);
      
    }
    
}
