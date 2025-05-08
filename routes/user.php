<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('user/{user_id}', [UserController::class, 'show'])->name('view-user');