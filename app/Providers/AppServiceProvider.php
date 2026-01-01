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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate --force');
                
                if (\App\Models\User::count() === 0) {
                    \Illuminate\Support\Facades\Artisan::call('db:seed --force');
                }
            } catch (\Exception $e) {
                // Log error but don't crash app if DB isn't ready
                \Illuminate\Support\Facades\Log::error('Auto-migration failed: ' . $e->getMessage());
            }
        }
    }
}
