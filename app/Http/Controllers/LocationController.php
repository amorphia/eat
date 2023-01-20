<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function destroy( Location $location )
    {
        if( !user()->can( 'update', $location) ) return error();

        // deactivate
        $location->close();
    }
}
