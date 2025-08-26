<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::inertia('/optimizations/create', 'Optimization')->name('optimizations.create');
    Route::get('/optimizations/{optimization}', [\App\Http\Controllers\OptimizationController::class, 'show'])->name('optimizations.show');
    Route::post('/optimizations/create', [\App\Http\Controllers\OptimizationController::class, 'store'])->name('optimizations.store');
    Route::put('/optimizations/{optimization}', [\App\Http\Controllers\OptimizationController::class, 'update'])->name('optimizations.update');
    Route::put('/optimizations/{optimization}/cancel', [\App\Http\Controllers\OptimizationController::class, 'cancel'])->name('optimizations.cancel');
    Route::put('/optimizations/{optimization}/toggle-applied', [\App\Http\Controllers\OptimizationController::class, 'toggleApplied'])->name('optimizations.toggle-applied');
    Route::delete('/optimizations/{optimization}', [\App\Http\Controllers\OptimizationController::class, 'destroy'])->name('optimizations.destroy');
    Route::get('view-optimized/{optimization}', [\App\Http\Controllers\DownloadOptimizedResumeController::class, 'sample']);

});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
