<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\UserRoleMiddleware;
use App\Http\Middleware\HandleCorsRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies for shared hosting / reverse proxy environments
        // This ensures HTTPS is detected correctly so session cookies work properly
        $middleware->trustProxies(at: '*');

        // Only redirect guests to login, don't redirect authenticated users away from login
        $middleware->redirectGuestsTo(fn(Request $request) => route('login.show'));
        $middleware->web(HandleCorsRequests::class);
        $middleware->web(SetLocale::class);
        $middleware->alias([
            'role' => UserRoleMiddleware::class,
        ]);
    })


    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
