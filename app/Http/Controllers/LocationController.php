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
    public function createByYelpId( Request $request )
    {
        $validated = $request->validate([
            'yelp_id' => 'string'
        ]);

        // add yelp location by ID
        Location::addByYelpId( $validated['yelp_id'], $this->yelp );
    }


    /*
     *  Create a new location from a yelp url
     *
     *  @param  \Illuminate\Http\Request  $request
     */
    public function createByYelpPage( Request $request )
    {
        // remove parameters from the url and then check if its already in our DB
        $url = stripUrlParams( $request->yelp_url );
        $count = Location::where( 'yelp_url', stripUrlParams( $url ) )->count();

        // if this url already exists in our DB then abort
        if( $count > 0 ) return error( 'This location already exists' );

        // add yelp location
        return Location::addByYelpPage( $url, $this->yelp );
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy( Location $location )
    {
        if( !user()->can( 'update', $location) ) return error();

        // deactivate
        $location->close();
    }
}
