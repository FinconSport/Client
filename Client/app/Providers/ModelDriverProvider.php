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
        Builder::macro('list', function () {
        
            // 緩存時間
            $cacheAliveTime = 1;


            $cacheKey = $this->getCacheKey();
            // 输出包含参数值的 SQL 查询语句
            dd($cacheKey);

            //
            return $this->get();
        });
    }

    protected function getCacheKey() {
        // getSQL
        $bindings = $this->getBindings();
        $sql = $this->toSql();
        $fullSql = vsprintf(str_replace('?', "'%s'", $sql), $bindings);

        $return = MD5($fullSql);

        return $return;
    }
    
}
