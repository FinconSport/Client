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
  
	// 取得NoticeList 公告列表 , PC端限定,  映射至頁面
	// protected function getNoticeList($api_lang = 'cn') {

	// 	$days = date('Y-m-d 00:00:00', strtotime('-1 days'));

	// 	$notice_list = array();

	// 	// 系統公告
	// 	$return = ClientMarquee::where("status",1)->get();      
	// 	if ($return === false) {
	// 	  $this->ApiError("01");
	// 	}
  
	// 	foreach ($return as $k => $v) {
	// 	  $game_id = 0;
	// 	  $title = $v['title'];
	// 	  $context = $v['marquee'];
	// 	  $create_time = $v['create_time'];
  
	// 	  $notice_list[$game_id][] = [
	// 		"game_id" => $game_id,
	// 		"title" => $title,
	// 		"context" => $context,
	// 		"create_time" => $create_time,
	// 	  ];
	// 	}
  
	// 	/////////////////
  
	// 	$return = AntNoticeList::where('create_time',">=", $days)->orderBy("create_time","DESC")->get();
	// 	if ($return === false) {
	// 	  $this->ApiError("02");
	// 	}
  
	// 	foreach ($return as $k => $v) {
	// 	  $game_id = $v['game_id'];
	// 	  $title = $v['title_'.$api_lang];
	// 	  $context = $v['context_'.$api_lang];
	// 	  $create_time = $v['create_time'];
  
	// 	  $notice_list[$game_id][] = [
	// 		"game_id" => $game_id,
	// 		"title" => $title,
	// 		"context" => $context,
	// 		"create_time" => $create_time,
	// 	  ];
	// 	}
		
	// 	$this->assign("notice_list",$notice_list);
  
	// }

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

	// // 新的menu_count
	// protected function menu_count($input , $is_sport = true) {
    //     $return = AntGameList::where("status",1)->get();
	// 	if ($return === false) {
	// 	  return false;
	// 	}
		
	// 	$game_list = array();
	// 	foreach ($return as $k => $v) {
	// 		$sport_id = $v['id'];

	// 		$today = time();
	// 		$after_tomorrow = $today + 2 * 24 * 60 * 60; 
	// 		$after_tomorrow = date('Y-m-d 00:00:00', $after_tomorrow); 

	// 		// 體育
	// 		$tmp = AntMatchList::join('ant_rate_list', 'ant_match_list.match_id', '=', 'ant_rate_list.match_id')
	// 			->join('ant_series_list', function ($join) {
	// 				$join->on('ant_match_list.game_id', '=', 'ant_series_list.game_id')->on('ant_match_list.series_id', '=', 'ant_series_list.series_id');
	// 			})
	// 		  ->select('ant_match_list.*', DB::raw('COUNT(ant_rate_list.id) as rate_count'))
	// 		  ->where('ant_rate_list.is_active', '=', 1)
	// 		  ->where('ant_series_list.status', 1)
    //   		  ->where('ant_match_list.start_time',"<=", $after_tomorrow)
	// 		  ->whereIn('ant_match_list.status', [1,2])
	// 		  ->where("ant_match_list.game_id",$sport_id)
	// 		  ->groupBy('ant_match_list.match_id')->having('rate_count', '>', 0)->get();
			
	// 		  $game_list[0][$sport_id] = count($tmp);

	// 		// 串關
	// 		$tmp = AntMatchList::join('ant_rate_list', 'ant_match_list.match_id', '=', 'ant_rate_list.match_id')
	// 			->join('ant_series_list', function ($join) {
	// 				$join->on('ant_match_list.game_id', '=', 'ant_series_list.game_id')->on('ant_match_list.series_id', '=', 'ant_series_list.series_id');
	// 			})
	// 		  ->select('ant_match_list.*', DB::raw('COUNT(ant_rate_list.id) as rate_count'))
	// 		  ->where('ant_rate_list.is_active', '=', 1)
	// 		  ->where('ant_series_list.status', 1)
	// 		  ->where('ant_match_list.start_time',"<=", $after_tomorrow)
	// 		  ->where('ant_match_list.status', 1)
	// 		  ->where("ant_match_list.game_id",$sport_id)
	// 		  ->groupBy('ant_match_list.match_id')->having('rate_count', '>', 0)->get();

	// 		$game_list[1][$sport_id] = count($tmp);
	// 	}

	// 	return $game_list;

	// }

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
