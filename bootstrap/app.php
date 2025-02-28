<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Agregar el middleware de CORS globalmente
        //$middleware->append(\App\Http\Middleware\CorsMiddleware::class); 
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

