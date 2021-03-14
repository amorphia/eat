<?php


namespace App\QueryFilters;

use Closure;
use \Illuminate\Database\Eloquent\Builder;

abstract class QueryFilterAbstract
{
    /**
     * Handle this query filter
     *
     * @param $request
     * @param Closure $next
     * @return Closure
     */
    public function handle( $request, Closure $next )
    {
        if( !$this->shouldProcess() ){
            return $next( $request );
        }

        $query = $next( $request );
        return $this->process( $query );
    }

    /**
     * Test to see if we should run this filter
     *
     * @return boolean
     */
    protected abstract function shouldProcess();

    /**
     * Run the filter
     *
     * @param $query
     * @return Closure;
     */
    protected abstract function process( Builder $query );

}
