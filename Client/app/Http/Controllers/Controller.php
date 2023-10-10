<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\SystemConfig;
use App\Models\Agent;
use App\Models\Player;
use App\Models\GameRisk;

class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
    
    protected $data;
    
    protected $system_config; 
    protected $page_limit = 20;

	protected $host;
	protected $domain;

	// 生成錯誤碼用 , 無其他用途
	protected $controller;
	protected $function;
    
    public function __construct() {

    	$this->middleware(function ($request, $next) {

    		// system_config
    		$this->system_config();
    		
			$url = url()->current();
			$data = explode(".",$url);
			$this->domain = $data[1];

			$tmp = explode("/",$url);

			if (isset($tmp[3])) {
				$current_controller = $tmp[3];
			} else {
				$current_controller = "index";
			}
			$this->controller = $current_controller;
			
			
			if (isset($tmp[4])) {
				if ($tmp[3] == "api") {
					$current_function = $tmp[5];
				} else {
					$current_function = $tmp[4];
				}
			} else {
				$current_function = "index";
			}
			$this->function = $current_function;

    		$this->assign("domain", $this->domain);
    		$this->assign("controller", $current_controller);

    		return $next($request);
    	});
    }

    // 取得request
    protected function getRequest($request) {
    	
    	$input = $request->input();
    	
    	foreach ($input as $k => $v) {
    		$input[$k] = trim($v);
    	}
    	return $input;
    }
    
    // assign
    protected function assign($key, $value) {
    	$this->data[$key] = $value;
    }

    // system_config
    protected function system_config() {
    	$return = SystemConfig::where("status",1)->get();
    	$data = array();
    	foreach ($return as $k => $v) {

			if ($v['name'] == "default_agent_limit") {
				continue;
			}
			if ($v['name'] == "default_min_bet") {
				continue;
			}

    		$data[$v['name']] = $v['value'];
    	}
    	
    	$this->system_config = $data;
    	$this->assign("system_config",$data);
    }
	
	///////////////////////////////////////////

	// 取得 商戶語系
	protected function getAgentLang($player_id) {
		$return = Player::where("id",$player_id)->first();
		if ($return === false) {
			return false;
		}

		$agent_id = $return['agent_id'];

		$return = Agent::where("id",$agent_id)->first();
		if ($return === false) {
			return false;
		}

		$api_lang = $return['api_lang'];
		app()->setLocale($api_lang);

		return $api_lang;
	}

	///////////////////////////////////////////
    
    // success
    protected function success($class , $method , $num) {
    	
    	$class = str_replace("App\Http\Controllers\\", "", $class);
    	$class = str_replace("Controller", "", $class);
    	$class = strtolower($class);
    	
    	$msg = "SUCCESS_" . $class . "_" . $method . "_" . $num;
    	$msg = L($msg);
    	
    	session()->flash("success", $msg);
    }
    
    // error
    protected function error($class , $method , $num) {
    	
    	$class = str_replace("App\Http\Controllers\\", "", $class);
    	$class = str_replace("Controller", "", $class);
    	$class = strtolower($class);
    	
    	$msg = "ERROR_" . $class . "_" . $method . "_" . $num;
    	$msg = L($msg);
    	
    	session()->flash("error", $msg);
    }

    private static function compareRateValue($a, $b) {
		return strcmp($a["rate_value"], $b["rate_value"]);
	}
	

	private static function compareRateValueB($a, $b) {
		return strcmp($a["order_by"], $b["order_by"]);
	}
	
	protected function customExplode($str) {
		$data = array();
		if (strpos($str, ' ') !== false) {
		  // 字串包含空白，以空白符分割
		  $data['filter'] = 0;
		  $tmp = explode(' ', $str);
		  $data['value'] = $tmp;
		} elseif (strpos($str, '+') !== false) {
		$data['filter'] = 1;
		$tmp = explode('+', $str);
		$data['value'] = $tmp;
		} elseif (strpos($str, '-') !== false) {
		$data['filter'] = 2;
		$tmp = explode('-', $str);
		$data['value'] = $tmp;
		}
	
		return $data;
	}

    // 分頁類
    protected function pagation($data) {
    	
    	$result = array();
    	
    	$result['currentPage'] = $data->currentPage();
    	$result['firstPage'] = "1";
    	$result['lastPage'] = $data->lastPage();
    	$result['perPage'] = $data->perPage();
    	$result['total'] = $data->total();
    	
    	if ($result['currentPage'] == 1) {
    		$result['privPage'] = $result['currentPage'];
    	} else {
    		$result['privPage'] = $result['currentPage']-1;
    	}
    	
    	if ($result['currentPage'] == $result['lastPage']) {
    		$result['nextPage'] = $result['currentPage'];
    	} else {
    		$result['nextPage'] = $result['currentPage']+1;
    	}
    	
    	return $result;
    }
    
    protected function ajaxSuccess($message = "",$data = array()) {
    	
    	$return = array();
    	$return['status'] = 1;
    	$return['data'] = $data;
    	$return['message'] = L($message,"ajax");
    	
    	echo json_encode($return);
    	exit();
    }
    
    // AJAX Error reponse
    protected function ajaxError($message = "", $data = array()) {
    	
    	$return = array();
    	$return['status'] = 0;
    	$return['data'] = $data;
    	$return['message'] = L($message,"ajax");
    	
    	echo json_encode($return);
    	exit();
    }

	// 判斷用戶是否為移動端
	protected function is_mobile() {
		if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
			$is_mobile = false;
		} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {

			if (isset($_SERVER['HTTP_SEC_CH_UA_PLATFORM'])) {
				// 這個是for chrome debug工具 切換用
				if (strpos($_SERVER['HTTP_SEC_CH_UA_PLATFORM'], 'Android') !== false) {
					$is_mobile = true;	
				} else {
					$is_mobile = false;
				}
			} else {
				$is_mobile = true;
			}
		} else {
			$is_mobile = false;
		}

		return $is_mobile;
	}

	// 取得子域名, 判斷是那位用戶
	protected function getSubHost() {
		
		$host = $_SERVER['HTTP_HOST'];
		$data = explode(".",$host);

		// system_config
		$host = $this->system_config['url'];
		$sc_data = explode(".",$host);

		if (($data[0] != $sc_data[0]) && ($data[0] != "www")) {
			return $data[0];
		}

		// 非子域名
		return false;
	}

	// 取得用戶資料並輸出至頁面
	protected function setPlayerInfo($session) {
		$player_id = $session['player']['id'];
		$return = Player::where("id",$player_id)->first();
		$player = array();
		$player['account'] = $return['account'];
		$player['balance'] = $return['balance'];
		$player['status'] = $return['status'];
		$this->assign("player",$return);
	}

	// 檢查是否登入
	protected function isLogin() {

		$is_login = Session::get('is_login', false);

		if ($is_login === false) {
			Session::flush();
			header("Location: /login");
			exit();
		}
	
	}
}
