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

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    /*
     *  Users
     */
    Route::get('/user', [App\Http\Controllers\UserController::class, 'show'] );
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'] );
    Route::get('/user/{user}/tour', [App\Http\Controllers\UserController::class, 'tour'] );

    /*
     *  Categories
     */

    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'] );


    /*
     *  Restaurants
     */
    Route::get('/restaurants', [App\Http\Controllers\RestaurantController::class, 'index'] );
    Route::get('/restaurants/{restaurant}', [App\Http\Controllers\RestaurantController::class, 'show'] );
    Route::post('/restaurants/merge', [App\Http\Controllers\RestaurantController::class, 'merge'] );
    Route::post('/restaurants/delete', [App\Http\Controllers\RestaurantController::class, 'destroy'] );
    Route::patch('/restaurants/{restaurant}', [App\Http\Controllers\RestaurantController::class, 'update'] );
    Route::post('/restaurants/search', [App\Http\Controllers\RestaurantController::class, 'search'] );


    /*
     *  Photos
     */
    Route::post('/photos', [App\Http\Controllers\PhotoController::class, 'store'] );
    Route::post('/photos/yelp', [App\Http\Controllers\PhotoController::class, 'yelp'] );
    Route::patch('/photos/{photo}', [App\Http\Controllers\PhotoController::class, 'update'] );
    Route::delete('/photos/{photo}', [App\Http\Controllers\PhotoController::class, 'destroy'] );

    /*
    *  Posts
    */
    Route::post('/posts', [App\Http\Controllers\PostController::class, 'store'] );
    Route::delete('/posts/{post}', [App\Http\Controllers\PostController::class, 'destroy'] );
    Route::patch('/posts/{post}', [App\Http\Controllers\PostController::class, 'update'] );

    /*
    *  Locations
    */
    Route::delete('/locations/{location}', [App\Http\Controllers\LocationController::class, 'destroy'] );
    Route::post('/locations/yelp/id', [App\Http\Controllers\LocationController::class, 'yelpId'] );
    Route::post('/locations/yelp/page', [App\Http\Controllers\LocationController::class, 'yelpPage'] );
});


Route::middleware(['auth:sanctum', 'throttle:ratings'])->group(function () {
        /*
       *  Ratings
       */
    Route::patch('/ratings/{restaurant}', [App\Http\Controllers\RatingController::class, 'update'] )->middleware('throttle:ratings');
});
