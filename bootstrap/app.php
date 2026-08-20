<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        //console: __DIR__ . '/../routes/console.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        App\Providers\AppServiceProvider::class,
        App\Providers\ViewServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->trustProxies(at: '*');
        
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
            'admin.guest' => \App\Http\Middleware\AdminGuest::class,
            'api.token' => \App\Http\Middleware\ApiTokenMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, \Illuminate\Http\Request $request) {
            $message = 'Le fichier envoyé est trop volumineux. Taille maximale autorisée : ' . \App\Support\UploadLimit::label() . '.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 413);
            }

            return redirect()->back()->withInput()->with('error', $message);
        });
    })
    ->create();
