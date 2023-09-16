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
use App\Models\GameOrder;

class MatchController extends PcController {
    
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

      // 取得熱門聯賽列表
      $this->getHotSeriesList($input['sport'],$api_lang);

      // 狀態
      $status = [
        -1 => "異常",
        1 => "等待開賽",
        2 => "進行中",
        3 => "已結束",
        4 => "延期",
        5 => "中斷",
        99 => "取消"
      ];

      $this->assign("status_list",$status);
    	/////////////////////////

      // 取得比賽資料

      $page_limit = $this->page_limit;
      $page = $input['page'];
      $skip = ($page-1)*$page_limit;

      $sport_id = $input['sport'];

      // form search
      
      $AntMatchList = AntMatchList::where("game_id",$sport_id)->where("status",">=",2);


      if (isset($input['series_id']) && ($input['series_id'] != "")) {
        $AntMatchList = $AntMatchList->where("series_id",$input['series_id']);
      }
  
      if (isset($input['start_time']) && ($input['start_time'] != "")) {
        $AntMatchList = $AntMatchList->where("start_time",">=",$input['start_time']." 00:00:00");
      }
  
      if (isset($input['end_time']) && ($input['end_time'] != "")) {
        $AntMatchList = $AntMatchList->where("start_time","<=",$input['end_time']." 23:59:59");
      }

      $total = $AntMatchList->count();
      $return = $AntMatchList->skip($skip)->take($page_limit)->orderBy('start_time', 'DESC')->get();
      if ($return === false) {
        $this->error(__CLASS__, __FUNCTION__, "03");
      }
        
      ////////////////////////////////////
      
      $columns = array(
        "id",
        "match_id",
        "game_id",
        "series_id",
        "start_time",
        "end_time",
        "status"
      );

      $data = array();
      foreach ($return as $k => $v) {

        $tmp = array();

        $series = json_decode($v['series'],true);
        $series_id = $series['id'];
        $tmp_logo = AntSeriesList::where("series_id",$series_id)->where("game_id",$sport_id)->first();
        if ($tmp_logo === false) {
          $this->error(__CLASS__, __FUNCTION__, "04");
        }
        if ($tmp_logo == null) {
          continue;
        }

        if (!isset($tmp_logo['local_logo'])) {
          $tmp_logo['local_logo'] = "series_default.png";
        }

        
        $name_columns = "name_".$api_lang;

        $tmp['series_name'] = $tmp_logo[$name_columns];
        $tmp['series_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'] . "?v=" . $this->system_config['version'];

        // stat
        $stat = json_decode($v['stat'],true);
        unset($stat['stat']['match_id']);
        unset($stat['stat']['time']);
        if ($v['stat'] == "") {
          $tmp['stat'] = [];
        } else {
          $tmp['stat'] = $stat['stat'];
        }

        $teams = json_decode($v['teams'],true);

        foreach ($teams as $key => $value) {
          $team_id = $value['team']['id'];
          $tmp_logo = AntTeamList::where("team_id",$team_id)->where("game_id",$sport_id)->first();
          if ($tmp_logo === false) {
            $this->error(__CLASS__, __FUNCTION__, "05");
          }
          
          if ($tmp_logo == null) {
            continue;
          }

          /////////////////////////////////

          $teams[$key]['team']['name'] =  $tmp_logo[$name_columns];
          $teams[$key]['team']['logo'] =  $this->system_config['image_url'] . $tmp_logo['local_logo'] . "?v=" . $this->system_config['version'];
       
        }

        foreach ($columns as $kk => $vv) {
          $tmp[$vv] = $v[$vv]; 
        }
        
        $tmp['status'] = $status[$v['status']];
       

        foreach ($teams as $kk => $vv) {

          if (!isset($vv['team']['name'])) {
            $vv['team']['name'] = "--";
          }

          if ($vv['index'] == 1) {
            $tmp['home_team_name'] = $vv['team']['name'];
            $tmp['home_team_logo'] = $vv['team']['logo'];
            $tmp['home_team_score'] = $vv['total_score'];
          } else {
            $tmp['away_team_name'] = $vv['team']['name'];
            $tmp['away_team_logo'] = $vv['team']['logo'];
            $tmp['away_team_score'] = $vv['total_score'];
          }
        }

        $data['list'][] = $tmp;
      }

      $data['total'] = $total;

      $this->assign("data",$data);

      ////////////////////////////////////

      // 菜單統計
      $return = $this->menu_count($session['player']['id']);
      if ($return === false) {
        $this->error(__CLASS__, __FUNCTION__, "06");
      }

      $this->assign("menu_count",$return);

      ////////////////////////////////////

