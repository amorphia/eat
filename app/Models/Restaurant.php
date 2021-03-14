<?php

namespace App\Models;

use App\Services\PipelineService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\Geographical;

class Restaurant extends Model
{
    use HasFactory, Geographical;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be appended to json output.
     *
     * @var array
     */
    protected $appends = ['checked'];



    /**
     *
     *  Relationships
     *
     */


    /**
     * Get the categories associated with this restaurant
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class )
                    ->where('active', 1 )
                    ->orderBy( 'priority', 'desc' );
    }


    /**
     * Get the posts (notes) belonging to this restaurant
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany( Post::class )
                    ->orderBy( 'created_at', 'desc' );
    }


    /**
     * Get the photos belonging to this restaurant
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany( Photo::class )
                    ->where('active', true )
                    ->orderBy( 'priority', 'desc' );
    }


    /**
     * Get the locations belonging to this restaurant
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locations()
    {
        return $this->hasMany( Location::class )
                    ->where( 'active', true );
    }


    /**
     * Ge the ratings belonging to this restaurant
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings()
    {
        return $this->hasMany( Rating::class );
    }



    /**
     *
     *  Attributes
     *
     */


    /**
     * dummy attribute appended to json output so it is initialized
     * and reactive on the vue end automatically
     *
     * @return bool false
     */
    public function getCheckedAttribute()
    {
        return false;
    }


    /**
     *
     *  Scopes
     *
     */




    /**
     * Query scope for our index list search
     *
     * @param Builder $query
     * @param PipelineService $pipeline
     * @return Builder
     */
    public function scopeIndex( Builder $query, PipelineService $pipeline )
    {
        $filters = [
            \App\QueryFilters\Category::class,
            \App\QueryFilters\Rated::class,
            \App\QueryFilters\Match::class,
            \App\QueryFilters\Sort::class,
        ];

        // initialize the query with the elements we will need for every request
        $query->active()
              ->withRelations()
              ->joinRatings();

        // send the query through our query filters pipeline and then return the query
        return $pipeline->resolve( $query, $filters );
    }


    /**
     * Query scope for when we perform a restaurant search
     *
     * @param Builder $query
     * @param string $searchTerm
     * @return Builder
     */
    public function scopeSearch( Builder $query, string $searchTerm )
    {
        return $query->where( 'name', 'like', "%{$searchTerm}%" )
            ->active()
            ->withRelations()
            ->joinRatings()
            ->take( 10 )
            ->orderBy( 'name', 'asc' );
    }


    /**
     * Get active restaurants
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive( Builder $query )
    {
        return $query->where( 'restaurants.active', true )
                ->where( function($query) {
                    $query->where( 'ratings.interest', '!=', -1 )
                        ->orWhereNull( 'ratings.interest' );
                });
    }



    /**
     * Add the restaurant's core relations
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithRelations( Builder $query )
    {

        return $query->with([
                'locations',
                'photos',
                'categories',
                'posts' => function ( $q ) {
                    return $q->where( 'user_id', request()->user()->id );
                }]);

    }


    /**
     * Join the current user's ratings to the restaurants
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeJoinRatings( Builder $query )
    {
        return $query->leftJoin( 'ratings', function( $join ) {
                    $join->on( 'restaurants.id', '=', 'ratings.restaurant_id' )
                        ->where( 'ratings.user_id', '=', request()->user()->id );
                })->select('restaurants.*',
                    DB::raw('coalesce( ratings.rating, 0) as rating'),
                    DB::raw('coalesce( ratings.interest, 0) as interest'),
                    DB::raw('coalesce( ratings.viewed, 0) as viewed'),
                );
    }




    /**
     *
     *  Methods
     *
     */


    /**
     *  Close this restaurant
     *
     * @return void
     */
    public function close()
    {
        $this->update([ 'active' => false ]);
    }


    /**
     * Merge this restaurant into another restaurant entry
     *
     * @param int $id
     * @return void
     */
    public function merge( int $id )
    {
        // we can't merge into ourselves, silly
        if( $this->id === $id ) return;

        // make sure we have a restaurant to merge into
        Restaurant::findOrFail( $id );

        // merge our relations
        $this->photos->each->update([ 'restaurant_id' => $id ]);
        $this->posts->each->update([ 'restaurant_id'=> $id ]);
        $this->locations->each->update([ 'restaurant_id' => $id ]);

        // set this restaurant as inactive, and add an "x" to the end of the name in case we want to change the
        // merged restaurant's name this name, so we don't get a duplicate name error
        $this->update([ 'active' => false, 'name' => $this->name . 'x' ]);
    }


    /**
     * Create a restaurant from the given location data
     *
     * @param $location
     * @return Restaurant|Model
     */
    public static function addRestaurant( $location )
    {
        // create restaurant
        $restaurant = self::create([
            'name' => $location->name,
            'image' => $location->image_url,
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


    /**
     * Add categories to this restaurant
     *
     * @param array $categories
     */
    public function addCategories( array $categories )
    {
        foreach( $categories as $cat ){

            // find the category of create it if we don't have one already
            $category = Category::where( 'name', $cat )->first();

            if( ! $category ){
                $category = Category::create([ 'name' => $cat, 'label' => $cat ]);
            }

            // attach category
            $this->categories()->attach( $category );
        }
    }
}
