<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('resumes', [\App\Http\Controllers\ResumesController::class, 'index'])->name('resumes.index');
    Route::post('resumes', [\App\Http\Controllers\ResumesController::class, 'store'])->name('resumes.store');
    Route::delete('resumes/{resume}', [\App\Http\Controllers\ResumesController::class, 'destroy'])->name('resumes.destroy');
    Route::post('jobs/information', [\App\Http\Controllers\JobsController::class, 'crawl'])->name('jobs.crawl');
    Route::get('resumes/{resume}', [\App\Http\Controllers\ResumesController::class, 'show'])->name('resumes.show');
    Route::get('optimizations', [\App\Http\Controllers\OptimizationController::class, 'index'])->name('optimizations.index');
    Route::post('download-optimized/{optimization}', [\App\Http\Controllers\DownloadOptimizedResumeController::class, 'resume'])->name('optimizations.download');
    Route::post('download-cover/{optimization}', [\App\Http\Controllers\DownloadOptimizedResumeController::class, 'cover'])->name('optimizations.download-cover');
    Route::post('/optimizations/create', [\App\Http\Controllers\UnattendedOptimizationController::class, 'store'])->name('optimizations.unattended-store');
});
Route::get('view',[ \App\Http\Controllers\DownloadOptimizedResumeController::class, 'sample']);
