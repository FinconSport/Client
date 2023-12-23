<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use DB;

class LsportMarketBet extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "lsport_market_bet";

    public static function getName($data) {

        // 緩存時間
        $cacheAliveTime = 3600;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
			$bet_id = $data['bet_id'];
			$api_lang = $data['api_lang'];
			
            $data = self::where('bet_id', $bet_id)->first();
            
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
			$bet_id = $data['bet_id'];

            $data = self::where('bet_id', $bet_id)->select('status')->first();
            $return = $data;

            return $return;
        });
    }


    // 取得相差最小的資料做為main-line
    public static function getMainLine($data) {

        // 緩存時間
        $cacheAliveTime = 1;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);
        
        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {

            $return = self::select('fixture_id', 'market_id', 'base_line', DB::raw('MIN(price) AS min_price'), DB::raw('MAX(price) AS max_price'), DB::raw('ABS(MIN(price) - MAX(price)) as different_price'))
            ->where('fixture_id', '=', $data['fixture_id'])
            ->where('market_id', '=', $data['market_id'])
            ->where('status',1)
            ->groupBy('market_id', 'base_line')
            ->orderBy('different_price', 'asc')
            ->first();

            return $return;
        });

    }

}
