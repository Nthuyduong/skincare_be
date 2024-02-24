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
Route::prefix('/admin')->group(function() {
    Route::post('/login', 'AuthController@loginAdmin');
    Route::get('/refresh', 'AuthController@refreshAdmin');
});
Route::group(['middleware' => 'auth:admin'], function() {
    Route::prefix('/admin')->group(function() {
        Route::post('/logout', 'AuthController@logoutAdmin');
        Route::get('/info', 'AuthController@adminInfo');
    });
});

Route::namespace("Api")->group(function() { 

    Route::get('/test', 'TestController@test');
    Route::prefix('/admin')->group(function() {
        Route::group(['middleware' => 'auth:admin'], function ($router) {
            Route::group(['prefix' => 'categories'], function () {
                Route::get('/', 'CategoryController@getAll');
                Route::post('/', 'CategoryController@createCategory');
                Route::get('/{id}', 'CategoryController@getCategoryById');
                Route::post('/{id}', 'CategoryController@updateCategory');
                Route::delete('/{id}', 'CategoryController@deleteCategory');
                Route::get('/{id}/childrens', 'CategoryController@getCategoriesByParentId');
            });
            Route::group(['prefix' => 'images'], function () {
                Route::get('/', 'ImageController@getAll');
                Route::post('/', 'ImageController@uploadImage');
                Route::get('/{id}', 'ImageController@getImageById');
                Route::post('/update', 'ImageController@updateImage');
                Route::post('/delete', 'ImageController@deleteImage');
            });
            Route::group(['prefix' => 'blogs'], function () {
                Route::get('/', 'BlogController@getAll');
                Route::post('/', 'BlogController@createBlog');
                Route::get('/{id}', 'BlogController@getBlogById');
                Route::post('/{id}', 'BlogController@updateBlog');
        
                Route::get('/slug/{slug}', 'BlogController@getBlogBySlug');
            });
            Route::group(['prefix' => 'ingredients'], function () {
                Route::get('/', 'IngredientController@getAll');
                Route::post('/', 'IngredientController@createIngredient');
                Route::get('/{id}', 'IngredientController@getIngredientById');
                Route::post('/{id}', 'IngredientController@updateIngredient');
                Route::delete('/{id}', 'IngredientController@deleteIngredient');
            });
        });
    });
    Route::group(['prefix' => 'blogs'], function () {
        Route::get('/', 'BlogController@getAll');
        Route::get('/{id}', 'BlogController@getBlogById');
        Route::get('/slug/{slug}', 'BlogController@getBlogBySlug');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'CategoryController@getAll');
        Route::get('/{id}', 'CategoryController@getCategoryById');
        Route::get('/{id}/childrens', 'CategoryController@getCategoriesByParentId');
    });

    

    Route::group(['prefix' => 'ingredients'], function () {
        Route::get('/', 'IngredientController@getAll');
        Route::get('/{id}', 'IngredientController@getIngredientById');
    });

});