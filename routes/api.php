<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum'])->group(function () {

    /*
     *  Users
     */
    Route::get('/user', function (Request $request) {
        user()->load( 'blocked' );
        return user();
    });


    /*
     *  Categories
     */

    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'] );


    /*
     *  Restaurants
     */
    Route::get('/restaurants', [App\Http\Controllers\RestaurantController::class, 'index'] );
    Route::post('/restaurants/merge', [App\Http\Controllers\RestaurantController::class, 'merge'] );
    Route::post('/restaurants/delete', [App\Http\Controllers\RestaurantController::class, 'destroy'] );
    Route::patch('/restaurants/{restaurant}', [App\Http\Controllers\RestaurantController::class, 'update'] );
    Route::post('/restaurants/search', [App\Http\Controllers\RestaurantController::class, 'search'] );

    /*
     *  Ratings
     */
    Route::patch('/ratings/{restaurant}', [App\Http\Controllers\RatingController::class, 'update'] );

    /*
     *  Photos
     */
    Route::post('/photos', [App\Http\Controllers\PhotoController::class, 'store'] );
    Route::patch('/photos/{photo}', [App\Http\Controllers\PhotoController::class, 'update'] );
    Route::delete('/photos/{photo}', [App\Http\Controllers\PhotoController::class, 'destroy'] );

    /*
    *  Posts
    */
    Route::post('/posts', [App\Http\Controllers\PostController::class, 'store'] );
    Route::delete('/posts/{post}', [App\Http\Controllers\PostController::class, 'destroy'] );
    Route::patch('/posts/{post}', [App\Http\Controllers\PostController::class, 'update'] );
});


