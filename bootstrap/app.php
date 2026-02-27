<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'hr' => \App\Http\Middleware\HrMiddleware::class,
            'staff' => \App\Http\Middleware\StaffMiddleware::class,
            'dc' => \App\Http\Middleware\DcMiddleware::class,
            'rd' => \App\Http\Middleware\RdMiddleware::class,
            'ard' => \App\Http\Middleware\ArdMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (TokenMismatchException $e, Request $request): Response {
            Log::warning('Token mismatch on form submit', [
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_id' => optional($request->user())->id,
            ]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session expired. Reload and try again.'], 419);
            }
            return redirect()->back()->withInput()->with('error', 'Your session expired. Please submit the form again.');
        });
    })->create();
