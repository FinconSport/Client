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

      $return = Player::where("status",1);
      $return = $return->list();
      dd($return);
      

      
    }


}