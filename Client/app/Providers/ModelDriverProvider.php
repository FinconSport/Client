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

            // 緩存Key
            $tableName = (new static)->getTable();

            dd($tableName);
            //
            return $this->get();
        });
    }
    
}
