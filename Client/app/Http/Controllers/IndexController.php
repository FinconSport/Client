<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;

use App\Models\AntGameList;
use App\Models\AntMatchList;
use App\Models\AntRateList;
use App\Models\AntSeriesList;
use App\Models\AntTeamList;
use App\Models\AntTypeList;
use App\Models\Player;
use App\Models\Agent;


class IndexController extends PcController {

  protected $match_status = [
    -1 => "異常",
    1 => "等待開賽",
    2 => "進行中",
    3 => "已結束",
    4 => "延期",
    5 => "中斷",
    99 => "取消"
  ];
    
  // 首頁
  public function index(Request $request) {
    
    	$input = $this->getRequest($request);

		  $session = Session::all();

    	/////////////////////////

      // 判斷是否登入
      if (isset($session['is_login'])) {
        if ($session['is_login'] != 1) {
          $this->error(__CLASS__, __FUNCTION__, "01");
        }
      } else {
        $this->error(__CLASS__, __FUNCTION__, "01");
      }

      if ((!isset($input['sport'])) || ($input['sport'] == "")) {
        $input['sport'] = 1;  // 預設1 , 足球
      }

      if ((!isset($input['page'])) || ($input['page'] == "")) {
        $input['page'] = 1; // 預設1 
      }
      
      $this->assign("search",$input);
      $this->assign("match_status",$this->match_status);

      $this->getCurrentTime();

      // 輸出玩家資料
      $this->setPlayerInfo($session);

    	/////////////////////////
      // 取得語系
      $player_id = $session['player']['id'];
      $api_lang = $this->getAgentLang($player_id);
      if ($api_lang === false) {
        $this->error(__CLASS__, __FUNCTION__, "02");
      }
      $this->assign("lang",$api_lang);

    	/////////////////////////
      // Search 區用

      // 取得體育種類列表
      $this->getGameList($api_lang);

      // 取得公告資料
      $this->getNoticeList($api_lang);
      
      // 取得跑馬燈 列表
      $this->getMarqueeList($api_lang);

      // 取得熱門聯賽列表
      $this->getHotSeriesList($input['sport'],$api_lang);

    	/////////////////////////
      
      $page_limit = $this->page_limit;
      $page = $input['page'];
      $skip = ($page-1)*$page_limit;

      $sport_id = $input['sport'];

      // 依照搜尋條件搜尋
            
      $sport_id = $input['sport'];

      // 新的LIST
      $today = time();
      $after_tomorrow = $today + 2 * 24 * 60 * 60; 
      $after_tomorrow = date('Y-m-d 00:00:00', $after_tomorrow); 

      //////////////////////////////
      // 早盤
      $return = AntMatchList::join('ant_rate_list', 'ant_match_list.match_id', '=', 'ant_rate_list.match_id')
      ->join('ant_series_list', function ($join) {
              $join->on('ant_match_list.game_id', '=', 'ant_series_list.game_id')->on('ant_match_list.series_id', '=', 'ant_series_list.series_id');
      })
      ->select('ant_match_list.*', DB::raw('COUNT(ant_rate_list.id) as rate_count'))
      ->where('ant_rate_list.is_active', '=', 1)
      ->where('ant_series_list.status', 1)
      ->where('ant_match_list.status', 1)
      ->where('ant_match_list.start_time',"<=", $after_tomorrow)
      ->where("ant_match_list.game_id",$sport_id)
      ->groupBy('ant_match_list.match_id')
      ->having('rate_count', '>', 0)
      ->orderBy("ant_series_list.order_by")->get();

      $tmp = $this->rebuild($return, $api_lang,$sport_id);
      $data['early'] = $tmp;

      //////////////////////////////
      // 滾球

      if ($this->controller == "m_order") {
          // 串關不抓滾球賽事
          $data['living'] = array();
      } else {
        $return = AntMatchList::join('ant_rate_list', 'ant_match_list.match_id', '=', 'ant_rate_list.match_id')
        ->join('ant_series_list', function ($join) {
                $join->on('ant_match_list.game_id', '=', 'ant_series_list.game_id')->on('ant_match_list.series_id', '=', 'ant_series_list.series_id');
        })
        ->select('ant_match_list.*', DB::raw('COUNT(ant_rate_list.id) as rate_count'))
        ->where('ant_rate_list.is_active', '=', 1)
        ->where('ant_series_list.status', 1)
        ->where('ant_match_list.status', 2)
        ->where('ant_match_list.start_time',"<=", $after_tomorrow)
        ->where("ant_match_list.game_id",$sport_id)
        ->groupBy('ant_match_list.match_id')
        ->having('rate_count', '>', 0)
        ->orderBy("ant_series_list.order_by")->get();
  
        $tmp = $this->rebuild($return, $api_lang,$sport_id);
        $data['living'] = $tmp;
      }
      
      ///////////////////////////////

      $this->assign("match_list",$data);

      // 菜單統計
      $return = $this->menu_count($input);
      if ($return === false) {
        $this->error(__CLASS__, __FUNCTION__, "08");
      }
      $this->assign("menu_count",$return);

      ////////////////////////////////////

      if ($this->controller == "m_order") {
        return view('index.m_order',$this->data);
      }
      
      return view('index.index',$this->data);
      
  }

  // account
  public function account(Request $request) {

    	$input = $this->getRequest($request);

		  $session = Session::all();

    	/////////////////////////

      // 判斷是否登入
      if (isset($session['is_login'])) {
        if ($session['is_login'] != 1) {
          $this->ApiError("01");
        }
      } else {
        $this->ApiError("01");
      }
      
      // 獲取用戶資料
      $player_id = $session['player']['id'];
      
      $return = Player::where("id",$player_id)->first();
      
      if ($return === false) {
        $this->ApiError("02");
      }

      if ($return['status'] != 1) {
        $this->ApiError("03");
      }

      $data = array();
      $data['account'] = $return['account'];
      $data['balance'] = $return['balance'];
      
      $this->ApiSuccess($data,"04");
  }

    ///////////////////////////////////////////////
    ///////////////////////////////////////////////
    ///////////////////////////////////////////////


}
