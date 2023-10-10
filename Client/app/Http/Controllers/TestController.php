<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;
use App\Models\Player;


use App\Models\LsportSport;

class TestController extends PcController {
    
    // 首頁
    public function index(Request $request) {
    
      $data['sport_id'] = 154914;
      $data['api_lang'] = 'tw';

      //$dd = LsportSport::getName($data);

      $sql = "select * from es_player";
      // $dd = LsportSport::getESQuery($sql);
      //$dd = LsportSport::getESAgg($sql);

      $mPlayer = Player::where("status",1)->whereIn("id",[1,2]);

    // Cache::reember() {  
      // select * from player where status = 1
      // player => es_player 
      // select * from es_player where status = 1
      // -> ES . 
    // }
      $return = $mPlayer->list();

      dd($return);
  
      foreach ($return as $k => $v) {

        $sport_id = $v['sport_id'];
        $sport_nanme = xxxx::getName($sport_id);

        $columns = ["sport, league , team"];
        foreach ($columns as $kk => $vv) {
          $$vv = $v[$vv];
          $sport_nanme = xxxx::getName($sport_id);
        }
      }
      dd($return);
      

      
    }


}