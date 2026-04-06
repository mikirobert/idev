<?php

use App\Http\Controllers\IdeaController;
use App\Http\Controllers\IdeaImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Route::get('/', fn () => view('welcome'));

Route::redirect('/', '/ideas');

Route::get('/ideas', [IdeaController::class, 'index'])->name('idea.index')->middleware('auth');
Route::post('/ideas', [IdeaController::class, 'store'])->name('idea.store')->middleware('auth');
Route::get('/ideas/{idea}', [IdeaController::class, 'show'])->name('idea.show')->middleware('auth');
Route::patch('/ideas/{idea}', [IdeaController::class, 'update'])->name('idea.update')->middleware('auth');
Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])->name('idea.destroy')->middleware('auth');

Route::delete('/ideas/{idea}/image', [IdeaImageController::class, 'destroyImage'])->name('ideaImage.destroy')->middleware('auth');

Route::patch('/steps/{step}', [StepController::class, 'update'])->name('step.update')->middleware('auth');

Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');

Route::get('/login', [SessionsController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [SessionsController::class, 'store'])->middleware('guest');

Route::post('/logout', [SessionsController::class, 'destroy'])->middleware('auth');

Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('auth');
Route::patch('profile/edit', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');


Route::livewire('/livelive', 'pages::post.create');

Volt::route('/posts', 'post.index')->name('posts.index');


