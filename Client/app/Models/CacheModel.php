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
        } else {
            // if fail
            return response()->json(['error' => 'request fail'], 500);
        }
    }
    
    // ES 聚合操作
    protected static function getESAgg($sql) {
        
        $sql = "select agent_id , SUM(bet_amount) as bet_amount from es_game_order group by agent_id";
        // 获取要发送请求的URL
        $url = 'http://72.167.135.22:29200/_sql?sql=' . $sql . '&pretty';

        $esUser = env("ES_USER");
        $esPass = env("ES_PASS");

        // 发送请求，并使用Basic Auth认证
        $response = Http::withBasicAuth($esUser, $esPass)->get($url);
        
        // 检查响应是否成功
        if ($response->successful()) {
            // 解码JSON响应
            $data = $response->json();

            $list = $data['aggregations'];

            return $list;
        } else {
            // 处理请求失败的情况

            dd($response);
            return response()->json(['error' => '请求失败'], 500);
        }
    }
}
