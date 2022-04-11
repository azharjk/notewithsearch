<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NoteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [RegisterController::class, 'index'])->name('register.index');
Route::post('/login', [LoginController::class, 'index'])->name('login.index');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LogoutController::class, 'index'])->name('logout.index');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/notes/{id}', [NoteController::class, 'show'])->name('note.show');
    Route::post('/notes', [NoteController::class, 'store'])->name('note.store');
});
