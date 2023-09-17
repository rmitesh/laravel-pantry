<?php

namespace Rmitesh\LaravelPantry;

use Illuminate\Support\ServiceProvider;
use Rmitesh\LaravelPantry\Console\Commands\MakePantryCommand;

class LaravelPantryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakePantryCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
