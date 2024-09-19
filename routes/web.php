<?php

use App\Http\Controllers\get_chat_messages;
use App\Models\User;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserSearch;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileUserShow;
use App\Http\Controllers\GetUserChats;
use App\Http\Controllers\get_user;
use App\Http\Controllers\GetKeys;
use App\Http\Controllers\SendMessage;

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
// Route::resource('users', UserController::class);

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

Route::get('/user_profile', [ProfileUserShow::class, 'getUserName']);
// Route::get('/test', [ProfileUserShow::class, 'getUserName']);

Route::get('/test', function () {
    return view('test');
});
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
Route::get('/get_chats', [GetUserChats::class, 'getChats']);
Route::get('/get_user', [get_user::class, 'get_user']);
Route::get('/send_message', [SendMessage::class, 'send_message']);
Route::get('/get_keys', [GetKeys::class, 'get_keys']);
Route::get('/get_messages_from_chat', [get_chat_messages::class, 'get_messages_from_chat']);
require __DIR__ . '/auth.php';
