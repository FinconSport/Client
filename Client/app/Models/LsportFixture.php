<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LsportFixture extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "lsport_fixture";
    
    public static function getStatus($data) {

        // 緩存時間
        $cacheAliveTime = 3600;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
			$sport_id = $data['sport_id'];

            $data = self::where('sport_id', $sport_id)->select('status')->first();
            $return = $data;

            return $return;
        });
    }

}
