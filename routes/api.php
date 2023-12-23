<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "Api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');

Route::group(['middleware' => 'auth:api'], function ($router) {
    Route::post('/logout', 'AuthController@logout');
    Route::post('/refresh', 'AuthController@refresh');
    Route::post('/me', 'AuthController@me');
});

Route::namespace("Api")->group(function() {
    Route::get('/test', 'TestController@test');

    Route::group(['prefix' => 'blogs'], function () {
        Route::get('/', 'BlogController@getAll');
        Route::post('/', 'BlogController@createBlog');
        // bài tập về nhà
        Route::get('/{id}', 'BlogController@getBlogById');
        Route::put('/{id}', 'BlogController@updateBlog');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'CategoryController@getAll');
        
        Route::post('/', 'CategoryController@createCategory');
        Route::get('/{id}', 'CategoryController@getCategoryById');
        Route::put('/{id}', 'CategoryController@updateCategory');
    });
});