<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('resumes', [\App\Http\Controllers\ResumesController::class, 'index'])->name('resumes.index');
    Route::post('resumes', [\App\Http\Controllers\ResumesController::class, 'store'])->name('resumes.store');
    Route::get('resumes/{resume}', [\App\Http\Controllers\ResumesController::class, 'show'])->name('resumes.show');
    Route::post('optimize/{resume}', \App\Http\Controllers\OptimizeResumeController::class);
    Route::post('download-optimized/{resume}', \App\Http\Controllers\DownloadOptimizedResumeController::class);
});
Route::get('view-download-optimized',[ \App\Http\Controllers\DownloadOptimizedResumeController::class, 'sample']);
