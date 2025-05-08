<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;

Route::get('/following', [HomeController::class, 'follow'])->name('following');
Route::get('/list-project', [HomeController::class, 'listProject'])->name('list-project');
Route::get('/search/{search}', [HomeController::class, 'search'])->name('search');
Route::get('/advanced_search', [HomeController::class, 'viewAdvancedSearch'])->name('advanced-search');
Route::post('/advanced_search_query', [HomeController::class, 'query'])->name('advanced-search-query');
Route::get('/settings', [HomeController::class, 'settings'])->name('settings');
