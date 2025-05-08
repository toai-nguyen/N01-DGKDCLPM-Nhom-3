<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\NovelController;

// Route::apiResource('api/novels', NovelController::class);

Route::group([
    'prefix' => 'api/v1',

], function () {
    Route::apiResource('novels', NovelController::class);
    Route::apiResource('create-novel', NovelController::class);
    Route::apiResource('update-novel', NovelController::class);
    Route::apiResource('delete-novel', NovelController::class);
});
