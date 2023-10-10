<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ClientMarquee extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "client_marquee";

    public static function getData(
        array $data = null  // data=參數
    ) {

        // 緩存時間
        $cacheAliveTime = 60;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {

            $data = self::where(
                "status", 1
            )->orderBy(
                'create_time', 'DESC'
            )->get();
            $return = $data;

            return $return;
        });
    }

}
