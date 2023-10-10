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

            $sql = $this->toSql();

            dd($sql);
            //
            return $this->get();
        });
    }
    
}
