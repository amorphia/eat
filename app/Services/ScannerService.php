<?php


namespace App\Services;

use App\Models\Zip;
use App\Models\Category;
use App\Models\Location;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ScanSummary;
use App\Services\YelpService;
use App\Models\YelpSort;

class ScannerService
{

    protected $yelp;
    protected $console;
    protected $slow;
    protected $summary = true;
    protected $newLocations = [];
    protected $closedLocations = [];
    protected $sleep = 2;
    protected $errors = 0;
    protected $max_errors = 10;
    protected $errorTimeout = 10;
    protected $sort;
    protected $zipCount = 5;
    protected $dontMarkZip;
    protected $setAttribute;


    public function __construct( YelpService $yelpService )
    {
        $this->yelp = $yelpService;
    }


    public function setSummary( $shouldSendSummary )
    {
        $this->summary = $shouldSendSummary;
    }


    public function setZipCount( $count )
    {
        $this->zipCount = $count;
    }

    public function setConsole( $console )
    {
        $this->console = $console;
    }


    public function getZips( $zip = null )
    {
        // if we supplied a zip code, instead just use the selected zip code
        if( $zip ) return [ $zip ];


        // check if we have any zips left to scan, if not, reset them
        if( Zip::where( 'scanned', false )->count() === 0 ){
            Zip::where( 'scanned', true )->update([ 'scanned' => false ]);

            // if we have worked our way through all of our zips then time to switch sorts
            $sort = YelpSort::where( 'scanned', false )->orderBy( 'id', 'asc' )->first();
            $sort->update([ 'scanned' => true ]);
        }

        // get our array of zip codes
        return Zip::where( 'scanned', false )
                ->take( $this->zipCount )
                ->orderBy( 'id', 'asc' )
                ->pluck( 'zip' )
                ->toArray();
    }

    /*
     *  Run a full scan of all the zip codes in our area
     */
    public function scan( $zip = null, $slow = null )
    {
        // slow down the process if the slow parameter set
        $this->slow = $slow;

        // get our zip codes array
        $zips = $this->getZips( $zip );

        // set our scan sort
        $this->setScanSort();

        foreach( $zips as $zip ){
            $this->scanZip( $zip );
        }

        if( $this->summary && ( $this->newLocations || $this->closedLocations ) ){
            Mail::send( new ScanSummary( $this->newLocations, $this->closedLocations ) );
        }

    }


    protected function setScanSort()
    {
        // check if we have any sorts left to scan, if not, reset them
        if( YelpSort::where( 'scanned', false )->count() === 0 ){
            YelpSort::where( 'scanned', true )->update([ 'scanned' => false ]);
        }

        // set our array of zip codes
        $sort = YelpSort::where( 'scanned', false )->orderBy( 'id', 'asc' )->first();
        $this->sort = $sort->sort;

        if( $this->console ) $this->console->info( "Sort set to {$this->sort}" );
    }



    public function scanNewAndHot( $options = [] ){

        $zip = $options['zip'] ?? 43201;

        $this->sort = $options['sort'] ?? 'best_match';
        $this->dontMarkZip = true;
        $this->setAttribute = 'hot_and_new';

        $this->scanZip( $zip );

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

        $options = [
            'slow' => $this->slow,
            'sort' => $this->sort
        ];

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

        $this->getLocationDetails( $location );

        if( $this->summary ) $this->newLocations[] = $location;
    }


    protected function getLocationDetails( $location )
    {
        $details = null;

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
        if( !$details ) return;

        // set location hours
        if( isset( $details->hours ) ) $location->update([ 'hours' => $details->hours[0]->open ]);

        // save location photos
        $this->saveLocationPhotos( $location, $details );
    }


    protected function saveLocationPhotos( $location, $details )
    {
        $restaurant = Restaurant::find( $location->restaurant_id );
        if( ! $restaurant || !isset( $details->photos ) ) return;

        foreach( $details->photos as $photo ){
            $restaurant->photos()->create([ 'url' => $photo ]);
        }
    }


    public function closeLocation( $location )
    {
        if( $this->console ) $this->console->error( "Closing location for {$location->name}" );
        if( $this->summary ) $this->closedLocations[] = $location;

        $result = $location->close();
        if( $result ) if( $this->console ) $this->console->error( "Closing restaurant: {$location->name}" );
    }


}
