<?php


namespace App\Actions;

use App\Models\Location;
use App\Models\Restaurant;
use App\Services\YelpServiceInterface;
use Goutte\Client as HTTPClient;

class CreateLocationFromYelp
{
    /**
    * Our Yelp API Service class
    * @var YelpServiceInterface
    */
    protected $yelp;

    /**
     * Our Guzzle Client
     * @var HTTPClient
     */
    protected $client;


    public function __construct( YelpServiceInterface $yelpService, HTTPClient $client )
    {
        $this->yelp = $yelpService;
        $this->client = $client;
    }


    /**
     * @param string $id
     * @param bool|null $fromUrl
     * @return Restaurant|void
     * @throws \Exception
     */
    public function execute( string $id, ?string $mode = id )
    {
        if( $mode === 'page' ){
            $id = $this->getIdFromUrl( $id );
            if( !$id) throw new \Exception( 'could not extract yelp id from url' );
        }

        // add yelp location
        return $this->createLocationFromYelpId( $id );
    }


    /**
     * Attempts to return a yelp biz id from a yelp url
     *
     * @param $url
     * @return string|null
     * @throws \Exception
     */
    protected function getIdFromUrl( string $url )
    {
        // remove parameters from the url and then check if its already in our DB
        $url = stripUrlParams( $url );
        $count = Location::where( 'yelp_url', $url )->count();

        // if this url already exists in our DB then abort
        if( $count > 0 ){
            throw new \Exception( 'This location already exists' );
        }

        // scrape the page
        $crawler = $this->client->request('GET', $url );

        // get id
        return $crawler->filter( 'meta[name=yelp-biz-id]' )->eq( 0 )->attr( 'content' );
    }


    /**
     * Create and store a location via a yelp ID
     *
     * @param string $id
     * @param YelpServiceInterface $yelp
     * @return \App\Models\Restaurant | void
     */
    protected function createLocationFromYelpId( string $id ){

        // fetch location details rom the yelp API via our yelp id
        $details = $this->yelp->details( $id );

        // if we didn't get anything abort
        if( !$details ) throw new \Exception( 'Could not get location details from yelp' ) ;

        // store location
        $location = Location::addLocation( $details );

        // update location hours
        if( isset( $details->hours ) ) $location->update([ 'hours' => $details->hours[0]->open ]);

        // get parent restaurant
        $restaurant = Restaurant::find( $location->restaurant_id );

        // add photos
        if( isset( $details->photos ) ){
            foreach( $details->photos as $photo ){
                $restaurant->photos()->create([ 'url' => $photo ]);
            }
        }

        // hit the database for the same restaurant again, with all of the
        // adjustments / relations / joins I make when searching so I can return it
        return Restaurant::search( $restaurant->name )->first();
    }
}
