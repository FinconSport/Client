<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LsportSport extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "lsport_sport";

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
            //if (($data['name_' . $api_lang] != "") && ($data['name_' . $api_lang] != null)) {
            if (isset($data['name_' . $api_lang]) && !empty($data['name_' . $api_lang])) {
                $name = $data['name_' . $api_lang];
            }
            return $name;
        });
    }
}
