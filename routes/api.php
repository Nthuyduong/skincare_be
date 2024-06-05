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


Route::get('/login/{provider}', 'AuthController@redirectToProvider');
Route::post('/login/{provider}/callback', 'AuthController@handleProviderCallback');
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
                Route::post('/publish/{id}', 'BlogController@publishBlog');
                Route::post('/update/status', 'BlogController@updateStatusBlogs');
                Route::post('/delete-many', 'BlogController@deleteBlogs');
                Route::get('/slug/{slug}', 'BlogController@getBlogBySlug');
                Route::get('/', 'BlogController@getAll');
                Route::post('/', 'BlogController@createBlog');
               
                Route::get('/{id}', 'BlogController@getBlogById');
                Route::post('/{id}', 'BlogController@updateBlog');
                Route::delete('/{id}', 'BlogController@deleteBlog');
            });
            Route::group(['prefix' => 'ingredients'], function () {
                Route::get('/', 'IngredientController@getAll');
                Route::post('/', 'IngredientController@createIngredient');
                Route::get('/{id}', 'IngredientController@getIngredientById');
                Route::post('/{id}', 'IngredientController@updateIngredient');
                Route::delete('/{id}', 'IngredientController@deleteIngredient');
            });

            Route::group(['prefix' => 'contacts'], function () {
                Route::get('/', 'ContactController@getAll');
                Route::get('/{id}', 'ContactController@getContactById');
                Route::delete('/{id}', 'ContactController@deleteContact');
            });
            Route::group(['prefix' => 'subscribes'], function () {
                Route::get('/', 'SubscriberController@getAll');
                Route::get('/{id}', 'Subscriberontroller@getSubscribeById');
                Route::delete('/{id}', 'SubscriberController@delete');
            });

            Route::group(['prefix' => 'settings'], function () {
                Route::get('/mail/{type}', 'SettingController@getSettingMail');
                Route::post('/mail/{type}', 'SettingController@updateSettingMail');
                Route::post('/mail/test/{type}', 'SettingController@testSettingMail');
            });
        });
    });
    Route::group(['prefix' => 'blogs'], function () {
        Route::get('/tags', 'BlogController@getByTags');
        Route::get('/newest', 'BlogController@getNewest');
        Route::get('/popular', 'BlogController@getPopular');
        Route::get('/related/{id}', 'BlogController@getRelatedBlogs');
        Route::get('/category/{id}', 'BlogController@getBlogsByCategory');
        Route::get('/category/slug/{slug}', 'BlogController@getBlogsByCategorySlug');
        Route::get('/slug/{slug}', 'BlogController@getBlogBySlug');
        Route::post('/update/view-count/{id}', 'BlogController@updateViewCount');
        Route::post('/update/share-count', 'BlogController@updateShareCount');
        Route::get('/', 'BlogController@getAll');
        Route::get('/{id}', 'BlogController@getBlogById');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'CategoryController@getAll');
        Route::get('/slug/{slug}', 'CategoryController@getCategoryBySlug');
        Route::get('/slug/{slug}/childrens', 'CategoryController@getCategoryByParentSlug');
        Route::get('/{id}', 'CategoryController@getCategoryById');
        Route::get('/{id}/childrens', 'CategoryController@getCategoriesByParentId');
    });

    Route::group(['prefix' => 'ingredients'], function () {
        Route::get('/slug/{slug}', 'IngredientController@getIngredientBySlug');
        Route::get('/getAll', 'IngredientController@getAllWithoutPagination');
        Route::get('/', 'IngredientController@getAll');
        Route::get('/{id}', 'IngredientController@getIngredientById');
    });
    Route::get('/search', 'SearchController@search');

    Route::post('/contact/portfolio', 'ContactController@createContactPortfolio');
    Route::post('/contact', 'ContactController@createContact');
    Route::post('/subscribes', 'SubscriberController@subscribe');
});