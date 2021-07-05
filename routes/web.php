<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FriendController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('friends/{id}/accept', [FriendController::class, 'acceptRequest'])
    ->middleware(['auth', 'verified'])
    ->name('friends.accept');
Route::get('dashboard', [FriendController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::post('friends/{id}/request', [FriendController::class, 'sendRequest'])
    ->middleware(['auth', 'verified'])
    ->name('friends.request');
Route::get('friends/{id}/deny', [FriendController::class, 'denyRequest'])
    ->middleware(['auth', 'verified'])
    ->name('friends.deny');
Route::post('friends/{id}/remove', [FriendController::class, 'removeFriend'])
    ->middleware(['auth', 'verified'])
    ->name('friends.remove');
Route::post('friends/invite', [FriendController::class, 'sendInvitation'])
    ->middleware(['auth', 'verified'])
    ->name('friends.invite');
Route::get('friends', [FriendController::class, 'getAcceptedFriendList'])
    ->middleware(['auth', 'verified'])
    ->name('friends.list');
require __DIR__ . '/auth.php';
