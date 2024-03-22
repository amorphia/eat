<?php


namespace App\Services;

use App\Models\Zip;
use App\Models\Category;
use App\Models\Location;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ScanSummary;
use App\Models\YelpSort;

class ScannerService
{

    /**
     * Options
     */

    // should we output a summary email?
    protected $summary = true;

    // should we slow down this scan?
    protected $slow = false;

    // number of seconds to sleep between API calls
    protected $sleep = 2;

    // how many failed API calls we've made this session
    protected $errors = 0;

    // the maximum allowable failed API calls per session before we abort
    protected $max_errors = 10;

    // time in seconds to wait after each failed API call
    protected $errorTimeout = 10;

    // how many zip codes to scan each session by default
    protected $zipCount = 5;

    /*
     * Internals
     */


    /**
     * Our yelpService object
     *
     * @var YelpService $yelp
     */
    protected $yelp;

    /**
     * an optional console object to output to
     *
     * @var \Illuminate\Console\Command $console
     */
    protected $console;

    // a boolean for when we do a special search and don't want to mark the zip code as having been searched afterwards
    protected $dontMarkZip;

    // search parameters that are set automatically
    protected $setAttribute;
    protected $sort;

    // for collecting our ongoing results
    protected $newLocations = [];
    protected $closedLocations = [];


    public function __construct( YelpServiceInterface $yelpService )
    {
        $this->yelp = $yelpService;
    }

    /**
     * Manually set at runtime if we should send a summary email
     *
     * @param boolean $shouldSendSummary
     * @return void
     */
    public function setSummary( $shouldSendSummary )
    {
        $this->summary = $shouldSendSummary;
    }

    /**
     * Manually set at runtime how many zip codes we should scan this session
     *
     * @param int $count
     * @return void
     */
    public function setZipCount( $count )
    {
        $this->zipCount = $count;
    }

    /**
     * Set our console if we want to output our results in realtime
     *
     * @param \Illuminate\Console\Command $console
     * @return void
     */
    public function setConsole( $console )
    {
        $this->console = $console;
    }


    /**
     * Fetch the zip codes we will be using for this scan, if a zip code is provided wrap it as an array and return it
     * or if none are supplied grab a number of unscanned zip codes from the database equal to our ZipCount property
     *
     * @param null|int $zip // an optional zip code to manually scan
     * @return array
     */
    public function getZips( $zip = null )
    {
        // if we supplied a zip code, instead just use the selected zip code
        if( $zip ) return [ $zip ];

        // check our zip codes to see if we need to reset them
        $this->checkZips();

        // get our array of zip codes
        return Zip::where( 'scanned', false )
                ->take( $this->zipCount )
                ->orderBy( 'id', 'asc' )
                ->pluck( 'zip' )
                ->toArray();
    }

    /**
     * check if we have any zip codes left to scan, if not, reset them
     * and switch to the next sort method
     *
     * @return void
     */
    protected function checkZips(){
        //
        if( Zip::where( 'scanned', false )->count() === 0 ){
            Zip::where( 'scanned', true )->update([ 'scanned' => false ]);

            // if we have worked our way through all of our zips then time to switch sorts
            $sort = YelpSort::where( 'scanned', false )->orderBy( 'id', 'asc' )->first();
            $sort->update([ 'scanned' => true ]);
        }
    }


    /**
     *  Determine the zip codes we will be scanning in this session, then run a scan each of them
     *
     * @param null|int $zip // optional zip code to manually scan
     * @param null|boolean $slow // should we slow down our scan to avoid getting throttled?
     */
    public function scan( $zip = null, $slow = null )
    {
        // slow down the process if the slow parameter set
        $this->slow = $slow;

        // get our zip codes array
        $zips = $this->getZips( $zip );

        // set our scan sort
        $this->setScanSort();

        // scan our zip codes
        foreach( $zips as $zip ){
            $this->scanZip( $zip );
        }

        // if we should send a summary email, do so now
        if( $this->summary && ( $this->newLocations || $this->closedLocations ) ){
            Mail::send( new ScanSummary( $this->newLocations, $this->closedLocations ) );
        }

    }


    /**
     *  Set our scan sort method for this session
     *
     *  @return void
     */
    protected function setScanSort()
    {
        // check if we have any sorts left to scan, if not, reset them
        if( YelpSort::where( 'scanned', false )->count() === 0 ){
            YelpSort::where( 'scanned', true )->update([ 'scanned' => false ]);
        }

        // grab the current sort method
        $sort = YelpSort::where( 'scanned', false )->orderBy( 'id', 'asc' )->first();
        $this->sort = $sort->sort;

        if( $this->console ) $this->console->info( "Sort set to {$this->sort}" );
    }

    /**
     * Run a special scan type for "new and hot" restaurants.
     *
     * @param array $options
     *      [zip] integer // manually set a zip code to scan
     *      [sort] string // manually set a sort mode
     */
    public function scanNewAndHot( $options = [] ){

        // get our zip code
        $this->zipCount = 1;
        $zip = $options['zip'] ?? $this->getZips();

        // set our sort method
        $this->sort = $options['sort'] ?? 'best_match';

        // don't set this zip code as scanned once completed (so it will still be included in the next regular scan)
        $this->dontMarkZip = true;

        // set the yelp API "hot and new" attribute for the scan
        $this->setAttribute = 'hot_and_new';

        // scan the zip code
        $this->scanZip( $zip );

        // if we should send a summary email do so now
        if( $this->summary && ( $this->newLocations || $this->closedLocations ) ){
            Mail::send( new ScanSummary( $this->newLocations, $this->closedLocations ) );
        }

    }


