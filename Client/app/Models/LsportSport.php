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
        $lsportSport = new static;
        $cacheKey = $lsportSport->getCacheKey($data);
		
		$sport_id = $data['sport_id'];
		$api_lang = 'tw';

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($sport_id, $api_lang, $lsportSport) {
            $data = $lsportSport->where('sport_id', $sport_id)->first();
            // 默认名称
            $name = $data['name_en'];
            if (($data['name_' . $api_lang] != "") && ($data['name_' . $api_lang] != null)) {
                $name = $data['name_' . $api_lang];
            }
            return $name;
        });
    }

	// 取得Sport Name
    public static function getName($data) {

        // 緩存時間
        $cacheAliveTime = 3600;

        // 緩存Key
		$cacheKey = static::getCacheKey($data);

		dd($cacheKey);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($sport_id, $api_lang) {
            $data = self::where('sport_id', $sport_id)->first();
            // default name
            $name = $data['name_en'];
            if (($data['name_'.$api_lang] != "") && ($data['name_'.$api_lang] != null)) {
                $name = $data['name_'.$api_lang];
            }
            return $name; 
        });
    }
}
