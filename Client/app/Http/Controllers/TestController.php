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
    
      // list方法 , 同get方法
      $return = Player::where("status",1)->where("currency_type",1)->list();

      // fetch方法 , 同first 方法
      $return = Player::where("status",1)->where("id",1)->where("currency_type",1)->fetch();

      // total方法, 專門用於取得統計

      // report方法, 包裝統計與list , 用於報表

      dd($return);
      
      // success => array , fail => false
      if ($return === false) {

      }
      
    }


}