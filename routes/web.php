<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Auth;
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

Route::prefix('users')->group(static function () {
    Auth::routes();

    Route::get('user/{user}', [\App\Http\Controllers\UserController::class, 'show'])->whereNumber(['user']);
    Route::put('user/{user}', [\App\Http\Controllers\UserController::class, 'edit'])->whereNumber(['user']);
});
