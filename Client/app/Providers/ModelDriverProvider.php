<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class ModelDriverProvider extends ServiceProvider {

    public function boot() {
        Builder::macro('list', function () {
            return $this->get();
        });
    }
}
