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

    // ES Query操作
    protected static function getESQuery($sql) {
        
        // create URL
        $url = 'http://72.167.135.22:29200/_sql?sql=' . $sql . '&pretty';

        $esUser = env("ES_USER");
        $esPass = env("ES_PASS");

        // Basic Auth
        $response = Http::withBasicAuth($esUser, $esPass)->get($url);
        
        // check successful
        if ($response->successful()) {
            // json decode
            $data = $response->json();
            $list = array();
            foreach ($data['hits']['hits'] as $k => $v) {
                $list[] = $v['_source'];
            }

            return $list;
        } 

        return false;
    }
    
    // ES 聚合操作
    protected static function getESAgg($sql) {
        
        // create URL
        $url = 'http://72.167.135.22:29200/_sql?sql=' . $sql . '&pretty';

        $esUser = env("ES_USER");
        $esPass = env("ES_PASS");

        // 发送请求，并使用Basic Auth认证
        $response = Http::withBasicAuth($esUser, $esPass)->get($url);
        
        // 检查响应是否成功
        if ($response->successful()) {
            $data = $response->json();
            $list = $data['aggregations'];
            return $list;
        } 
        
        return false;
    }


    // getList
    protected static function list() {
        $return = self::get();
        return $return;
    }
}


