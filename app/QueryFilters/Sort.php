<?php


namespace App\QueryFilters;

use Closure;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Sort extends QueryFilterAbstract
{

    /**
     * All of the valid sort types, and their default direction (if applicable)
     *
     * @var array
     */
    protected $sorts = [
        'interest' => 'desc',
        'rating' => 'desc',
        'name' => 'asc',
        'created_at' => 'desc',
        'distance' => null,
    ];


    /**
     * Test to see if we should run this filter
     *
     * @return boolean
     */
    protected function shouldProcess()
    {
        return true;
    }


    /**
     * Run the filter
     *
     * @param Builder $query
     * @return Builder;
     */
    protected function process( Builder $query )
    {
        // all match searches are sorted by combined rating
        if( request()->match ) return $query->orderBy( 'combined_rating', 'desc' );

        // set sort type to the one provided, or to the first default sort if none is provided
        $sort = request()->sort ?? array_keys( $this->sorts )[0];


        // apply our primary sort
        if( $sort === 'distance' ) {
            $query = $this->sortByDistance( $query );
        } else {
            // set sort direction
            $direction = request()->direction ?? $this->sorts[$sort];

            // apply sort
            if( array_key_exists( $sort, $this->sorts ) ) $query->orderBy( $sort, $direction );
        }


        // set secondary orderBy clauses
        foreach ( $this->sorts as $key => $direction ){
            if( $sort !== $key && $direction !== null ) $query->orderBy( $key, $direction );
        }

        return $query;
    }


    /**
     * Sort by location distance
     *
     * @param Builder $query
     * @return Builder
     */
    public function sortByDistance( $query )
    {
        $latitude = request()->latitude;
        $longitude = request()->longitude;

        if( !$latitude || !$longitude ) return $query;

        return $query->rightJoin( 'locations', function( $join ) {
                $join->on( 'restaurants.id', '=', 'locations.restaurant_id' );
            })
            ->distance( $latitude, $longitude, ['table' => 'locations'] )
            ->addSelect( 'locations.yelp_id' )
            ->whereNotNull( 'latitude' )
            ->whereNotNull( 'longitude' )
            ->orderBy( 'distance', request()->direction ?? 'asc' );
    }

}
