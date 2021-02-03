<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Photo;
use App\Models\Restaurant;
use Illuminate\Http\Request;


class PhotoController extends Controller
{
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
    public function store( Request $request )
    {
        $validated = $request->validate([
            'restaurant_id' => 'exists:restaurants,id',
            'body' => 'string|nullable',
            'image' => 'image|nullable|required_without:url',
            'url' => 'string|nullable|required_without:image'
        ]);

        // save image if file supplied rather than url
        if( $validated['image'] ){
            $validated['url'] = Photo::saveUpload( $request );
        }

        // get parent restaurant
        $restaurant = Restaurant::where( 'id', $validated['restaurant_id'] )->first();

        // store in db
        $photo = $restaurant->photos()->create([
            'url' => $validated['url'],
            'body' => $validated['body'],
            'user_id' => user()->id
        ]);

        // return model
        return $photo;
    }



    public function yelp( Request $request )
    {
        $validated = $request->validate([
            'url' => 'exists:locations,yelp_url',
            'photo' => 'string',
            'body' => 'string|nullable'
        ]);

        // get the appropriate location
        $location = Location::where( 'yelp_url', $validated['url'] )->first();

        // store in db
        $photo = $location->restaurant->photos()->create([
            'url' => $validated['photo'],
            'body' => $validated['body'],
            'user_id' => user()->id
        ]);

        // return model
        return $photo;
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function show(Photo $photo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function edit(Photo $photo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, Photo $photo )
    {
        if( !user()->can( 'update', $photo ) ) return error();

        $validated = $request->validate([
            'url' => 'string',
            'body' => 'string|nullable',
            'priority' => 'integer',
        ]);

        // update rating with new values then return
        return $photo->update( $validated );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Photo $photo)
    {
        if( !user()->can( 'delete', $photo ) ) return error();

        return $photo->update([ 'active' => false ]);
    }
}
