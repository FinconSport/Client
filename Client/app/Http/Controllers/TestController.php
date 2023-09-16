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

class TestController extends PcController {
    
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


      /////////////////////////////

      $return = AntMatchList::whereIn("status",[1,2])->where("game_id",3)->get();

      $data = array();
      foreach ($return as $k => $v) {

        $stat = json_decode($v['stat'],true);
        $stat = $stat['teams'];
        $teams = json_decode($v['teams'],true);
        $series = json_decode($v['series'],true);

        // 賽事id
        $match_id = $v['match_id'];

        // 取得聯賽
        $series_name = $series['name_cn'];

        // 取得主客隊
        $home_team_name = "";
        $away_team_name = "";
        $home_team_stat_score = "";
        $away_team_stat_score = "";

        $home_team_score = "";
        $away_team_score = "";
        
        foreach ($stat as $kk => $vv) {
          if ($vv['index'] == 1) {
            $home_team_name = $vv['team']['name_cn'];
            $home_team_stat_score = $vv['total_score'];
          }
          if ($vv['index'] == 2) {
            $away_team_name = $vv['team']['name_cn'];
            $away_team_stat_score = $vv['total_score'];
          }
        }

        foreach ($teams as $kk => $vv) {
          if ($vv['index'] == 1) {
            $home_team_score = $vv['total_score'];
          }
          if ($vv['index'] == 2) {
            $away_team_score = $vv['total_score'];
          }
        }

        $tmp = array();
        $tmp['match_id'] = $match_id;
        $tmp['series_name'] = $series_name;
        $tmp['home_team_name'] = $home_team_name;
        $tmp['away_team_name'] = $away_team_name;
        $tmp['home_team_stat_score'] = $home_team_stat_score;
        $tmp['away_team_stat_score'] = $away_team_stat_score;
        $tmp['home_team_score'] = $home_team_score;
        $tmp['away_team_score'] = $away_team_score;
        $data[] = $tmp;
      }

      /////////////////////////

      foreach ($data as $k => $v) {

        echo $v['series_name'] . " (  match_id : " . $v['match_id'] . " )";
        echo "<br><br>";
        echo "<table style='border: 1px solid #786b6b;'>";
        echo "<tr><td colspan=3>" . $v['home_team_name'] . "[主] VS " . $v['away_team_name'] ."</td></tr>";
        echo "<tr><td>舊數據</td><td>".$v['home_team_score'] . "</td><td>" . $v['away_team_score']."</td></tr>";
        echo "<tr><td>新數據</td><td>".$v['home_team_stat_score'] . "</td><td>" . $v['away_team_stat_score']."</td></tr>";
        echo "</table>";
        echo "<hr>";

      }

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
    
            $rate = AntRateList::where("match_id",$match_id)->where("is_active",1)->get();
            if ($return === false) {
              $this->error(__CLASS__, __FUNCTION__, "06");
            }

            foreach ($rate as $kkkk => $vvvv) {
    
              // 處理rate , rate item 的value
              $type_id = $vvvv['type_id'];
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

    
      // 404
    public function error_404(Request $request) {
        return view('errors.404',$this->data); 
    }

    // 500
    public function error_500(Request $request) {
        return view('errors.500',$this->data); 
    }

    // ip not allow
    public function error_ip(Request $request) {
        return view('errors.ip',$this->data); 
    }

    // maintain page
    public function maintain(Request $request) {
        return view('errors.maintain',$this->data); 
    }

}