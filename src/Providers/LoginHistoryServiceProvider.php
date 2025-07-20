<?php

namespace LoginHistory\Providers;

use LoginHistory\Console\Commands\CLoadTestUserLoginHistories;
use LoginHistory\Console\Commands\CMigrateLoginHistory;

use Illuminate\Support\ServiceProvider;

class LoginHistoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register config
        $this->mergeConfigFrom(
            __DIR__.'/../../config/login-history.php',
            'login-history'
        );
    }

    public function boot()
    {
        // Register migration without publication
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');

        // Publisg config
        $this->publishes([
            __DIR__.'/../../config/login-history.php' => config_path('login-history.php'),
        ], 'login-history-config');

        // Registret console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CLoadTestUserLoginHistories::class,
                CMigrateLoginHistory::class
            ]);
        }
    }
}