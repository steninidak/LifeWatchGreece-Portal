<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('title', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[\pL\pN\s,.!\(\)+=@#$?;_-]+$/u', $value);
        });

        Validator::extend('filename', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[\pL\pN\s\(\)_-]+$/u', $value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
