<?php

namespace Lia\Filemanager;

use Illuminate\Support\ServiceProvider;

class FilemanagerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom( __DIR__ . '/../config/lia-filemanager.php', 'lia-filemanager' );
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lia-filemanager');
        $this->loadTranslationsFrom( __DIR__ . '/../resources/lang', 'lia-filemanager' );

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'lia-filemanager');
            $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang')], 'lia-filemanager');
            $this->publishes([__DIR__.'/../resources/assets' => public_path('vendor/lia-filemanager')], 'lia-filemanager');
        }

        ExtensionLia::boot();
    }
}

//php artisan lia:import filemanager
//php artisan vendor:publish --provider="Lia\Filemanager\FilemanagerServiceProvider"