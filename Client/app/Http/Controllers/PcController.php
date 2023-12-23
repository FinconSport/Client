<?php
/*

	PC端 場景類

*/
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
// use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerOnline;
use App\Models\ClientMarquee;
use App\Models\Player;
use App\Models\Agent;

class PcController extends Controller {

    // 設定當前時間
    protected function getCurrentTime() {
		$current_time = time();
		$this->assign("current_time",$current_time);
	}

	// Marquee 取得NoticeList 公告列表 , PC端限定,  映射至頁面
	protected function getMarqueeList($lang = 'cn') {

		$lang_key = "title_" . $lang;

		$days = date('Y-m-d', strtotime('-1 days'));
		$return = AntNoticeList::where("create_time",">=",$days)->orderBy("id","DESC")->take(10)->get();

		$list = array();

		// 系統公告 （預留）
		$tmp = array();
		$tmp['title'] =  "系統公告DEMO";
		$tmp['context'] =  "系統公告系統公告系統公告系統公告";
		$list[] = $tmp;

		// 各種球類公告
		foreach ($return as $k => $v) {
			$tmp = array();
			$tmp['title'] =  $v['title_'.$lang];
			$tmp['context'] =  $v['context_'.$lang];
			$list[] = $tmp;
		}

		ksort($list);

		$this->assign("marquee_list",$list);
  
	}

	// 輸出player,token
	protected function getPlayerAndToken($session) {
  
		$player = $session['player']['id'];
  
		$return = PlayerOnline::where("player_id",$player)->first();

		$token = $return['token'];
  
		$this->assign("player",$player);
		$this->assign("token",$token);
	}

	// Api Success
	protected function ApiSuccess($data,$message,$gzip=false) {

		$success_code = strtoupper("SUCCESS_" . $this->controller . "_" . $this->function . "_" . $message);

		$tmp = array();
		$tmp['status'] = 1;
		$tmp['data'] = $data;
		$tmp['message'] = $success_code;
		$tmp['gzip'] = 0;
		if ($gzip) {
		$tmp['gzip'] = 1;
		}
		
		echo json_encode($tmp,true);
		exit();
	}

	// Api Error
	protected function ApiError($message , $is_common = false,$gzip=false) {

		if ($is_common === false) {
		// CLASS_FUNCTION ONLY
		$error_code = strtoupper("ERROR_" . $this->controller . "_" . $this->function . "_" . $message);
		} else {
		// 通用錯誤類
		$error_code = strtoupper("ERROR_COMMON_" . $message);
		}      
		
		$tmp = array();
		$tmp['status'] = 0;
		$tmp['data'] = null;
		$tmp['message'] = $error_code;
		$tmp['gzip'] = 0;
		if ($gzip) {
		$tmp['gzip'] = 1;
		}
		
		echo json_encode($tmp,true);
		exit();

	}


}
