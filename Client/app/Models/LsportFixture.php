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

            $data = self::where($data)->select('status')->first();
            $return = $data;

            return $return;

        });
    }

    // result_index , 取得列表
    public static function getResultList($data) {

        // 緩存時間
        $cacheAliveTime = 60;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
			
            $sport_id = $data['sport'];
            $page = $data['page'];
            $start_time = $data['start_time'];
            $end_time = $data['end_time'];
            $league_id = $data['league_id'];
            dd($league_id);
        
            if ($league_id !== false) {
                $return = LsportFixture::where("sport_id", $sport_id)
                ->where("league_id",$league_id)
                ->where("start_time", ">=", $start_time)
                ->where("start_time", "<", $end_time)
                ->whereIn("status", [3,4,5,6,7])
                ->orderBy("start_time","DESC")
                ->get();
            } else {
                $return = LsportFixture::where("sport_id", $sport_id)
                ->where("start_time", ">=", $start_time)
                ->where("start_time", "<", $end_time)
                ->whereIn("status", [3,4,5,6,7])
                ->orderBy("start_time","DESC")
                ->get();
            }
    
            $data = self::where($data)->select('status')->first();
            $return = $data;

            return $return;

        });

    }

}
