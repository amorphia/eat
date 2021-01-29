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
    Route::get('/user', function (Request $request) { return $request->user(); });

    /*
     *  Restaurants
     */
    Route::get('/restaurants', [App\Http\Controllers\RestaurantController::class, 'index'] );

    /*
     *  Ratings
     */
    Route::patch('/ratings/{restaurant}', [App\Http\Controllers\RatingController::class, 'update'] );

    /*
     *  Photos
     */
    Route::patch('/photos/{photo}', [App\Http\Controllers\PhotoController::class, 'update'] );
    Route::delete('/photos/{photo}', [App\Http\Controllers\PhotoController::class, 'destroy'] );

    /*
    *  Posts
    */
    Route::post('/posts', [App\Http\Controllers\PostController::class, 'store'] );
    Route::delete('/posts/{post}', [App\Http\Controllers\PostController::class, 'destroy'] );
    Route::patch('/posts/{post}', [App\Http\Controllers\PostController::class, 'update'] );
});


