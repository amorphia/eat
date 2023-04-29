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
    Route::patch('/tour', [App\Http\Controllers\TourController::class, 'update'] )->name( 'tour.update' );

    // Categories
    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'] )->name( 'categories.index' );;

    // Restaurants
    Route::resource('restaurants', App\Http\Controllers\RestaurantController::class)->only([ 'index', 'show', 'update' ]);
    Route::post('/restaurants/search', [App\Http\Controllers\RestaurantController::class, 'search'] )->name( 'restaurants.search' );
    Route::post('/restaurants/merge', [App\Http\Controllers\RestaurantController::class, 'merge'] )->name( 'restaurants.merge' );

    //Photos
    Route::resource('photos', App\Http\Controllers\PhotoController::class)->only([ 'store', 'destroy', 'update' ]);
    Route::get('/photos/reload/{restaurant}', [App\Http\Controllers\PhotoController::class, 'reload'] )->name( 'photos.reload' );
    Route::post('/photos/yelp', [App\Http\Controllers\PhotoController::class, 'yelp'] )->name( 'photos.yelp' );

    // Posts (I should rename this notes, as that's the wording I use in the UI)
    Route::resource('posts', App\Http\Controllers\PostController::class)->only([ 'store', 'destroy', 'update' ]);

    // Locations
    Route::delete('/locations/{location}', [App\Http\Controllers\LocationController::class, 'destroy'] )->name( 'locations.destroy' );
    Route::post('/locations/create/yelp/{mode}', App\Http\Controllers\Actions\CreateLocationFromYelpController::class )->name( 'locations.createFromYelp' );
});


// Ratings throttle group allows substantially more requests per minute
Route::middleware(['auth:sanctum', 'throttle:ratings'])->group(function () {

    // Ratings
    Route::patch('/ratings/{restaurant}', [App\Http\Controllers\RatingController::class, 'update'] )->name( 'ratings.update' );

});
