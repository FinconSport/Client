<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\Agent;
use App\Models\Player;
use App\Models\PlayerOnline;

class LoginController extends Controller {
    
    // 首頁

    // for test , https://sportc.shopping166.net/login?token=12345&player=1&m=1
    public function index(Request $request) {

    	$input = $this->getRequest($request);

		  $session = Session::all();

    	/////////////////////////
      
      $default_page = "/login";
		
      $player_id = $input['player'];
      $token = $input['token'];
      $is_mobile = 0;
      if (isset($input['m'])) {
        $is_mobile = $input['m'];
      } 
      $return = PlayerOnline::where("player_id",$player_id)->where("token",$token)->count();
      if ($return == 0) {
        $this->ajaxError("ERROR_login_01");
      }

      $return = Player::where("id",$player_id)->first();
      if ($return === false) {
        $this->ajaxError("ERROR_login_02");
      }

      $agent_id = $return['agent_id'];
      $agent = Agent::where("id",$agent_id)->first();
      if ($return === false) {
        $this->ajaxError("ERROR_login_03");
      }
      $lang = $agent['api_lang'];
      $limit_data = $agent['limit_data'];

      $return['lang'] = $lang;
      $return['limit_data'] = $limit_data;

      // 寫入session
    	Session::put("player",$return);
    	Session::put("is_login",1);
      Session::Save();

      //////////////////////////
      
      $return = Player::where("id",$player_id)->update([
          "last_update" => date("Y-m-d H:i:s")
      ]);
      if ($return === false) {
        $this->ajaxError("ERROR_login_04");
      }


      if ($is_mobile == 1) {
        return redirect("/mobile");
      } else {
        return redirect("/index"); 
      }

    }


    
}
