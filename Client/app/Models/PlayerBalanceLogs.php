<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlayerBalanceLogs extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "player_balance_logs";

    public static function getBalanceLogsList($data) {

        // 緩存時間
        $cacheAliveTime = 1;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
			
			if ($data['balance_type'] === false) {
				$return = self::where("player_id", $data['player'])
					->where("create_time", ">=", strtotime($data['start_time']))
					->where("create_time", "<", strtotime($data['end_time']))
					->skip($data['skip'])
					->take($data['page_limit'])
					->orderBy('id', 'DESC')
					->get();  
			} else {
				$return = self::where("player_id", $data['player'])
					->where("create_time", ">=", strtotime($data['start_time']))
					->where("create_time", "<", strtotime($data['end_time']))
					->where("balance_type", "=", $data['balance_type'])
					->skip($data['skip'])
					->take($data['page_limit'])
					->orderBy('id', 'DESC')
					->get();
			}
			
            return $return;
        });
    }

    
}

