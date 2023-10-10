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

            // Build ES SQL
            $bindings = $this->getBindings();
            $modelSql = $this->toSql();
            $sql = vsprintf(str_replace('?', "'%s'", $modelSql), $bindings);
            $sql = str_replace($tableName, $esTableName, $sql);
            $sql = str_replace("'", "", $sql);
            $sql = str_replace("`", "", $sql);

            $cacheKey = MD5($sql);

            $data = Cache::remember($cacheKey, $cacheAliveTime, function () {
                return $this->get();
            });

            return $data;
            
        });
    }
    
}
