<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;

// use App\Models\GameOrder;

class MatchListController extends PcController {
    
    // 首頁
    public function index(Request $request) {
    	
    	$input = $this->getRequest($request);

		  $session = Session::all();

    	/////////////////////////

      return view('match_list.index',$this->data);
    }

}

