<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Restaurant extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['checked'];


    /**
     *
     *  Relationships
     *
     */

    public function categories()
    {
        return $this->belongsToMany(Category::class )
            ->where('active', 1 )
            ->orderBy( 'priority', 'desc' );
    }

    public function posts()
    {
        return $this->hasMany( Post::class )
            ->orderBy( 'created_at', 'desc' );
    }

    public function photos()
    {
        return $this->hasMany( Photo::class )
            ->where('active', 1 )
            ->orderBy( 'priority', 'desc' );
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
     *  Attributes
     *
     */

    // dummy attributes appended to json output so its
    // initialized for use on the vue end automatically
    public function getCheckedAttribute()
    {
        return false;
    }


    /**
     *
     *  Scopes
     *
     */

    public function scopeIndex( $query )
    {
        return $query->active()
                    ->withRelations()
                    ->setCategory()
                    ->joinRatings()
                    ->setRatedFilter()
                    ->setOrder();
    }


    public function scopeSearch( $query, $searchTerm )
    {
        return $query->where( 'name', 'like', "%{$searchTerm}%" )
            ->withRelations()
            ->active()
            ->joinRatings()
            ->take( 10 )
            ->orderBy( 'name', 'asc' );
    }


    public function scopeActive( $query )
    {
        return $query->where( 'active', true )
                ->where( function($query) {
                    $query->where( 'interest', '!=', -1 )
                        ->orWhereNull( 'interest' );
                });
    }


    public function scopeWithRelations( $query )
    {
        return $query->with([
                'locations',
                'photos.user',
                'categories',
                'posts' => function ( $q ) {
                    return $q->where( 'user_id', request()->user()->id );
                }]);
    }


    public function scopeJoinRatings( $query )
    {
        return $query->leftJoin( 'ratings', function( $join ) {
                    $join->on( 'restaurants.id', '=', 'ratings.restaurant_id' )
                        ->where( 'ratings.user_id', '=', request()->user()->id );
                })
                ->select('restaurants.*',
                    DB::raw('coalesce( ratings.rating, 0) as rating'),
                    DB::raw('coalesce( ratings.interest, 0) as interest')
                );
    }


    public function scopeSetOrder( $query )
    {
        $sort = request()->sort;

        // add our manual sort if included
        if( $sort ){
            $query->orderBy( $sort, request()->direction );
        }

        // add default sorts
        if( $sort !== 'rating' ) $query->orderBy( 'rating', 'desc' );
        if( $sort !== 'interest' ) $query->orderBy( 'interest', 'desc' );
        if( $sort !== 'name' ) $query->orderBy( 'name', 'asc' );

        return $query;
    }


    public function scopeSetRatedFilter( $query )
    {
        $filter = request()->rated;

        switch( $filter ){
            // if we are filtering by rated only say our rating has to be equal to non-zero
            case 'rated': return $query->where( 'rating', '!=', 0 );

            // if we are filtering by unrated only then allow ratings of 0 or null
            case 'unrated': return $query->where( function( $query ) {
                    $query->where( 'rating', 0 )
                          ->orWhereNull( 'rating' );
            });

            // otherwise just return the query
            default: return $query;
        }

    }


    public function scopeSetCategory( $query )
    {
        $category = request()->category;

        // if set to all, just return
        if( !$category || $category === 'all' ) return $query;

        // otherwise filter by relation with chosen category
        return $query->whereHas( 'categories', function( $q ) use ( $category ){
            $q->where( 'name', $category );
        });

    }

    /**
     *
     *  Methods
     *
     */
    public function close()
    {
        $this->update([ 'active' => false ]);
    }


    public function merge( $id )
    {
        if( $this->id === $id ) return;

        $this->photos->each->update([ 'restaurant_id' => $id ]);
        $this->posts->each->update([ 'restaurant_id'=> $id ]);
        $this->locations->each->update([ 'restaurant_id' => $id ]);

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

            $category = Category::where( 'name', $cat )->first();

            if( ! $category ){
                $category = Category::create([ 'name' => $cat, 'label' => $cat ]);
            }

            // attach category
            $this->categories()->attach( $category );
        }
    }
}
