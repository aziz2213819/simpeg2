<?php

use App\Http\Middleware\EnsureGuestDataExists;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsAdminSampah;
use App\Http\Middleware\IsAdminSimpeg;
use App\Http\Middleware\IsPegawai;
use App\Http\Middleware\IsPetugasSampah;
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
        $middleware->alias([
            'admin' => IsAdmin::class,
            'tamu.cek' => EnsureGuestDataExists::class,
            'isAdminSimpeg' => IsAdminSimpeg::class,
            'isAdminSampah' => IsAdminSampah::class,
            'isPegawai' => IsPegawai::class,
            'isPetugasSampah' => IsPetugasSampah::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
