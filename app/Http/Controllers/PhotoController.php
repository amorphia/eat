<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Photo;
use App\Models\Restaurant;
use Illuminate\Http\Request;


class PhotoController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ImagickException
     */
    public function store( Request $request )
    {
        $validated = $request->validate([
            'restaurant_id' => 'exists:restaurants,id',
            'body' => 'string|nullable',
            'image' => 'image|nullable|required_without:url',
            'url' => 'string|nullable|required_without:image'
        ]);

        // store our photo
        $photo = Photo::storePhoto( $request, $validated );

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
