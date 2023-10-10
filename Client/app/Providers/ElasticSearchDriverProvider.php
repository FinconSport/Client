<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ElasticSearchDriverProvider extends ServiceProvider {

    public function register() {
        // ...
    }

    public function boot() {

        // list() method 
        Builder::macro('list', function ($cacheAliveTime = 1) {
            
            // get Model TableName
            $tableName = $this->getModel()->getTable();
            $esTableName = "es_" . $tableName;

            // Build ES SQL
            $bindings = $this->getBindings();
            $rawSql = $this->toSql();
            $esSql = vsprintf(str_replace('?', "'%s'", $rawSql), $bindings);    // getRawSQL
            $esSql = str_replace($tableName, $esTableName, $esSql); // fix es_table_name
            $esSql = str_replace("'", "", $esSql);  // remove '
            $esSql = str_replace("`", "", $esSql);  // remove `
            $cacheKey = MD5($esSql); // create CacheKey by MD5

            // use Cache
            return Cache::remember($cacheKey, $cacheAliveTime, function () use ($esSql) {
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
                // fail , return false
                return false;
            });
        });
        
        // fetch() method 
        Builder::macro('fetch', function ($cacheAliveTime = 1) {
            
            // get Model TableName
            $tableName = $this->getModel()->getTable();
            $esTableName = "es_" . $tableName;

            // Build ES SQL
            $bindings = $this->getBindings();
            $rawSql = $this->toSql();
            $esSql = vsprintf(str_replace('?', "'%s'", $rawSql), $bindings);    // getRawSQL
            $esSql = str_replace($tableName, $esTableName, $esSql); // fix es_table_name
            $esSql = str_replace("'", "", $esSql);  // remove '
            $esSql = str_replace("`", "", $esSql);  // remove `
            $cacheKey = MD5($esSql); // create CacheKey by MD5

            // use Cache
            return Cache::remember($cacheKey, $cacheAliveTime, function () use ($esSql) {
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
                        $list = $v['_source'];
                    }
                    return $list;
                } 
                // fail , return false
                return false;
            });
        });

        // total() method 
        Builder::macro('total', function ($cacheAliveTime = 1) {
            
            // get Model TableName
            $tableName = $this->getModel()->getTable();
            $esTableName = "es_" . $tableName;

            // Build ES SQL
            $bindings = $this->getBindings();
            $rawSql = $this->toSql();
            $esSql = vsprintf(str_replace('?', "'%s'", $rawSql), $bindings);    // getRawSQL
            $esSql = str_replace($tableName, $esTableName, $esSql); // fix es_table_name
            $esSql = str_replace("'", "", $esSql);  // remove '
            $esSql = str_replace("`", "", $esSql);  // remove `
            $cacheKey = MD5($esSql); // create CacheKey by MD5

            // use Cache
            return Cache::remember($cacheKey, $cacheAliveTime, function () use ($esSql) {
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
                    foreach ($data['aggregations'] as $k => $v) { 
                        foreach ($data['aggregations'] as $kk => $vv) {
                            $buckets = $vv['buckets'];
                            foreach ($buckets as $kkk => $vvv) {
                                $tmp = array();
                                $tmp[$kk] = $vvv['key'];
                                $count = 0; // 用於計算欄位順序
                                foreach ($vvv as $kkkk => $vvvv) {
                                   if ($count >= 2) {
                                        $tmp[$kkkk] = $vvvv['value'];
                                   }
                                    $count++;
                                }   
                                $list[] = $tmp;
                            }
                        }
                    }
                    return $list;
                } 
                // fail , return false
                return false;
            });
        });
    }
    
}
