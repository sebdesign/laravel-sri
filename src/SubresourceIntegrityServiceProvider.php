<?php

namespace Sebdesign\SRI;

use Illuminate\Support\ServiceProvider;

class SubresourceIntegrityServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
               __DIR__.'/../config/sri.php' => config_path('sri.php'),
           ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sri.php', 'sri');

        $this->app->singleton(Hasher::class, function ($app) {
            return new Hasher(hash_algos(), $app['config']->get('sri'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Hasher::class];
    }
}
