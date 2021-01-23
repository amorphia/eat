<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;

class YelpService
{

    protected $client;
    protected $limit = 50; // how many results to fetch at once from Yelp API (max 50)
    protected $max_results = 1000; // Maximum number of results to collect (max 1000)


    public function __construct()
    {
        $this->client = resolve( 'Yelp' );
    }


    public function searchYelp( $zip, $offset = 0 )
    {
        $parameters = [
            'location' => $zip,
            'limit' => $this->limit,
            'offset' => $offset,
            'categories' => 'restaurants',
            'sort_by' => 'distance'
        ];

        return $this->client->getBusinessesSearchResults( $parameters );
    }

    public function search( $zip )
    {
        $businesses = [];
        $offset = 0;
        $continue = true;

        while( $continue === true ){
            $continue = false;

            // hit the yelp api
            $results = $this->searchYelp( $zip, $offset );

            if( $this->hasResults( $results ) ){
                // if we have results add them to our collection
                $businesses =  array_merge ( $businesses, $results->businesses );
                // then check if we need to do another search
                $continue = $this->hasMoreResults( $results, $businesses );
            }

            // if we are continuing increment our offset and then
            // sleep for a second to avoid rate limiting
            if( $continue ){
                $offset += $this->limit;
                sleep( 1 );
            }
        }

        return $businesses;
    }

    /*
     *  Checks if we need to continue searching for more results;
     */
    protected function hasMoreResults( $results, $businesses )
    {
        // if the last search returned results equal to the search limit,
        // and we haven't hit our max limit yet
        return  count( $results->businesses ) === $this->limit
                && count( $businesses ) < $this->max_results;
    }


    /*
    *  Checks if we received at least one business result from our search
    */
    protected function hasResults( $results )
    {
        // Do we have results?
        return  $results
                && isset( $results->businesses )
                && count( $results->businesses ) > 0;
    }


}
