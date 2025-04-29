<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('tasks')->group(function () {
    Route::get('/', [\App\Http\Controllers\TaskController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\TaskController::class, 'store']);
    Route::post('/filter', [App\Http\Controllers\TaskController::class, 'list']);
});

