<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Location;

class YelpService
{

    protected $client;
    protected $limit = 50; // how many results to fetch at once from Yelp API (max 50)
    protected $max_results = 1000; // Maximum number of results to collect (max 1000)
    protected $sleep = 2;
    protected $errors = 0;
    protected $maxErrors = 10;
    protected $errorTimeout = 10;

    public function __construct()
    {
        $this->client = resolve( 'Yelp' );
    }


    public function details( $location )
    {
        // if the supplied argument isn't a location object, or an int return false
        if( !is_a( $location, Location::class ) && !is_int( $location ) ){
            return false;
        }

        // if we have an int get the location model, otherwise return
        if( is_int( $location ) ) $location = Location::find( $location );
        if( !$location ) return false;

        // return the resulting details
        return $this->client->getBusiness( $location->yelp_id );
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

        $results = null;

        try
        {
            $results = $this->client->getBusinessesSearchResults( $parameters );
        }
        catch ( \Throwable $e )
        {
            if( $this->console ) $this->console->error( 'Failed to scan for details: ' . $e->getMessage() );

            $this->errors++;
            if( $this->errors >= $this->maxErrors ){
                if( $this->console ) $this->console->error( 'Too many errors, aborting' );
                die();
            } else {
                sleep( $this->errorTimeout );
            }
        }


        return $results;
    }

    public function search( $zip, $options = [] )
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
            // sleep for a bit to avoid rate limiting
            if( $continue ){
                $offset += $this->limit;

                // set our sleep time to either 1 or 2 seconds depending on our supplied options
                $sleepTime = $options['slow'] ? $this->sleep * 2 : $this->sleep;
                sleep( $sleepTime );
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
