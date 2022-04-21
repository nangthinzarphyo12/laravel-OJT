<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;

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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/',  [PostController::class, 'index'])->name('postList');

Route::prefix('posts')->group(function() {
    Route::get('/create',  [PostController::class, 'create'])->name('posts.create')->middleware('auth');
    Route::post('/store', [PostController::class, 'store'])->name('posts.store');
    Route::get('/detail/{id}', [PostController::class, 'show'])->name('posts.detail');
    Route::get('/edit/{id}', [PostController::class, 'edit'])->name('posts.edit')->middleware('owner');
    Route::post('/update', [PostController::class, 'update'])->name('posts.update');
    Route::get('/delete/{id}', [PostController::class, 'destroy'])->name('posts.delete')->middleware('owner');
    Route::get('/search', [PostController::class, 'search'])->name('posts.search');
    Route::post('/comment/{id}', [PostController::class, 'commentStore'])->name('posts.comment');
});

Route::prefix('users')->group(function() {
    Route::get('/list', [UserController::class, 'index'])->name('users.list')->middleware('auth');
    Route::get('/create', [UserController::class, 'create'])->name('users.create')->middleware('role');
    Route::post('/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/detail/{id}', [UserController::class, 'show'])->name('users.detail');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit')->middleware('role');
    Route::post('/update', [UserController::class, 'update'])->name('users.update');
    Route::get('/delete/{id}', [UserController::class, 'destroy'])->name('users.delete')->middleware('role');;
    Route::get('/search', [UserController::class, 'search'])->name('users.search');
});

Route::prefix('auth')->group(function() {
    Route::get('detail/{id}', [UserController::class, 'showProfile'])->name('auth.profileDetail')->middleware('ownProfile');
    Route::get('passwordEdit/{id}', [UserController::class, 'passwordEdit'])->name('auth.passwordEdit')->middleware('ownProfile');
    Route::get('profileEdit/{id}', [UserController::class, 'profileEdit'])->name('auth.profileEdit')->middleware('ownProfile');
    Route::post('profileUpdate', [UserController::class, 'profileUpdate'])->name('auth.profileUpdate');
    Route::post('passwordUpdate', [UserController::class, 'passwordUpdate'])->name('auth.passwordUpdate');
});

Route::get('logout', [LoginController::class, 'logOut'])->name('logout');