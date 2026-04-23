<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        $productAbilities = ['insert-product', 'edit-product', 'delete-product'];
        foreach ($productAbilities as $ability) {
           Gate::define($ability, function ($user) {
                return $user->roles()->whereIn('role', ['admin', 'owner'])->exists();
            });
        }
    }
}
