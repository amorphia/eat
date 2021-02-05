<?php

namespace App\Models;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Goutte\Client;
use Malhal\Geographical\Geographical;

class Location extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     *
     *  Relationships
     *
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

    public static function addByYelpPage( $url, $yelp )
    {

        $client = new Client();

        // scrape the page
        $crawler = $client->request('GET', $url );

        // get id
        $id = $crawler->filter( 'meta[name=yelp-biz-id]' )->eq( 0 )->attr( 'content' );
        if( !$id ) return;

        // store
        return self::addByYelpId( $id, $yelp );
    }


    public static function addByYelpId( $id, $yelp )
    {
        $details = $yelp->details( $id );
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

        // hit the database for the same restaurant again, with with all of the
        // adjustments / relations / joins I make when searching so I can return it
        return Restaurant::search( $restaurant->name )->first();
    }


    public function close()
    {
        $this->update([ 'active' => false ]);

        // check if restaurant parent needs to close
        $openLocationCount = Location::where( 'restaurant_id', $this->restaurant_id )
            ->where( 'active', true )
            ->count();

        if( !$openLocationCount ){
            $restaurant = Restaurant::find( $this->restaurant_id );
            $restaurant->close();
            return true;
        }
    }

}
