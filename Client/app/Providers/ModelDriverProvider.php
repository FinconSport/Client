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

            // getSQL
            $bindings = $this->getBindings();
            $sql = $this->toSql();
            $fullSql = vsprintf(str_replace('?', "'%s'", $sql), $bindings);
            $cacheKey = MD5($fullSql);

            return Cache::remember($cacheKey, $cacheAliveTime, function() {
                return $this->get();
            });
            
        });
    }
    
}
