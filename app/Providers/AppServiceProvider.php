<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
 use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

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
  

    public function boot()
    {
        Schema::defaultStringLength(191);

        if (app()->environment('production')) {
            try {
                // Run migrations
                Artisan::call('migrate', ['--force' => true]);

                // Auto-seed if database is empty
                if (\App\Models\Product::count() === 0) {
                    Artisan::call('db:seed', ['--force' => true]);
                }
            } catch (\Throwable $e) {
                // Prevent crash in production if something is wrong with DB
                \Log::error('Auto-migration/seeding failed: ' . $e->getMessage());
            }
        }
    }

}
