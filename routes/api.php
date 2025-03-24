<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('resume', \App\Http\Controllers\ResumeUploadController::class);
    Route::post('parser/{resume}', \App\Http\Controllers\ResumeParserController::class);
    Route::post('optimize/{resume}', \App\Http\Controllers\OptimizeResumeController::class);
    Route::post('download-optimized/{resume}', \App\Http\Controllers\DownloadOptimizedResumeController::class);
});
Route::get('view-download-optimized',[ \App\Http\Controllers\DownloadOptimizedResumeController::class, 'sample']);
