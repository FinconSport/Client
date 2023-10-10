<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LsportLeague extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "lsport_league";

    public static function getName($data) {

        // 緩存時間
        $cacheAliveTime = 3600;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
			$sport_id = $data['sport_id'];
			$api_lang = $data['api_lang'];
			
            $data = self::where('sport_id', $sport_id)->first();
            
			// 預設值
            $name = $data['name_en'];
            if (isset($data['name_' . $api_lang]) && !empty($data['name_' . $api_lang])) {
                $name = $data['name_' . $api_lang];
            }
            return $name;
        });
    }
    
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
