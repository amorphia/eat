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


    public function tour( Request $request, User $user )
    {
        if( user()->id !== $user->id ) return error();
        $user->tour = true;
        $user->save();
        return $user;
    }

    public function token( Request $request )
    {
        $token = $request->user()->createToken( $request->token_name );
        return ['token' => $token->plainTextToken];
    }

}
