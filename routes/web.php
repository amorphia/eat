<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Location;

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


// Login/Register welcome page for unauthenticated Users
Route::get( 'welcome', function () { return view( 'welcome' ); })
    ->middleware('guest')
    ->name( 'welcome' );


// Routes for authentication
Auth::routes();
Route::get( 'logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'] );


// Outside of the landing and authentication, all other routes should just point to the SPA
Route::get('/{any}', function () { return view( 'app' ); })
    ->where('any', '.*' )
    ->middleware( 'auth' )
    ->name( 'home' );


