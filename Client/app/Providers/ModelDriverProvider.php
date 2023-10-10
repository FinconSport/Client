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
        Builder::macro('list', function ($cacheAliveTime = 3600) {
            
            // 获取模型的表名
            $tableName = $this->getModel()->getTable();
            $esTableName = "es_" . $tableName;

            // getSQL
            $bindings = $this->getBindings();
            $sql = $this->toSql();
            $fullSql = vsprintf(str_replace('?', "'%s'", $sql), $bindings);
            $fullSql = str_replace($tableName, $esTableName, $fullSql);

            dd($fullSql);
            $cacheKey = MD5($fullSql);

            $data = Cache::remember($cacheKey, $cacheAliveTime, function () {
                return $this->get();
            });

            return $data;
            
        });
    }
    
}
