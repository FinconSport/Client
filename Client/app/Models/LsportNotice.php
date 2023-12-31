<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LsportNotice extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "lsport_notice";

    public static function getList($data) {

        // 緩存時間
        $cacheAliveTime = 60;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
            $time = strtotime($data['create_time']);
            $data = self::where("create_time" , ">=" , $time)->orderBy('sport_id', 'ASC')->orderBy('create_time', 'DESC')->get();
            return $data;
        });
    }
	
	
}
