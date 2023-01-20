<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{


    /**
     * Display listing of all users
     *
     * @return User[]
     */
    public function index( Request $request )
    {
        return User::where( 'id', '!=', user()->id )
                    ->orderBy( 'name', 'asc' )
                    ->get();
    }


    /**
     * Display the currently logged in user
     *
     * @return User
     */
    public function show( Request $request )
    {
        user()->load( 'blocked' );
        return user();
    }

}
