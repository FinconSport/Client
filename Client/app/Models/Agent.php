<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Agent extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "agent";

    public static function getApiLang(
        array $data  // data=參數
    ) {

        // 緩存時間
        $cacheAliveTime = 3600;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data) {
            $id = $data['id'];

            $data = self::where('id', $id)->select('api_lang')->first();
            $return = $data;

            return $return;
        });
    }

    // public static function getData(
    //     array $data,  // data=參數, 
    //     string $id_col = 'id'  // id_col=主鍵或是搜尋的欄位名
    // ) {

    //     // 緩存時間
    //     $cacheAliveTime = 1;

    //     // 緩存Key
    //     $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

    //     return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data, $id_col) {
    //         if (!strlen($id_col)) {
    //             $id_col = 'id';
    //         }
    //         $id = $data[$id_col];

    //         $data = self::where($id_col, $id)->first();
    //         $return = $data;

    //         return $return;
    //     });
    // }

}
