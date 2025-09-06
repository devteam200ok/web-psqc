<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckAdmin;
use App\Providers\ScheduleServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([ScheduleServiceProvider::class]) // 스케줄링 프로바이더 등록 ✅
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth'        => \App\Http\Middleware\Authenticate::class,
            'check_admin' => CheckAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
