<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class ModelDriverProvider extends ServiceProvider {

    public function register() {
        // ...
    }
    
    public function boot() {
        Builder::macro('list', function () {
            return $this->get();
        });
    }
}
