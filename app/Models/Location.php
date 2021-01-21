<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            'yelp_url' => self::getUrlWithoutParameters( $locationData->url ),
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


    protected static function getUrlWithoutParameters( $url )
    {
        $arr = explode("?", $url );
        return $arr[0];
    }


    public function close()
    {
        $this->update([ 'active' => false ]);
    }

}
