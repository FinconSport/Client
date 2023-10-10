<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ModelDriverProvider extends ServiceProvider {

    public function register() {
        // ...
    }

    public function boot() {
        Builder::macro('list', function ($cacheAliveTime = 1) {
            
            // 获取模型的表名
            $tableName = $this->getModel()->getTable();
            $esTableName = "es_" . $tableName;

            // Build ES SQL
            $bindings = $this->getBindings();
            $rawSql = $this->toSql();
            $esSql = vsprintf(str_replace('?', "'%s'", $rawSql), $bindings);
            $esSql = str_replace($tableName, $esTableName, $esSql);
            $esSql = str_replace("'", "", $esSql);
            $esSql = str_replace("`", "", $esSql);

            $cacheKey = MD5($esSql);

            $data = Cache::remember($cacheKey, $cacheAliveTime, function () use ($esSql) {
                
                // create URL
                $url = 'http://72.167.135.22:29200/_sql?sql=' . $esSql . '&pretty';

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
            });

            return $data;
            
        });
    }
    
}
