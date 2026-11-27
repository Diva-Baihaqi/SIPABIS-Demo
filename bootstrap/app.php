<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            // Jika token CSRF kedaluwarsa saat pengguna mencoba keluar (/logout),
            // jangan tampilkan layar 419; tetap akhiri sesi secara aman ke halaman Logout.
            if ($e->getStatusCode() === 419 && $request->is('logout') && ! $request->expectsJson()) {
                if (Auth::check()) {
                    Auth::logout();
                }

                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                return redirect()->route('logout.page')->with('logged_out', true);
            }

            return null;
        });
    })->create();
