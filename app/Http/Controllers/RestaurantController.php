<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        return Restaurant::index()->paginate( 102 );
    }



    public function search( Request $request )
    {
        if( ! $request->searchTerm ) return;

        $restaurants = Restaurant::search( $request->searchTerm )->get();

        if( $restaurants->count() ){
            return $restaurants;
        }

        return $restaurants->count() ? $restaurants : [[ "name" => "No Results" ]];
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function show(Restaurant $restaurant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function edit(Restaurant $restaurant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        if( !user()->can( 'update', Restaurant::class ) ) return error();

        $validated = $request->validate([
            'name' => 'string',
            'active' => 'boolean'
        ]);

        $restaurant->update( $validated );

    }


    public function merge( Request $request )
    {
        if( !user()->can( 'update', Restaurant::class ) ) return error();

        $validated = $request->validate([
            'ids.*' => 'exists:restaurants,id'
        ]);

        $restaurants = Restaurant::whereIn( 'id', $validated['ids'] )->get();

        // take the first restaurant as the anchor to merge the rest into, then merge
        // the other's relations to it
        $anchor = $restaurants->first();
        $restaurants->each->merge( $anchor->id );

        // grab the restaurant ids, and remove the anchor ID to make a list of deleted restaurants
        $ids = collect( $validated['ids'] )->filter( function ( $val, $key ) use ( $anchor ) {
            return $val !== $anchor->id;
        })->toArray();

        return response()->json([ 'ids' => $ids ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request )
    {
        if( !user()->can( 'update', Restaurant::class ) ) return error();

        $validated = $request->validate([
            'ids.*' => 'exists:restaurants,id'
        ]);

        // get restaurant models
        Restaurant::whereIn( 'id', $validated['ids'] )->update([ 'active' => false ]);

        return response()->json([ 'ids' => $validated['ids'] ]);

    }
}
