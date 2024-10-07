<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\User;
use Illuminate\Support\Facades\Route;

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


Route::middleware('guest')->group(function (){
    Route::get('/login' , [ AuthController::class , 'loginView' ])->name('login');
    Route::post('/login' , [ AuthController::class , 'login']);
});

Route::post('/logout' , [AuthController::class , 'logout'])->name('logout');
Route::get('/password/reset' , [AuthController::class , 'getResetForm']);
Route::post('/password/reset' , [AuthController::class , 'reset']);
Route::post('/reset' , [AuthController::class , 'resetPassword']);

Route::middleware('auth')->group(function (){

    Route::post('/password' , [AuthController::class , 'password']);
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

    /*
     * Users
     *
     */

    Route::prefix('users')->group(function (){
        Route::get('/' ,  [UserController::class , 'index']);
        Route::post('/create' ,  [UserController::class , 'store']);
        Route::get('/{model}/view' ,  [UserController::class , 'view']);
        Route::get('/{model}/activate' ,  [UserController::class , 'activate']);
        Route::get('/{model}/deactivate' ,  [UserController::class , 'deactivate']);
        Route::post('/{model}/update' ,  [UserController::class , 'update']);
    });

    Route::prefix('roles')->group(function (){
        Route::get('/' ,  [RoleController::class , 'index']);
        Route::post('/create' ,  [RoleController::class , 'store']);
        Route::get('/{model}/view' ,  [RoleController::class , 'view']);
        Route::post('/{model}/update' ,  [RoleController::class , 'update']);
    });

    Route::prefix('permissions')->group(function (){
        Route::get('/' ,  [PermissionController::class , 'index']);
    });

});



