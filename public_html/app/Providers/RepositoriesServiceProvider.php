<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\LeadRepositoryInterface::class,
            \App\Repositories\LeadRepository::class
        );

        $this->app->bind(
            \App\Contracts\LeadsRepositoryInterface::class,
            \App\Repositories\LeadsRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
