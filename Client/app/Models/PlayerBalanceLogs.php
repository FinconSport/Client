<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerBalanceLogs extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "player_balance_logs";

    public static function getBalanceLogsList($data) {

        // 緩存時間
        $cacheAliveTime = 60;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
			
			if ($data['type'] !== false) {
				$return = PlayerBalanceLogs::where("player_id", $data['player'])
				->where("start_time","<=",$data['start_time'])
				->where("start_time",">",$data['end_time'])
				->where("type",$data['type'])
				->skip($data['skip'])
				->take($data['page_limit'])
				->orderBy('id', 'DESC')
				->get();  
			} else {
				$return = PlayerBalanceLogs::where("player_id", $player_id)
				->where("start_time","<=",$input['start_time'])
				->where("start_time","<=",$input['start_time'])
				->skip($data['skip'])
				->take($data['page_limit'])
				->orderBy('id', 'DESC')
				->get();  
			}
			
            return $return;
        });
    }
}

