<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MobileController extends Controller {
    
    // 首頁
    public function index(Request $request) {

		  $session = Session::all();

      $is_login = $session['is_login'];
      if ($is_login != 1) {
        $this->ajaxError("PLAYER_RELOGIN");
      }

      $player = $session['player'];

      $player_id = $player['id'];
      $player_token = "12345";

      $this->assign("player",$player_id);
      $this->assign("token",$player_token);
      
    	/////////////////////////
      // 取得語系
      $player_id = $session['player']['id'];
      $api_lang = $this->getAgentLang($player_id);
      if ($api_lang === false) {
        $this->error(__CLASS__, __FUNCTION__, "02");
      }
      $this->assign("lang",$api_lang);

      // 注單status 
      $order_status = [
        0 => "已取消",
        1 => "等待審核",
        2 => "等待開獎",
        3 => "等待派獎",
        4 => "已派獎"
      ];
      $this->assign("order_status",$order_status);


      return view('mobile.index',$this->data);
    }

    // Match
    public function match(Request $request) {
      return view('mobile.match',$this->data);
    }

    // Game
    public function game(Request $request) {
      return view('mobile.game',$this->data);
    }

    
}

