<?php


namespace App\QueryFilters;

use Closure;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Matches extends QueryFilterAbstract
{

    /**
     * The minimum rating to allow for each user when determining restaurant matches
     *
     * @var int
     */
    protected $min_rating = 4;

    /**
     * The ID of the User model we are matching with
     *
     * @var integer
     */
    protected $match_id;

    /**
     * Test to see if we should run this filter
     *
     * @return boolean
     */
    protected function shouldProcess()
    {
        return  request()->has( 'match' );
    }

    /**
     * Run the filter
     *
     * @param Builder $query
     * @return Builder;
     */
    protected function process( Builder $query )
    {

        $query->leftJoin( 'ratings AS match', function( $join ) {
                    $join->on( 'restaurants.id', '=', 'match.restaurant_id' )
                        ->where( 'match.user_id', '=', $this->getMatchId() );
                });

        return $this->setSelects( $query );

    }


    /**
     * Get and cache the ID of the User model we are matching with
     *
     * @return int
     */
    protected function getMatchId()
    {
        // if we have already cached the match ID just return the cached version
        if( isset( $this->match_id ) ) return $this->match_id;

        // fetch our matched User model
        $match = User::where( 'uuid', request()->match )->first();

        // cache our matched User's ID
        $this->match_id = $match->id;

        return $this->match_id;
    }


    /**
     * Set the additional 'SELECT' statements for the query when matching
     *
     * @param Builder $query
     * @return Builder
     */
    protected function setSelects( Builder $query )
    {

        // add the base select statements we will need for all match types
        $query->addSelect(
            DB::raw('coalesce( match.rating, 0) as match_rating'),
            DB::raw('coalesce( match.interest, 0) as match_interest')
        );

        // then add any additional select statements we will need for each specific match type
        switch( request()->type ){
            case "interest": return $this->setInterestSelects( $query );
            case "ratings": return $this->setRatingsSelects( $query );
            case "interest-overlap": return $this->setInterestOverlapSelects( $query );
            case "ratings-overlap": return $this->setRatingsOverlapSelects( $query );
            default: return $this->setCombinedOverlapSelects( $query );
        }

    }

    /**
     * Set the additional 'SELECT' statements for the match type "interest"
     *
     * @param Builder $query
     * @return Builder
     */
    protected function setInterestSelects( Builder $query )
    {
        return $query->addSelect(
                    DB::raw('coalesce( (match.interest * 5), 0) as combined_rating'),
                )->where( 'match.interest', '>=', 1 );
    }


    /**
     * Set the additional 'SELECT' statements for the match type "ratings"
     *
     * @param Builder $query
     * @return Builder
     */
    protected function setRatingsSelects( Builder $query )
    {
        return $query->addSelect(
                    DB::raw('coalesce( match.rating, 0) as combined_rating'),
                )->where( 'match.rating', '>=', 1 );
    }


    /**
     * Set the additional 'SELECT' statements for the match type "interest-overlap"
     *
     * @param Builder $query
     * @return Builder
     */
    protected function setInterestOverlapSelects( Builder $query )
    {
        return  $query->addSelect(
                    DB::raw('coalesce( (match.interest * 5) + (ratings.interest * 5), 0) as combined_rating'),
                )->where( function($query) {
                    $query->where( 'match.interest', '>=', 1 )
                        ->where( 'ratings.interest', '>=', 1 ) ;
                });
    }

    /**
     * Set the additional 'SELECT' statements for the match type "ratings-overlap"
     *
     * @param Builder $query
     * @return Builder
     */
    protected function setRatingsOverlapSelects( Builder $query )
    {
        return $query->addSelect(
                    DB::raw('coalesce( match.rating + ratings.rating, 0) as combined_rating'),
                )->where( function($query) {
                    $query->where( 'match.rating', '>=', $this->min_rating  )
                    ->where( 'ratings.rating', '>=', $this->min_rating ) ;
                });
    }


    /**
     * Set the additional 'SELECT' statements for the default match type of "combined-overlap"
     *
     * @param Builder $query
     * @return Builder
     */
    protected function setCombinedOverlapSelects( Builder $query )
    {
        return $query->addSelect(
                    DB::raw('coalesce( (match.interest * 5) + (ratings.interest * 5) + match.rating + ratings.rating, 0) as combined_rating'),
                )->where( function($query) {
                    $query->where( function( $query ){
                    $query->where( 'match.rating', '>=', $this->min_rating  )
                        ->orWhere( 'match.interest', '>=', 1 );
                })->where( function( $query ){
                    $query->where( 'ratings.rating', '>=', $this->min_rating )
                        ->orWhere( 'ratings.interest', '>=', 1 );
                }) ;
            });
    }

}
