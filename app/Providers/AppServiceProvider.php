<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('Helpers/PaymentHelper.php');
    }

    public function boot(): void
    {
        $abilities = [
            'view-admin',
            'insert-author', 'update-author', 'delete-author',
            'insert-book',   'update-book',   'delete-book',
            'create-publisher', 'edit-publisher', 'delete-publisher',
            'manage-stores',
        ];
        foreach ($abilities as $ability) {
            Gate::define($ability, function ($user) {
                return $user->roles()->whereIn('role', ['admin', 'owner'])->exists();
            });
        }
    }
}
