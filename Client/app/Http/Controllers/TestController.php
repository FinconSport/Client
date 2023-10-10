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
    
      // list 方法
      $return = Player::where("status",1)->where("currency_type",1)->list();
      if ($return === false) {
        
      }

      // fetch 方法
      $return = Player::where("status",1)->where("id",1)->where("currency_type",1)->fetch();
      if ($return === false) {
        
      }
      dd($return);
      

      
    }


}