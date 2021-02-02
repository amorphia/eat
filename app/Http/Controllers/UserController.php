<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{

    public function index( Request $request )
    {
        return User::where( 'id', '!=', user()->id )
                    ->orderBy( 'name', 'asc' )
                    ->get();
    }


    public function show( Request $request )
    {
        user()->load( 'blocked' );
        return user();
    }
}
