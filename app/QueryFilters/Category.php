<?php


namespace App\QueryFilters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class Category extends QueryFilterAbstract
{

    /**
     * Test to see if we should run this filter
     *
     * @return boolean
     */
    protected function shouldProcess()
    {
        return request()->category && request()->category !== 'all' && !request()->has( 'match' );
    }


    /**
     * Run the filter
     *
     * @param $query
     * @return Builder;
     */
    protected function process( $query )
    {
        $category = request()->category;

        // otherwise filter by relation with chosen category
        return $query->whereHas( 'categories', function( $q ) use ( $category ){
            $q->where( 'name', $category );
        });
    }


}
