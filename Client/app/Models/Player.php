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

    public static function getData(
        $data,  // data=參數, 
        $id_col = 'id',  // id_col=主鍵或是搜尋的欄位名
        $col = null // col=要取出的欄位(字串或陣列),未指定則回傳整個array
    ) {

        // 緩存時間
        $cacheAliveTime = 1;

        // 緩存Key
        $cacheKey = (new static)->getCacheKey($data , __FUNCTION__);

        return Cache::remember($cacheKey, $cacheAliveTime, function () use ($data, $id_col, $col) {
            if (!strlen($id_col)) {
                $id_col = 'id';
            }
            $id = $data[$id_col];

            if ($col && strlen($col)) {
                $data = self::where($id_col, $id)->select($col)->first();
            } else {
                $data = self::where($id_col, $id)->first();
            }
            $return = $data;

            return $return;
        });
    }

}

