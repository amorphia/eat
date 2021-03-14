<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Restaurant;
use App\Services\PipelineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{

    /**
     * Return an index of restaurants.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request, PipelineService $pipelineService )
    {
        return Restaurant::index( $pipelineService )->paginate( 102 );
    }


    /**
     * Return restaurant search results
     *
     * @param Request $request
     * @return mixed
     */
    public function search( Request $request )
    {
        if( ! $request->searchTerm ) return error( 'No Search Term' );

        // get restaurants matching our search results
        $restaurants = Restaurant::search( $request->searchTerm )->get();

        // if we have any results return them, otherwise just return a dummy "no results" entry
        return $restaurants->count() ? $restaurants : [[ "name" => "No Results" ]];
    }


    /**
     * Display the specified restaurant.
     *
     * @param  \App\Models\Restaurant $restaurant
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request )
    {
        return Restaurant::active()
                    ->withRelations()
                    ->joinRatings()
                    ->find( $request->restaurant );
    }


    /**
     * Update the restaurant in storage.
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


    /**
     * Merge two or more restaurants into one entry
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function merge( Request $request )
    {
        if( !user()->can( 'update', Restaurant::class ) ) return error();

        $validated = $request->validate([
            'ids.*' => 'exists:restaurants,id'
        ]);

        $ids = Restaurant::mergeRestaurants( $validated );
        return response()->json([ 'ids' => $ids ]);
    }


    /**
     * Set the given restaurants to inactive.
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

        // get restaurant models and set them to inactive
        Restaurant::whereIn( 'id', $validated['ids'] )->update([ 'active' => false ]);
        return response()->json([ 'ids' => $validated['ids'] ]);
    }

}
