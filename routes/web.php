<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserSearch;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/friends', function () {
    return view('friends');
})->middleware(['auth', 'verified'])->name('friends');

Route::get('/video', function () {
    return view('video');
})->middleware(['auth', 'verified'])->name('video');

Route::get('/chats', function () {
    return view('chats');
})->middleware(['auth', 'verified'])->name('chats');

Route::get('/user_profile', function () {
    return view('user_profile');
})->middleware(['auth', 'verified'])->name('user_profile');

Route::get('/feed', function () {
    return view('feed');
})->middleware(['auth', 'verified'])->name('feed');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/avatar/{user}', [ProfileController::class, 'avatar'])->name('profile.avatar');
Route::post('/friends', [UserSearch::class, 'submit'])->name('contact.submit');

require __DIR__ . '/auth.php';
