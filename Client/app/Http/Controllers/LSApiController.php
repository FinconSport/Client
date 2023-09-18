<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;

use App\Models\GameMatch;
use App\Models\GameResult;
use App\Models\GameOrder;

use App\Models\AntGameList;
use App\Models\AntMatchList;
use App\Models\AntRateList;
use App\Models\AntSeriesList;
use App\Models\AntTeamList;
use App\Models\AntTypeList;
use App\Models\AntNoticeList;

use App\Models\PlayerOnline;
use App\Models\Player;
use App\Models\Agent;
use App\Models\PlayerBalanceLogs;
use App\Models\ClientMarquee;
use App\Models\SystemConfig;


class LSApiController extends Controller {
    
  protected $page_limit = 20;
  
  public function index(Request $request) {

    $input = $this->getRequest($request);

    $return = $this->checkToken($input);
    if ($return === false) {
      $this->ApiError("PLAYER_RELOGIN",true);
    }

    $this->ApiError("01");
    //////////////////////////////////////////

    // .....

    $this->ApiError("01");
    
    $this->ApiSuccess($data,"01");

  }


  ////////////// ////////////// ////////////// ////////////// //////////////
  


  ////////////// ////////////// ////////////// ////////////// //////////////
  protected function gzip($data) {

    $data = json_encode($data,true);
    // 使用 gzcompress() 函數進行壓縮
    $compressedData = gzcompress($data);

    // 使用 base64_encode() 函數進行 base64 編碼
    $base64Data = base64_encode($compressedData);

    return $base64Data;
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
    
  // check token
  protected function checkToken($input) {
    
    $player_id = $input['player'];
    $token = $input['token'];
    
    $return = PlayerOnline::where("player_id",$player_id)->where("token",$token)->where("status",1)->count();
    if ($return == 0) {
      return false;
    }

    return true;

  }

}

