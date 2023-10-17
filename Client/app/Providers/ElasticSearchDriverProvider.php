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
        Builder::macro('list', function ($cacheAliveTime=1,$dd=false) {
            
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
            if ($dd !== false) {
                dd($esSql);
            }
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
        Builder::macro('total', function ($cacheAliveTime=1,$dd=false) {
            
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
            if ($dd !== false) {
                dd($esSql);
            }
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
                    $list = $data['aggregations'];
                    return $list;
                } 
                // fail , return false
                return false;
            });
        });

        
        // queries () method 
        Builder::macro('queries', function ($JSON, $cacheAliveTime=1,$dd=false) {

            // get Model TableName
            $tableName = "`" . $this->getModel()->getTable() . "`";
            $esTableName = "`es_" . $tableName."`";

            $cacheKey = MD5($JSON); // create CacheKey by MD5
            
            // use Cache
            return Cache::remember($cacheKey, $cacheAliveTime, function () use ($esTableName, $JSON) {
                $url = 'http://72.167.135.22:29200/' . $esTableName . '_search';

                $esUser = env("ES_USER");
                $esPass = env("ES_PASS");

                // 构建请求头，根据 Elasticsearch 的要求设置适当的头部信息
                $headers = [
                    'Authorization' => 'Basic ' . base64_encode("$esUser:$esPass"),
                    'Content-Type' => 'application/json', // 设置请求内容类型为 JSON
                ];
        
                // 发送原始 POST 请求
                $response = Http::withHeaders($headers)
                ->send('POST', $url, [
                    'body' => $JSON // 将原始查询内容作为请求主体发送
                ]);
        
                // 检查响应并处理它
                if ($response->successful()) {
                    return $response->json(); // 如果请求成功，返回 JSON 响应
                } else {
                    // 处理错误
                    // $response->status() 可以获取 HTTP 状态码
                    // $response->body() 可以获取响应内容
                    // 这里可以根据需要处理错误情况
                    return false; // 或者抛出异常，具体取决于您的需求
                }
            });
        });


    }
    
}
