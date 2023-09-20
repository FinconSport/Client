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
use App\Models\AntGameList;
use App\Models\AntSeriesList;
use App\Models\AntTeamList;
use App\Models\AntRateList;
use App\Models\AntTypeList;
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

	
    // 取得體育種類列表
    protected function getGameList($lang = 'cn') {
      
		$lang_key = "name_" . $lang;
  
		$list = array();
		$return = AntGameList::where("status",1)->get();
		if ($return === false) {
		  return false;
		}
  
		foreach ($return as $k => $v) {
		  $list[$v['id']] = $v[$lang_key];
		}
  
		$this->assign("sport_list",$list);
  
	  }
  
		// 取得熱門聯賽列表
	  protected function getHotSeriesList($sport_type, $lang = 'cn') {
  
		$lang_key = "name_" . $lang;
  
		$list = array();
		//  $return = AntSeriesList::where("game_id",$sport_type)->where("is_hot",1)->get();
		$return = AntSeriesList::join('ant_game_list', 'ant_series_list.game_id', '=', 'ant_game_list.id')
		->where('ant_series_list.is_hot', 1)
		->where('ant_game_list.status', 1)
		->select('ant_series_list.*')
		->get();
		if ($return === false) {
		  return false;
		}
  
		foreach ($return as $k => $v) {
		  $list[$v['game_id']][$v['series_id']] = $v[$lang_key];
		}
  
		$this->assign("series_list",$list);
  
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
    

  	// 資料重整
  	protected function rebuild($return, $api_lang, $sport_id) {

		$tmp = array();
		$tmp_lang = array();
		foreach ($return as $kk => $vv) {

			$columns = array("id","match_id","game_id","start_time","end_time","status","bo","win_team","live_status","has_live","has_animation","series","teams"); 
			$ttmp = array();
			foreach ($columns as $kkk => $vvv) {
			$ttmp[$vvv] = $vv[$vvv];
			}

			// series 處理logo
			$ttmp['series'] = json_decode($ttmp['series'],true);
			$series_id = $ttmp['series']['id'];
			$game_id = $ttmp['series']['game_id'];
			$tmp_logo = AntSeriesList::where("series_id",$series_id)->where("game_id",$game_id)->first();
			if ($tmp_logo === false) {
			$this->error(__CLASS__, __FUNCTION__, "04");
			}
			if ($tmp_logo != null) {
			$ttmp['series']['logo'] = $this->system_config['image_url'] . $tmp_logo['local_logo'] . "?v=" . $this->system_config['version'];
			}

			// series 處理語系
			$name_columns = "name_".$api_lang;
			$ttmp['series']['name'] = $tmp_logo[$name_columns];
			unset($ttmp['series']['name_cn']);
			unset($ttmp['series']['name_en']);
			unset($ttmp['series']['name_hi']);

			$tmp[$series_id]['series'] =  $ttmp['series'];

			// team 處理logo
			$ttmp['teams'] = json_decode($ttmp['teams'],true);

			foreach ($ttmp['teams'] as $key => $value) {
			$team_id = $value['team']['id'];
			$tmp_logo = AntTeamList::where("team_id",$team_id)->where("game_id",$sport_id)->first();
			if ($tmp_logo === false) {
				$this->error(__CLASS__, __FUNCTION__, "05");
			}
			if ($tmp_logo != null) {
				$ttmp['teams'][$key]['team']['logo'] =  $this->system_config['image_url'] . $tmp_logo['local_logo'] . "?v=" . $this->system_config['version'];
			}

			$name_columns = "name_".$api_lang;
			$ttmp['teams'][$key]['team']['name'] = $tmp_logo[$name_columns];
			unset($ttmp['teams'][$key]['team']['name_cn']);
			unset($ttmp['teams'][$key]['team']['name_en']);
			unset($ttmp['teams'][$key]['team']['name_hi']);

			// 語系用
			$tmp_lang[$key]['cn'] = trim($tmp_logo['name_cn']);
			$tmp_lang[$key]['tw'] = trim($tmp_logo['name_tw']);

			}
			$ttmp['rate'] = array();

			// 取得賠率資料
			$match_id = $vv['match_id'];

			$rate = AntRateList::where("match_id",$match_id)->where("is_active",1)->whereIn("status",[1,2])->get();
			if ($return === false) {
			$this->error(__CLASS__, __FUNCTION__, "06");
			}

			foreach ($rate as $kkkk => $vvvv) {

			// 處理rate , rate item 的value
			$type_id = $vvvv['type_id'];
			$rate_status = $vvvv['status'];
			$type_data = AntTypeList::where("type_id",$type_id)->where("game_id",$sport_id)->first();
			if ($type_data === false) {
				$this->error(__CLASS__, __FUNCTION__, "07");
			}

			if ($type_data == null) {
				$rate_value = "";
			} else {
				$rate_item_value = $type_data['value'];
				$g = str_replace("+","",$rate_item_value);
				$g = str_replace("-","",$g);
				$rate_value = $g;
			}

			///////////////

			// 取得GameRisk 

			$risk = GameRisk::where("game_id",$sport_id)->where("match_id",$match_id)->where("game_priority",$vvvv['game_priority'])->first();
			if ($risk === false) {
				$this->error(__CLASS__, __FUNCTION__, "07");
			}

			$is_risk = false;
			if ($risk != null) {
				$is_risk = true;
				$risk_data = json_decode($risk['data'],true);
			}

			$name_columns = "name_".$api_lang;
			$d = array();
			$d['id'] = $vvvv['id'];
			$d['rate_id'] = $vvvv['rate_id'];
			$d['name'] = $vvvv[$name_columns];
			$d['game_priority'] = $vvvv['game_priority'];
			$d['status'] = $vvvv['status'];
			$d['rate_value'] = $rate_value;

			$d['rate'] = array();
			$items = json_decode($vvvv['items'],true);

			// 針對讓球, 有平局的做排除
			if (($vvvv['game_priority'] == 2) || ($vvvv['game_priority'] == 4)) {
				if (count($items) > 2) {
				continue;
				}
			}

			foreach ($items as $kkkkk => $vvvvv) {

				// rate item 顯示轉化
				$item_name = $vvvvv["name_cn"]; // 預設
				$tmp_lang[] = array("cn" => "单","tw" => "單");
				$tmp_lang[] = array("cn" => "双","tw" => "雙");
				foreach ($tmp_lang as $lang_k => $lang_v) {
				$item_name = str_replace($lang_v['cn'],$lang_v['tw'],$item_name);
				}

				$e = array();
				$e['id'] = $vvvvv['id'];
				$e['limit'] = $vvvvv['limit'];
				$e['name'] =  $item_name;
				$e['rate'] = $vvvvv['rate'];
				$e['status'] = $vvvvv['status'];
				if ($is_risk) {
					
					if (isset($risk_data[$kkkkk])) {
						$e['risk'] = $risk_data[$kkkkk];
					  } else {
						$e['risk'] = 0;
					  }
				} else {
				$e['risk'] = 0;
				}
				$e['updated_at'] = strtotime($vvvv['updated_at']);

				// 這邊不要轉語系, 這不是顯示用
				$rate_value = $this->customExplode($vvvvv['name_cn']);
				$e['value'] = $rate_value['value'][count($rate_value['value'])-1];
				
				if (($vvvv['game_priority'] == 7) ||($vvvv['game_priority'] == 8)) {
				$d['rate'][$kkkkk] = $e;
				} else {
				$d['rate'][$vvvvv['id']] = $e;
				}
				
			}
			
			$ttmp['rate'][$vvvv['game_priority']][] = $d;
			// 針對波膽 7,8 做排序
			if (($vvvv['game_priority'] == 7) || ($vvvv['game_priority'] == 8)) {

				$tmp_rate_for_order = array();
				foreach ($ttmp['rate'][$vvvv['game_priority']] as $rate_k => $rate_v) {
				foreach ($rate_v['rate'] as $rate_kk => $rate_vv) {

					$tmp_rate_name = $rate_vv['value'];

					if ($tmp_rate_name == "其他") {
					$rate_vv['order_by'] = 999;
					$tmp_rate_for_order[1][] = $rate_vv;
					} else {
					$tmp_rate_name = explode("-",$tmp_rate_name);
					$tmp_rate_name[0] = $tmp_rate_name[0]+0;
					$tmp_rate_name[1] = $tmp_rate_name[1]+0;
					
					if ($tmp_rate_name[0] > $tmp_rate_name[1]) {
						// 主贏
						$rate_vv['order_by'] = floatval($tmp_rate_name[0] . "." . $tmp_rate_name[1]);
						$tmp_rate_for_order[0][] = $rate_vv;

					} elseif ($tmp_rate_name[0] == $tmp_rate_name[1]) {
						// 平局
						$rate_vv['order_by'] = floatval($tmp_rate_name[0] . "." . $tmp_rate_name[1]);
						$tmp_rate_for_order[1][] = $rate_vv;

					} elseif ($tmp_rate_name[0] < $tmp_rate_name[1]) {
						// 客贏
						$rate_vv['order_by'] = floatval($tmp_rate_name[1] . "." . $tmp_rate_name[0]);
						$tmp_rate_for_order[2][] = $rate_vv;

					}
					
					}
				}
				}
				
				foreach ($tmp_rate_for_order as $rate_k => $rate_v) {
				for ($i=0;$i<3;$i++) {
					usort($tmp_rate_for_order[$i], [self::class, 'compareRateValueB']);
				}
				}

				$tmp_rate_count = count($ttmp['rate'][$vvvv['game_priority']])-1;
				$ttmp['rate'][$vvvv['game_priority']][$tmp_rate_count]['rate'] = $tmp_rate_for_order;
			}
			
			}

			///////////////////////
			
			// 先對priority 做一次排序
			ksort($ttmp['rate']);
			// 再使用 usort 对rate 資料依rate value 排序
			foreach ($ttmp['rate'] as $rate_k => $rate_v) {
			usort($ttmp['rate'][$rate_k], [self::class, 'compareRateValue']);
			}
			
			$tmp[$series_id]['list'][] = $ttmp;

		}

		// 最後將聯賽id 換成順序
		$i = 0;
		foreach ($tmp as $k => $v) {
			$tmp[$i] = $v;
			unset($tmp[$k]);
			$i++;
		}

		return $tmp;
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
