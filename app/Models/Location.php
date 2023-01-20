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
     *  Scopes
     *
     */

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive( $query )
    {
        return $query->where('active', true);
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
