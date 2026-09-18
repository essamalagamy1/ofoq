<?php

use App\Http\Middleware\LanguageMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: '/api/v1',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [LanguageMiddleware::class]);
        $middleware->alias([
            'web-language' => LanguageMiddleware::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'check.parent.eligibility' => \App\Http\Middleware\CheckParentEligibility::class,
        ]);
        
        $middleware->redirectUsersTo(function () {
            if (auth()->check()) {
                if (auth()->user()->hasRole('super_admin')) {
                    return route('admin.dashboard');
                } elseif (auth()->user()->hasRole('teacher')) {
                    return route('teacher.dashboard');
                } elseif (auth()->user()->hasRole('parent')) {
                    return route('parent.dashboard');
                }
            }
            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
