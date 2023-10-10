<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Player extends CacheModel
{
	use HasFactory;
	
	public $timestamps = false;
	protected $table = "player";

    public static function findData(
        array $data,  // data=參數, 
        string $id_col = 'id'  // id_col=主鍵或是搜尋的欄位名
    ) {

        // 緩存時間
        $cacheAliveTime = 1;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data, $id_col) {
            $id = $data[$id_col];

            $data = self::where($id_col, $id)->first();
            $return = $data;

            return $return;
        });
    }

}
