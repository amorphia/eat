<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $guarded = [];


    /**
     *
     *  Relationships
     *
     */

    public function categories()
    {
        return $this->belongsToMany(Category::class );
    }

    public function posts()
    {
        return $this->hasMany( Post::class );
    }

    public function photos()
    {
        return $this->hasMany( Photo::class );
    }

    public function locations()
    {
        return $this->hasMany( Location::class );
    }

    public function ratings()
    {
        return $this->hasMany( Rating::class );
    }

    /*
    public function users()
    {
        return $this->belongsToMany(User::class, 'ratings' )
                    ->as( 'rating' )
                    ->withPivot('visited', 'rating', 'priority' );
    }
    */


    /**
     *
     *  Methods
     *
     */
    public function close()
    {
        $this->update([ 'active' => false ]);
    }


    public static function addRestaurant( $location )
    {

        // create restaurant
        $restaurant = self::create([
            'name' => $location->name,
            'image' => $location->image_url
        ]);

        // create array of categories
        $categories = [];

        foreach( $location->categories as $category ){
            $categories[] = $category->title;
        }

        // add restaurant categories
        $restaurant->addCategories( $categories );

        return $restaurant;
    }

    public function addCategories( $categories )
    {
        foreach( $categories as $cat ){
            // find the category of create it if new
            $category = Category::firstOrCreate([ 'name' => $cat ]);
            // attach category
            $this->categories()->attach( $category );
        }
    }
}
