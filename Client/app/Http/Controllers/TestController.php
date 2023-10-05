<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;

use App\Models\LsportSport;

class TestController extends PcController {
    
    // 首頁
    public function index(Request $request) {
    

      $dd = LsportSport::getName(154914,'en');
      dd($dd);

      $dd = LsportSport::getName(154914,'tw');
      dd($dd);

    }


}