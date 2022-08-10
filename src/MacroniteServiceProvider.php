<?php

namespace Lyhty\Macronite;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class MacroniteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/macronite.php', 'macronite');

        $this->app->singleton(MacroniteService::class, function ($app) {
            return new MacroniteService($app['config']['macronite'], $app->make(Filesystem::class));
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MacroCacheCommand::class,
                Commands\MacroClearCommand::class,
                Commands\MacroGenerateCommand::class,
                Commands\MacroMakeCommand::class,
            ]);
        }
    }
}
