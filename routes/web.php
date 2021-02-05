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


/*
 * Routes for authentication and logging out
 */
Auth::routes();
Route::get( 'logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'] );

// get API token
Route::get('/tokens/create', [App\Http\Controllers\UserController::class, 'token'] )->middleware( 'auth' );

Route::get( 'welcome', function () {
    return view( 'welcome' );
} )->name( 'welcome' );

/*
 * Other than authentication, all other routes should point to the SPA
 */


Route::get('/{any}', function () {
    return view( 'app' );
})->where('any', '.*' )->middleware( 'auth' )->name( 'home' );


