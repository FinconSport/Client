<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GameOrder extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "game_order";

	protected static function getOrderList($data) {

        // 緩存時間
        $cacheAliveTime = 10;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
			
			$player_id = $data['player_id'];
			$result = $data['result'];
			$start_time = strtotime($data['start_time']);
			$end_time = strtotime($data['end_time']);
			$skip = $data['skip'];
			$page_limit = $data['page_limit'];
			
            $model = self::where('player_id', $player_id)->whereColumn('m_id', '=', 'id');

			//////////////////////////////
			// Search 

			// start time 
			if ($start_time != "") {
				$model = $model->where('create_time', ">=", $start_time);
			}
			// end time 
			if ($end_time != "") {
				// 如果輸入是2020-10-10, 應當包含2020-10-10的資料, 所以條件為 < 2020-10-11
				$model = $model->where('create_time', "<", $end_time);
			}


			dd($start_time , $end_time);
			// result
			if ($result == -1) {		// 全部
				// do nothing
			} elseif ($result == 0) {	// 未結
				$model = $model->whereIn('status', [0,1,2,3]);
			} else {					// 已結
				$model = $model->where('status', 4);
			}

			//////////////////////////////

			$return = $model->skip($skip)->take($page_limit)->orderBy("id","DESC")->get();
            
            return $return;
        });
	}

}
