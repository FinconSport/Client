<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class ElasticSearchDriverProvider extends ServiceProvider {

    protected $totalCount = 0;

    public function register() {
        // ...
    }

    public function boot() {

        // list() method 
        Builder::macro('list', function ($cacheAliveTime=1) {
            
            // get Model TableName
            $tableName = "`" . $this->getModel()->getTable() . "`";
            $esTableName = "`es_" . $tableName."`";

            // Build ES SQL
            $bindings = $this->getBindings();
            $rawSql = $this->toSql();
            $esSql = vsprintf(str_replace('?', "'%s'", $rawSql), $bindings);    // getRawSQL
            $esSql = str_replace($tableName, $esTableName, $esSql); // fix es_table_name
            $esSql = str_replace("'", "", $esSql);  // remove '
            $esSql = str_replace("`", "", $esSql);  // remove `
            dd($esSql);
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

        // page() method 
        Builder::macro('page', function ($perPage=20, $cacheAliveTime=1) {
            
            // get Model TableName
            $tableName = "`" . $this->getModel()->getTable() . "`";
            $esTableName = "`es_" . $tableName."`";

            // use skip and take
            $page = request()->input('page', 1);

            $skip = ($page - 1) * $perPage;
            $take = $perPage;
        
            $this->skip($skip)->take($take);
            
            // Build ES SQL
            $bindings = $this->getBindings();
            $rawSql = $this->toSql();
            $esSql = vsprintf(str_replace('?', "'%s'", $rawSql), $bindings);    // getRawSQL
            $esSql = str_replace($tableName, $esTableName, $esSql); // fix es_table_name
            $esSql = str_replace("'", "", $esSql);  // remove '
            $esSql = str_replace("`", "", $esSql);  // remove `
            $cacheKey = MD5($esSql); // create CacheKey by MD5
            // use Cache
            $result = Cache::remember($cacheKey, $cacheAliveTime, function () use ($esSql) {
                $url = 'http://72.167.135.22:29200/_sql?sql=' . $esSql . '&pretty';

                $esUser = env("ES_USER");
                $esPass = env("ES_PASS");

                // Basic Auth
                $response = Http::withBasicAuth($esUser, $esPass)->get($url);
                
                // check successful
                if ($response->successful()) {
                    // json decode
                    $data = $response->json();

                    // get TotalCount 
                    $this->totalCount = $data['hits']['total'];

                    $list = array();
                    foreach ($data['hits']['hits'] as $k => $v) {
                        $list[] = $v['_source'];
                    }
                    return $list;
                } 
                // fail , return false
                return false;
            });

            // Create a LengthAwarePaginator instance
            $paginator = new LengthAwarePaginator(
                $result,
                $this->totalCount,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return $paginator;
        });

        // cc() method 
        Builder::macro('cc', function ($cacheAliveTime=1) {
            
            // get Model TableName
            $tableName = "`" . $this->getModel()->getTable() . "`";
            $esTableName = "`es_" . $tableName."`";

            // Build ES SQL
            $bindings = $this->getBindings(); 

            // 获取原始查询构建器
            $queryBuilder = $this->toBase();
            $countQueryBuilder = clone $queryBuilder;
            $countQueryBuilder->selectRaw('COUNT(*) as aggregate');
            $rawSql = $countQueryBuilder->toSql();

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
                    return $data['aggregations']['aggregate']['value'];
                } 
                // fail , return false
                return false;
            });
        });

        // fetch() method 
        Builder::macro('fetch', function ($cacheAliveTime=1) {
            
            // get Model TableName
            $tableName = "`" . $this->getModel()->getTable() . "`";
            $esTableName = "`es_" . $tableName."`";

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
        Builder::macro('total', function ($cacheAliveTime=1) {
            
            // get Model TableName
            $tableName = "`" . $this->getModel()->getTable() . "`";
            $esTableName = "`es_" . $tableName."`";

            // Build ES SQL
            $bindings = $this->getBindings();
            $rawSql = $this->toSql();
            $esSql = vsprintf(str_replace('?', "'%s'", $rawSql), $bindings);    // getRawSQL
            $esSql = str_replace($tableName, $esTableName, $esSql); // fix es_table_name
            $esSql = str_replace("'", "", $esSql);  // remove '
            $esSql = str_replace("`", "", $esSql);  // remove `
            dd($esSql);
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
