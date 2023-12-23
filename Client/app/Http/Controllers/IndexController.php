<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;

use App\Models\Player;
use App\Models\Agent;


class IndexController extends PcController {

  protected $match_status = [
    -1 => "異常",
    1 => "等待開賽",
    2 => "進行中",
    3 => "已結束",
    4 => "延期",
    5 => "中斷",
    99 => "取消"
  ];
    
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

      ////////////////////////////////////

      if ($this->controller == "m_order") {
        return view('index.m_order',$this->data);
      }
      
      return view('index.index',$this->data);
      
  }

  // account
  public function account(Request $request) {

    	$input = $this->getRequest($request);

		  $session = Session::all();

    	/////////////////////////

      // 判斷是否登入
      if (isset($session['is_login'])) {
        if ($session['is_login'] != 1) {
          $this->ApiError("01");
        }
      } else {
        $this->ApiError("01");
      }
      
      // 獲取用戶資料
      $player_id = $session['player']['id'];
      
      $return = Player::where("id",$player_id)->first();
      
      if ($return === false) {
        $this->ApiError("02");
      }

      if ($return['status'] != 1) {
        $this->ApiError("03");
      }

      $data = array();
      $data['account'] = $return['account'];
      $data['balance'] = $return['balance'];
      
      $this->ApiSuccess($data,"04");
  }

    ///////////////////////////////////////////////
    ///////////////////////////////////////////////
    ///////////////////////////////////////////////


}
