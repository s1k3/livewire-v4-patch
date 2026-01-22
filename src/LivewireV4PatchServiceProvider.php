<?php

namespace LivewireV4;

use Illuminate\Support\ServiceProvider;

class LivewireV4PatchServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laravel-v4-patch.php' => config_path('laravel-v4-patch.php'),
        ]);
    }
}
