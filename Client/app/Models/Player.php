<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Player extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "player";
    
    public static function getData($data) {

        // 緩存時間
        $cacheAliveTime = 1;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
			$player_id = $data['player_id'];

            $data = self::where('id', $player_id)->first();
            $return = $data;

            return $return;
        });
    }

}

