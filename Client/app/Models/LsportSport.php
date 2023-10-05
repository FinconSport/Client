<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LsportSport extends Model
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "lsport_sport";

	// 取得Sport Name
    public static function getName($sport_id, $api_lang = 'en') {
        // 緩存時間
        $cacheAliveTime = 3600;
        // 緩存Key
		$tableName = (new static)->getTable();
		$cacheKey = $tableName . "_" . __FUNCTION__ . "_" . $sport_id;

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
