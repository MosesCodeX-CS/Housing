<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\SmsService::class);
        $this->app->singleton(\App\Services\MediaService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Database\Schema\Blueprint::macro("timestampsTz", function () {
            $this->timestampTz("created_at")->nullable();
            $this->timestampTz("updated_at")->nullable();
        });
    }
}
