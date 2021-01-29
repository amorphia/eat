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

        return Restaurant::with([
            'locations',
            'photos.user',
            'categories',
            'posts' => function ( $q ) use ( $request ) {
                return $q->where( 'user_id', $request->user()->id );
            }])
            ->leftJoin( 'ratings', function( $join ) use( $request ) {
                $join->on( 'restaurants.id', '=', 'ratings.restaurant_id' )
                    ->where( 'ratings.user_id', '=', $request->user()->id );
            })
            ->select('restaurants.*',
                DB::raw('coalesce( ratings.rating, 0) as rating'),
                DB::raw('coalesce( ratings.interest, 0) as interest')
            )
            ->orderBy( 'rating', 'desc' )
            ->orderBy( 'interest', 'desc' )
            ->orderBy( 'name', 'asc' )
            ->where( 'active', true )
            ->where( 'interest', '!=', -1 )
            ->orWhereNull( 'interest' )
            ->take( 102 )
            ->get();
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
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restaurant $restaurant)
    {
        //
    }
}
