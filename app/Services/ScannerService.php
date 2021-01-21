<?php


namespace App\Services;

use App\Models\Category;
use App\Models\Location;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ScanSummary;
use App\Services\YelpService;

class ScannerService
{

    protected $yelp;
    protected $console;
    protected $summary = true;
    protected $newLocations = [];
    protected $closedLocations = [];

    public function __construct( YelpService $yelpService )
    {
        $this->yelp = $yelpService;
    }


    public function setSummary( $shouldSendSummary )
    {
        $this->summary = $shouldSendSummary;
    }


    public function setConsole( $console )
    {
        $this->console = $console;
    }


    /*
     *  Run a full scan of all the zip codes in our area
     */
    public function scan( $zip = null )
    {
        // get our array of zip codes then shuffle them
        $zips = config( 'services.yelp.zip_codes' );
        shuffle ( $zips );

        // if we supplied a zip code, instead just use the selected zip code
        if( $zip ) $zips = [ $zip ];

        foreach( $zips as $zip ){
            $this->scanZip( $zip );
        }

        if( $this->summary && ( $this->newLocations || $this->closedLocations ) ){
            Mail::send( new ScanSummary( $this->newLocations, $this->closedLocations ) );
        }

    }


    /*
     * Run a scan for a single zip code
     */
    protected function scanZip( $zip )
    {
        if( $this->console ) $this->console->info( "Begin scanning {$zip}" );

        // grab our results for this zip code from yelp
        $locations = $this->yelp->search( $zip );

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
    }


    /*
     *
     *  Begin processing a yelp location result
     *
     */
    protected function processLocation( $location )
    {
        // grab the matching object from our DB if it exists
        $existingLocation = Location::where( 'yelp_id', $location->id )->first();

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


    public function addLocation( $location )
    {
        if( $this->console ) $this->console->info( "Adding location for {$location->name}" );
        $location = Location::addLocation( $location );
        if( $this->summary ) $this->newLocations[] = $location;
    }


    public function closeLocation( $location )
    {
        if( $this->console ) $this->console->error( "Closing location for {$location->name}" );
        $location->close();

        if( $this->summary ) $this->closedLocations[] = $location;

        // check if restaurant parent needs to close
        $openLocationCount = Location::where( 'name', $location->name )
                            ->where( 'active', true )
                            ->count();

        if( !$openLocationCount )$this->closeRestaurant( $location->name );
    }


    public function closeRestaurant( $name )
    {
        if( $this->console ) $this->console->error( "Closing restaurant: {$name}" );
        $restaurant = Restaurant::where( 'name', $name )->first();
        $restaurant->close();
    }

}
