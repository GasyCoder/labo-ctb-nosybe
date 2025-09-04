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
    ->withMiddleware(function (Middleware $middleware): void {
        // Middlewares personnalisés
        $middleware->alias([
            // Votre middleware existant
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'role.redirect' => \App\Http\Middleware\RoleRedirectMiddleware::class,
            
            // Middlewares complémentaires pour plus de flexibilité
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'secretaire' => \App\Http\Middleware\SecretaireMiddleware::class,
            'technicien' => \App\Http\Middleware\TechnicienMiddleware::class,
            'biologiste' => \App\Http\Middleware\BiologisteMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();