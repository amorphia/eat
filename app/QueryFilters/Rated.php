<?php


namespace App\QueryFilters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class Rated extends QueryFilterAbstract
{

    /**
     * Test to see if we should run this filter
     *
     * @return boolean
     */
    protected function shouldProcess()
    {
        return  request()->has( 'rated' ) && !request()->has( 'match' );
    }

    /**
     * Run the filter
     *
     * @param Builder $query
     * @return Builder;
     */
    protected function process( Builder $query )
    {
        $filter = request()->rated;

        switch( $filter ) {
            case 'rated': return $this->setRatedFilter( $query );
            case 'interested': return $this->setInterestedFilter( $query );
            case 'unviewed': return $this->setUnviewedFilter( $query );
            case 'unrated': return $this->setUnratedFilter( $query );
            default: return $query;
        }
    }

    /**
     * if we are filtering by rated our rating has to be equal to non-zero
     *
     * @param Builder $query
     * @return Builder
     */
    protected function setRatedFilter( Builder $query ){
        return $query->where( 'rating', '!=', 0 );
    }


    /**
     * if we are filtering by interested our interest has to be equal to non-zero
     *
     * @param Builder $query
     * @return Builder
     */
    protected function setInterestedFilter( Builder $query ){
        return $query->where( 'interest', '!=', 0 );
    }


    /**
     * if we are filtering unviewed then allow only null or viewed 0
     *
     * @param Builder $query
     * @return Builder
     */
    protected function setUnviewedFilter( Builder $query ){
        return $query->where(function ($query) {
                    $query->where('viewed', 0)
                        ->orWhereNull('rating');
                });
    }


    /**
     * if we are filtering by unrated only then allow ratings of 0 or null
     *
     * @param Builder $query
     * @return Builder
     */
    protected function setUnratedFilter( Builder $query ){
        return $query->where(function ($query) {
                    $query->where('rating', 0)
                        ->orWhereNull('rating');
                });
    }

}
