<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Location;

class YelpService implements YelpServiceInterface
{

    /**
     * Options
     */

    // how many results to fetch at once from Yelp API (max 50)
    protected $limit = 50;

    // Maximum number of results to collect (max 1000)
    protected $max_results = 1000;

    // radius in meters of search area, max 40,000 (about 25 miles)
    protected $radius = 40000;

    // the number of seconds to sleep between api calls to avoid getting throttled
    protected $sleep = 2;

    // how many failed API calls we've made this session
    protected $errors = 0;

    // the maximum allowable failed API calls per session before we abort
    protected $maxErrors = 10;

    // time in seconds to wait after each failed API call
    protected $errorTimeout = 10;


    /*
     * Internals
     */

    // an optional console object to output to
    protected $console;

    // our yelp client
    protected $client;


    public function __construct()
    {
        $this->client = resolve( 'Yelp' );

        // reduce max results when testing outside of production
        if( config( 'app.env' ) !== 'production' ) $this->max_results = 50;

    }


    /**
     * Ask our yelp api client for a location's details when provided a location model (/App/Models/Location),
     * the id for a location model (int), or a yelp_id (string)
     *
     * @param mixed (int|string|/App/Models/Location) $location
     * @return mixed
     */
    public function details( $location )
    {
        if( !is_string( $location ) ){
            // if the supplied argument isn't a location object, or an int return false
            if( !is_a( $location, Location::class ) && !is_int( $location ) ){
                return false;
            }

            // if we have an int get the location model, otherwise return
            if( is_int( $location ) ) $location = Location::find( $location );
            if( !$location ) return false;

            $location = $location->yelp_id;
        }

        // return the resulting details
        return $this->client->getBusiness( $location );
    }



    /**
     * Perform a search of a given zip code, calling the yelp API and collecting the results
     * until we reach our pagination limit for this search
     *
     * @param $zip // the zip code to center
     * @param array $options
     *      [slow] boolean // slow down the search if I'm worried about hitting a yelp API throttle
     *      [...] additional options may be passed to the searchYelp method via this array as well
     * @return array
     */
    public function search( $zip, $options = [] )
    {
        $businesses = [];
        $offset = 0;
        $continue = true;

        while( $continue === true ){
            // assume this is our last loop, unless we later decide we qualify for more results
            $continue = false;

            // hit the yelp api
            $results = $this->searchYelp( $zip, $offset, $options );

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


    /**
     * Call our Yelp API client to Search restaurants centered on a given zip
     *
     * @param $zip // The zip code to center our search on
     * @param $offset // Used for tracking current pagination
     * @param array|null $options
     *      [sort] string // Sort type
     *      [attributes] string // Any additional search attributes you'd like to include
     * @return array|null // Search results
     */
    public function searchYelp( $zip, $offset = 0, $options = [] )
    {
        $parameters = [
            'location' => $zip,
            'limit' => $this->limit,
            'offset' => $offset,
            'categories' => 'restaurants',
            'radius' => $this->radius,
            'sort_by' => $options['sort'] ?? 'distance'
        ];

        // if we have any attributes to add, set them here
        if( isset( $options['attributes'] ) ){
            $parameters['attributes'] = $options['attributes'];
        }

        $results = null;

        try {
            $results = $this->client->getBusinessesSearchResults( $parameters );
        } catch ( \Throwable $e ) {
            if( $this->console ) $this->console->error( 'Failed to scan for details: ' . $e->getMessage() );

            // increment our errors
            $this->errors++;

            // if we've hit our may errors for this run about mission
            if( $this->errors >= $this->maxErrors ){
                if( $this->console ) $this->console->error( 'Too many errors, aborting' );
                die();
            } else { // otherwise just sleep for a bit and keep going
                sleep( $this->errorTimeout );
            }
        }

        return $results;
    }


    /**
     * Checks if we need to continue searching for more results;
     *
     * @param $results // results from our most recent API call
     * @param $businesses // total results collected this session
     * @return bool
     */
    protected function hasMoreResults( $results, $businesses )
    {
        // if the last search returned results equal to the search limit then we probably still have more to get,
        // so long as we haven't hit our max limit yet
        return  count( $results->businesses ) === $this->limit
                && count( $businesses ) < $this->max_results;
    }


    /**
     * Checks if we received at least one business result from our search
     *
     * @param $results // results from an API call
     * @return bool
     */
    protected function hasResults( $results )
    {
        // Do we have results?
        return  $results
                && isset( $results->businesses )
                && count( $results->businesses ) > 0;
    }


}
