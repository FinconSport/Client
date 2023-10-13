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

    public static function getCache($data) {

        // 緩存時間
        $cacheAliveTime = 5;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
            $data = self::where($data)->first();
            return $data;
        });
    }
}

