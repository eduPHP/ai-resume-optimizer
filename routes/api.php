<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('resumes', [\App\Http\Controllers\ResumesController::class, 'index'])->name('resumes.index');
    Route::post('resumes', [\App\Http\Controllers\ResumesController::class, 'store'])->name('resumes.store');
    Route::get('resumes/{resume}', [\App\Http\Controllers\ResumesController::class, 'show'])->name('resumes.show');
    Route::get('optimizations', [\App\Http\Controllers\OptimizationController::class, 'index'])->name('optimizations.index');
    Route::post('download-optimized/{optimization}', \App\Http\Controllers\DownloadOptimizedResumeController::class);
});
Route::get('view-download-optimized',[ \App\Http\Controllers\DownloadOptimizedResumeController::class, 'sample']);