      return view('match.index',$this->data);
    }

    // post
    public function post(Request $request) {
    	
    	$input = $this->getRequest($request);

		  $session = Session::all();

    	/////////////////////////

      // 判斷是否登入
      if (isset($session['is_login'])) {
        if ($session['is_login'] != 1) {
          $this->ajaxError("error_match_post_01");
        }
      } else {
        $this->ajaxError("error_match_post_01");
      }

      // 輸入判定
      if (!isset($input['menu']) || ($input['menu'] == "")) {
        $input['menu'] = "";  // 預設未結算
      }

      if (!isset($input['sport']) || ($input['sport'] == "")) {
        $input['sport'] = 1;  // 預設1 , 足球
      }

      if (!isset($input['page']) || ($input['page'] == "")) {
        $input['page'] = 1; // 預設1 
      }

      
      if (!isset($input['series_id']) || ($input['series_id'] == "")) {
        $input['series_id'] = "";  // 預設未結算
      }

      if (!isset($input['status']) || ($input['status'] == "")) {
        $input['status'] = "";  // 預設未結算
      }

      if (!isset($input['start_time']) || ($input['start_time'] == "")) {
        $input['start_time'] = "";  // 預設未結算
      }

      if (!isset($input['end_time']) || ($input['end_time'] == "")) {
        $input['end_time'] = "";  // 預設未結算
      }

    	/////////////////////////
      // 取得語系
      $player_id = $session['player']['id'];
      $api_lang = $this->getAgentLang($player_id);
      if ($api_lang === false) {
        $this->error(__CLASS__, __FUNCTION__, "02");
      }

    	/////////////////////////
      // Search 區用

      // 狀態
      $status = [
        -1 => "異常",
        1 => "等待開賽",
        2 => "進行中",
        3 => "已結束",
        4 => "延期",
        5 => "中斷",
        99 => "取消"
      ];

    	/////////////////////////

      // 取得比賽資料

      $page_limit = $this->page_limit;
      $page = $input['page'];
      $skip = ($page-1)*$page_limit;

      $sport_id = $input['sport'];


        // form search
        $AntMatchList = AntMatchList::where("game_id",$sport_id)->where("status",">=",2);

        if (($input['series_id'] != "")) {
          $AntMatchList = $AntMatchList->where("series_id",$input['series_id']);
        }
  
        if (($input['start_time'] != "")) {
          $AntMatchList = $AntMatchList->where("start_time",">=",$input['start_time']." 00:00:00");
        }
  
        if (($input['end_time'] != "")) {
          $AntMatchList = $AntMatchList->where("start_time","<=",$input['end_time']." 23:59:59");
        }

        $return = $AntMatchList->skip($skip)->take($page_limit)->orderBy('start_time', 'DESC')->get();

        
        $pagination = $AntMatchList->count();

      

      ////////////////////
      $columns = array(
        "id",
        "match_id",
        "game_id",
        "series_id",
        "start_time",
        "end_time",
        "status"
      );

      $data = array();
      foreach ($return as $k => $v) {

        $tmp = array();
        
        $series = json_decode($v['series'],true);
        $series_id = $series['id'];
        $game_id = $series['game_id'];
        $tmp_logo = AntSeriesList::where("series_id",$series_id)->where("game_id",$sport_id)->where("status",1)->first();
        if ($tmp_logo === false) {
          $this->ApiError("01");
        }
        if ($tmp_logo == null) {
          continue;
        }

        $name_columns = "name_".$api_lang;

        $tmp['series_name'] = $tmp_logo[$name_columns];
        $tmp['series_logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'] . "?v=" . $this->system_config['version'];

        foreach ($columns as $kk => $vv) {
          $tmp[$vv] = $v[$vv]; 
        }

        // stat
        $stat = json_decode($v['stat'],true);
        unset($stat['stat']['match_id']);
        unset($stat['stat']['time']);
        if ($v['stat'] == "") {
          $tmp['stat'] = [];
        } else {
          $tmp['stat'] = $stat['stat'];
        }

        $tmp['status'] = $status[$v['status']];
       
        $teams = json_decode($v['teams'],true);

        $teams = json_decode($v['teams'],true);

        foreach ($teams as $key => $value) {
          $team_id = $value['team']['id'];
          $tmp_logo = AntTeamList::where("team_id",$team_id)->where("game_id",$sport_id)->first();
          if ($tmp_logo === false) {
            $this->error(__CLASS__, __FUNCTION__, "05");
          }
          
          if ($tmp_logo == null) {
            continue;
          }

          /////////////////////////////////

          $teams[$key]['team']['name'] =  $tmp_logo[$name_columns];
          $teams[$key]['team']['logo'] =  $this->system_config['image_url'] . $tmp_logo['local_logo'] . "?v=" . $this->system_config['version'];
       
        }

        foreach ($columns as $kk => $vv) {
          $tmp[$vv] = $v[$vv]; 
        }
        
        $tmp['status'] = $status[$v['status']];
       

        foreach ($teams as $kk => $vv) {

          if ($vv['index'] == 1) {
            $tmp['home_team_name'] = $vv['team']['name'];
            $tmp['home_team_logo'] = $vv['team']['logo'];
            $tmp['home_team_score'] = $vv['total_score'];
          } else {
            $tmp['away_team_name'] = $vv['team']['name'];
            $tmp['away_team_logo'] = $vv['team']['logo'];
            $tmp['away_team_score'] = $vv['total_score'];
          }
        }

        $data[] = $tmp;
      }

      
      $this->ajaxSuccess("success_match_post_01",$data);

    }


    ///////////////////////////////////////////////
    ///////////////////////////////////////////////
    ///////////////////////////////////////////////

}

