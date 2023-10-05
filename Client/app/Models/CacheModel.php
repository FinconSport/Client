<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CacheModel extends Model {

    // 設定cacheKey
    protected static function getCacheKey($data) {

        $tableName = (new static)->getTable();
		$key = json_encode($data,true);
		$cacheKey = MD5($tableName . "_" . __FUNCTION__ . "_" . $key);

        return $cacheKey;
    }
}
