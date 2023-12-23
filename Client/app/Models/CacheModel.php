<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class CacheModel extends Model {
    // 設定cacheKey
    protected static function getCacheKey($data, $funcName) {

        $tableName = (new static)->getTable();
		$key = json_encode($data,true);
		$cacheKey = $tableName . "_" . $funcName . "_" . $key;
        $cacheKey = MD5($cacheKey);

        return $cacheKey;
    }

}