    /**
     * Run a scan on a single zip code
     *
     * @param array|int $zip
     */
    protected function scanZip( $zip )
    {
        // just in case I pass an array just grab the first element
        if( is_array( $zip ) ) $zip = $zip[0];

        if( $this->console ) $this->console->info( "Begin scanning {$zip}" );

        $options = [
            'slow' => $this->slow,
            'sort' => $this->sort
        ];

        // set attribute if one is included
        if( $this->setAttribute ){
            $options['attribute'] = $this->setAttribute;
            if( $this->console ) $this->console->info( "Setting attribute: {$this->setAttribute}" );
        }

        // grab our results for this zip code from yelp
        $locations = $this->yelp->search( $zip, $options );

        // if we didn't get any results then we are done
        if( !is_array( $locations ) || !count( $locations ) ){
            if( $this->console ) $this->console->error( "No locations found for {$zip}" );
            return;
        }

        $location_count = count( $locations );
        if( $this->console ) $this->console->info( "{$location_count} locations found in {$zip}" );

        // process our locations
        foreach( $locations as $location ){
            $this->processLocation( $location );
        }

        // mark our zip code as scanned once done
        if( !$this->dontMarkZip ) Zip::where( 'zip', $zip )->update(['scanned' => true]);

    }


    /**
     *  Begin processing a yelp location result
     *
     * @param object $location
     */
    protected function processLocation( $location )
    {
        // grab the matching object from our DB if it exists
        $existingLocation = Location::where( 'yelp_id', $location->id )->first();

        // if this exists but was disabled
        if( !$location->is_closed && $existingLocation && $existingLocation->exists() && !$existingLocation->active ){
            $existingLocation->active = true;
            $existingLocation->closure_scanned = false;
            $existingLocation->save();

            $existingLocation->restaurant->active = true;
            $existingLocation->restaurant->save();
            $existingLocation->refresh();

            if( $this->summary ) $this->newLocations[] = $existingLocation;
            return;
        }


        // if this location doesn't already exist add it
        if( !$existingLocation || !$existingLocation->exists() ){
            $this->addLocation( $location );
            return;
        }

        // check if location has closed
        if( $location->is_closed && $existingLocation->active ){
            $this->closeLocation( $existingLocation );
        }

        // touch last updated
        $existingLocation->touch();
    }


    /**
     * Create a location model and save it to the DB
     *
     * @param object $location
     */
    public function addLocation( $location )
    {
        // create location model
        if( $this->console ) $this->console->info( "Adding location for {$location->name}" );
        $location = Location::addLocation( $location );

        // hit the yelp API for additional location details
        $this->getLocationDetails( $location );

        // if we need to send a summary email later keep track of our new location
        if( $this->summary ) $this->newLocations[] = $location;
    }

    /**
     * hit the yelp API for additional location details and save them to our location model
     *
     * @param \App\Models\Location $location
     */
    public function getLocationDetails( $location, $debug = false )
    {
        $details = null;

        // hit the yelp API for additional location details
        try {
            $details = $this->yelp->details( $location );
        } catch ( \Throwable $e ) {
            if($debug) logger( 'Failed to scan for details: ' . $e->getMessage() );
            if( $this->console ) $this->console->error( 'Failed to scan for details: ' . $e->getMessage() );

            $this->errors++;
            if( $this->errors >= $this->max_errors ){
                if( $this->console ) $this->console->error( 'Too many errors, aborting' );
                die();
            } else {
                sleep( $this->errorTimeout );
            }
        }

        if($debug) logger("yelp search returned 200");

        // sleep for a bit to avoid API trouble
        sleep( $this->sleep );

        // if we didn't get nothin, then we are done here
        if( !$details ) return;

        if($debug) logger("found location details");

        // set location hours
        if( isset( $details->hours ) ) $location->update([ 'hours' => $details->hours[0]->open ]);

        // save location photos
        $this->saveLocationPhotos( $location, $details, $debug );
    }


    /**
     * Store the photos returned with our location details with the restaurant associated with our location model
     *
     * @param \App\Models\Location $location
     * @param object $details
     */
    public function saveLocationPhotos( $location, $details, $debug = false)
    {
        // find the restaurant model our location belongs to
        $restaurant = Restaurant::find( $location->restaurant_id );

        // if we can't find the restaurant give up
        if( ! $restaurant || !isset( $details->photos ) ){
            if($debug) logger("No restaurant, or no photos");
            return;
        }
        if($debug) logger( count( $details->photos ) . " photos found" );

        // store each photo with the restaurant model
        foreach( $details->photos as $photo ){
            try {
                $restaurant->photos()->create([ 'url' => $photo ]);
            } catch ( \Throwable $e ) {
                if($debug) logger( 'Adding photo failed: ' . $e->getMessage() );
                if( $this->console ) $this->console->error( 'Adding photo failed: ' . $e->getMessage() );
            }
        }
    }


    /**
     * Close a location that is no longer active
     *
     * @param \App\Models\Location $location
     */
    public function closeLocation( $location )
    {
        if( $this->console ) $this->console->error( "Closing location for {$location->name}" );

        // if we need to send a summary email later keep track of our closed location
        if( $this->summary ) $this->closedLocations[] = $location;

        // close the location
        $result = $location->close();

        if( $result ) if( $this->console ) $this->console->error( "Closing restaurant: {$location->name}" );
    }

}
