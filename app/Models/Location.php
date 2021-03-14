<?php

namespace App\Models;

use App\Models\Restaurant;
use App\Services\YelpServiceInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Goutte\Client as HTTPClient;
use Malhal\Geographical\Geographical;

class Location extends Model
{
    use HasFactory;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     *
     *  Relationships
     *
     */

    /**
     * Get the restaurant this location belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class );
    }


    /**
     *
     *  Methods
     *
     */

    /**
     * @param object $locationData
     * @return Location|Model
     */
    public static function addLocation( $locationData )
    {
        // create restaurant
        $location = self::create([
            'yelp_id' => $locationData->id,
            'phone' => $locationData->phone,
            'slug' => $locationData->alias,
            'name' => $locationData->name,
            'yelp_url' => stripUrlParams( $locationData->url ),
            'latitude' => $locationData->coordinates->latitude,
            'longitude' =>	$locationData->coordinates->longitude,
            'street' => $locationData->location->address1,
            'city' => $locationData->location->city,
            'zip' => $locationData->location->zip_code,
        ]);

        // check for restaurant parent and create if it doesn't exist
        $restaurant = Restaurant::where( 'name', $location->name )->first();
        if( !$restaurant || !$restaurant->exists ){
            $restaurant = Restaurant::addRestaurant( $locationData );
        }

        // attach location to restaurant
        $restaurant->locations()->save( $location );

        return $location;
    }


    /**
     * Add a location via a yelp url
     *
     * @param string $url
     * @param YelpServiceInterface $yelp
     * @return mixed
     */
    public static function addByYelpPage( string $url, YelpServiceInterface $yelp )
    {

        $client = new HTTPClient();

        // scrape the page
        $crawler = $client->request('GET', $url );

        // get id
        $id = $crawler->filter( 'meta[name=yelp-biz-id]' )->eq( 0 )->attr( 'content' );
        if( !$id ) return;

        // store
        return self::addByYelpId( $id, $yelp );
    }


    /**
     * Create and store a location via a yelp ID
     *
     * @param string $id
     * @param YelpServiceInterface $yelp
     * @return \App\Models\Restaurant | void
     */
    public static function addByYelpId( string $id, YelpServiceInterface $yelp )
    {
        // fetch location details rom the yelp API via our yelp id
        $details = $yelp->details( $id );

        // if we didn't get anything abort
        if( !$details ) return;

        // store location
        $location = self::addLocation( $details );

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


    /**
     * Close this location, and if this was a restaurant's only location close it as well
     *
     * @return bool | void
     */
    public function close()
    {
        $this->update([ 'active' => false ]);

        // check how many locations the parent restaurant has left
        $openLocationCount = Location::where( 'restaurant_id', $this->restaurant_id )
            ->where( 'active', true )
            ->count();

        // if the parent restaurant has no locations left, then close it
        if( !$openLocationCount ){
            $restaurant = Restaurant::find( $this->restaurant_id );
            $restaurant->close();
            return true;
        }
    }

}
