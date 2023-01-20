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

class ClosedService
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

    // how many closed restaurants to scan for at a time
    protected $closedCount = 100;


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
    public function setClosedCount( $count )
    {
        $this->closedCount = $count;
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
     * Fetch the locations we will check for closures
     *
     * @return array
     */
    public function getLocations()
    {
        return Location::active()
                ->where( 'closure_scanned', false )
                ->take( $this->closedCount )
                ->get();
    }

    /**
     *  Scan restaurants for closure
     *
     * @param null|boolean $slow // should we slow down our scan to avoid getting throttled?
     */
    public function scan( $slow = null )
    {
        // slow down the process if the slow parameter set
        $this->slow = $slow;

        // get our zip codes array
        $locations = $this->getLocations();

        // check our locations for closure
        foreach( $locations as $location ){
            $this->checkLocation( $location );
        }

        // if we've scanned the last of our locations reset our scanner
        if( Location::active()->where( 'closure_scanned', false )->count() === 0 ){
            Location::active()->where('closure_scanned', true)->update(['closure_scanned' => false]);
        }

        // if we should send a summary email, do so now
        if( $this->summary && $this->closedLocations ){
            Mail::send( new ScanSummary( null, $this->closedLocations ) );
        }
    }

    /**
     * Check our location for closure
     *
     * @param Location $location
     */
    protected function checkLocation( $location )
    {
        if( $this->console ) $this->console->info( "Checking {$location->name}" );

        // grab our results for this location from yelp
        $details = $this->getLocationDetails( $location );

        $location->update(['closure_scanned' => true]);

        // if we didn't get any results then we are done
        if( !$details ){
            if( $this->console ) $this->console->error( "No locations details found for {$location->name} [{$location->yelp_id}]" );
            return;
        }

        if($details->is_closed){
            $this->closeLocation( $location );
        }
    }


    /**
     * hit the yelp API for additional location details and save them to our location model
     *
     * @param \App\Models\Location $location
     */
    protected function getLocationDetails( $location )
    {
        $details = null;

        // hit the yelp API for additional location details
        try {
            $details = $this->yelp->details( $location );
        } catch ( \Throwable $e ) {
            if( $this->console ) $this->console->error( 'Failed to scan for details: ' . $e->getMessage() );

            $this->errors++;
            if( $this->errors >= $this->max_errors ){
                if( $this->console ) $this->console->error( 'Too many errors, aborting' );
                die();
            } else {
                sleep( $this->errorTimeout );
            }
        }

        // sleep for a bit to avoid API trouble
        sleep( $this->sleep );

        // if we didn't get nothin, then we are done here
        return $details;
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
