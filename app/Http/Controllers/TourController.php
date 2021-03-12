<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TourController extends Controller
{

    /**
     * Mark a given tour as having been viewed for the currently logged in user
     *
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request )
    {
        // check that the given tour is in our list of acceptable tours
        $request->validate([
           'tour' =>  Rule::in( config( 'app.tours' ) ),
        ]);

        // flag the given tour for the currently logged in user as watched
        user()->update([ $request->tour => true ]);
        return user();
    }
}
