<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CacheModel extends Model {

    // 設定cacheKey
    protected static function getCacheKey($data, $funcName) {

        $tableName = (new static)->getTable();
		$key = json_encode($data,true);
		$cacheKey = $tableName . "_" . $funcName . "_" . $key;

        dd($cacheKey);

        return $cacheKey;
    }
}
