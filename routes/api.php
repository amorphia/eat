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

    // Users
    Route::resource('users', App\Http\Controllers\UserController::class)->only([ 'show', 'index' ]);

    // Update user to flag if they have watched a specific tour
    Route::patch('/tour', [App\Http\Controllers\TourController::class, 'update'] );

    // Categories
    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'] );

    // Restaurants
    Route::resource('restaurants', App\Http\Controllers\RestaurantController::class)->only([ 'index', 'show', 'update' ]);
    Route::post('/restaurants/search', [App\Http\Controllers\RestaurantController::class, 'search'] );
    Route::post('/restaurants/merge', [App\Http\Controllers\RestaurantController::class, 'merge'] );

    //Photos
    Route::resource('photos', App\Http\Controllers\PhotoController::class)->only([ 'store', 'destroy', 'update' ]);
    Route::post('/photos/yelp', [App\Http\Controllers\PhotoController::class, 'yelp'] );

    // Posts (I should rename this notes, as that's the wording I use in the UI)
    Route::resource('posts', App\Http\Controllers\PostController::class)->only([ 'store', 'destroy', 'update' ]);

    // Locations
    Route::delete('/locations/{location}', [App\Http\Controllers\LocationController::class, 'destroy'] );
    Route::post('/locations/create/yelp/page', [App\Http\Controllers\LocationController::class, 'createByYelpPage'] );
    Route::post('/locations/create/yelp/id', [App\Http\Controllers\LocationController::class, 'createByYelpId'] ); // for use by chrome extension

});


// Ratings throttle group allows substantially more requests per minute
Route::middleware(['auth:sanctum', 'throttle:ratings'])->group(function () {

    // Ratings
    Route::patch('/ratings/{restaurant}', [App\Http\Controllers\RatingController::class, 'update'] );

});
