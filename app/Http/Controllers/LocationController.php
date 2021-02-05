<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\YelpService;
use Illuminate\Http\Request;

class LocationController extends Controller
{

    protected $yelp;

    public function __construct( YelpService $yelpService )
    {
        $this->yelp = $yelpService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }


    /*
     *
     *  Create a new location from a yelp id
     *
     *  @param  \Illuminate\Http\Request  $request
     */
    public function yelpId(Request $request)
    {
        $validated = $request->validate([
            'yelp_id' => 'string'
        ]);

        Location::addByYelpId( $validated['yelp_id'], $this->yelp );
    }

    /*
     *
     *  Create a new location from a yelp id
     *
     *  @param  \Illuminate\Http\Request  $request
     */
    public function yelpPage( Request $request )
    {
        $url = stripUrlParams( $request->yelp_url );
        $count = Location::where( 'yelp_url', stripUrlParams( $url ) )->count();

        if( $count > 0 ) return error( 'This location already exists' );

        return Location::addByYelpPage( $url, $this->yelp );
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location)
    {
        //
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
