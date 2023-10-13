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

	// 此處寫死orderby 條件 , 這表應用場景少
    public static function getList($data) {

        // 緩存時間
        $cacheAliveTime = 60;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
            $data = self::where($data)->orderBy("create_time","DESC")->get();
            return $data;
        });
    }
}
