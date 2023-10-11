<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;
use App\Models\Player;
use App\Models\LsportFixture;
use App\Models\LsportSport;

class TestController extends PcController {
    
    // 首頁
    public function index(Request $request) {
    
      // list方法 , 同get方法
      $return = Player::where("status",1)->where("currency_type",1)->list();

      // fetch方法 , 同first 方法
      $return = Player::where("status",1)->where("id",1)->where("currency_type",1)->fetch();

      // total方法, 專門用於取得統計
      $return = Player::select('agent_id', DB::raw('SUM(balance) as total_balance'), DB::raw('COUNT(*) as player_count'))->groupBy('agent_id')->total();

      // 多表關聯搜尋 , 需要用別名
      $return = LsportFixture::select('f.*')
      ->from('es_lsport_fixture as f')
      ->join('es_lsport_sport as s', 'f.sport_id', '=', 's.sport_id')
      ->where('s.status', '=', 1)
      ->skip(100)->take(10)  // Limit 100,
      ->orderBy("f.start_time","DESC")
      ->list();

      ////////////////////////

      dd($return);
  
      if ($return === false) {

      }
      
    }


}