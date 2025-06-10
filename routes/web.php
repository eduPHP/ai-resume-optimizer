<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::inertia('/optimizations/create', 'Optimization')->name('optimizations.create');
    Route::get('/optimizations/{optimization}', [\App\Http\Controllers\OptimizationController::class, 'show'])->name('optimizations.show');
    Route::post('/optimizations/create', [\App\Http\Controllers\OptimizationController::class, 'store'])->name('optimizations.store');
    Route::put('/optimizations/{optimization}', [\App\Http\Controllers\OptimizationController::class, 'update'])->name('optimizations.update');
    Route::get('view-optimized/{optimization}', [\App\Http\Controllers\DownloadOptimizedResumeController::class, 'sample']);

});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
