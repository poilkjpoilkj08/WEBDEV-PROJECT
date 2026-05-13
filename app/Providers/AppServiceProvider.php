<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $abilities = [
            'insert-author', 'update-author', 'delete-author',
            'insert-book',   'update-book',   'delete-book',
            'manage-stores',
        ];
        foreach ($abilities as $ability) {
            Gate::define($ability, function ($user) {
                return $user->roles()->whereIn('role', ['admin', 'owner'])->exists();
            });
        }
    }
}
