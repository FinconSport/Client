<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemConfig extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "system_config";

    public static function getActiveSystemConfig(
        array $data = null  // data=參數
    ) {

        // 緩存時間
        $cacheAliveTime = 1;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {

            $data = self::where(
                "status", 1
            )->get();
            $return = $data;

            return $return;
        });
    }
}
