<?php

namespace LivewireV4;

use Illuminate\Support\ServiceProvider;
use LivewireV4\Commands\ConvertToMultiFileComponent;

class LivewireV4PatchServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/livewire-v4-patch.php' => config_path('livewire-v4-patch.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ConvertToMultiFileComponent::class,
            ]);
        }

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/livewire-v4-patch.php', 'livewire-v4-patch');
    }

}
