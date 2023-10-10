<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlayerOnline extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "player_online";


    public static function isPlayerOnline(
        array $data  // data=參數, 
    ) {

        // 緩存時間
        $cacheAliveTime = 1;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
            $player_id = $data['player_id'];
            $token = $data['token'];

            $checkToken = PlayerOnline::where("player_id", $player_id)
                ->where("token", $token)
                ->where("status", 1)
                ->count();

            return ($checkToken > 0);
        });
    }
}

