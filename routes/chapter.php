<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChapterController;


Route::get('novels/reading/{novel_id}/{chapter_id}', 
[ChapterController::class, 'show'])->name('chapter.show');

Route::get('novels/edit_chapter/{novel_id}/{chapter_id}/', 
[ChapterController::class, 'edit'])->name('chapter.edit');

Route::post('novels/update_chapter/{novel_id}/{chapter_id}', 
[ChapterController::class, 'update'])->name('chapter.update');

Route::get('novels/create_chapter/{novel_id}', 
[ChapterController::class, 'create'])->name('chapter.create');

Route::post('novels/add_chapter/{novel_id}/', 
[ChapterController::class, 'store'])->name('chapter.store');
