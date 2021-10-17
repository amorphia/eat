<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\YelpServiceInterface;
use Illuminate\Http\Request;

class LocationController extends Controller
{

    /**
     * Our Yelp API Service class
     *
     * @var YelpServiceInterface
     */
    protected $yelp;


    public function __construct( YelpServiceInterface $yelpService )
    {
        $this->yelp = $yelpService;
    }


    /*
     *  Create a new location from a yelp id
     *
     *  @param  \Illuminate\Http\Request  $request
     */
    public function createByYelpId( Request $request ) : void
    {
        $validated = $request->validate([
            'yelp_id' => 'string'
        ]);

        // add yelp location by ID
        Location::addByYelpId( $validated['yelp_id'], $this->yelp );
    }


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
