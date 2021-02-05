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
    protected $match_id = null;


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
                    ->where('active', true )
                    ->orderBy( 'priority', 'desc' );
    }


    public function locations()
    {
        return $this->hasMany( Location::class )
                    ->where( 'active', true );
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
                    ->setMatches()
                    ->setSelects()
                    ->setRatedFilter()
                    ->setOrder();
    }


    public function scopeSetMatches( $query )
    {
        if( !request()->match ) return $query;

        return $query->leftJoin( 'ratings AS match', function( $join ) {
            $join->on( 'restaurants.id', '=', 'match.restaurant_id' )
                ->where( 'match.user_id', '=', $this->getMatchId() );
        });
    }


    public function scopeSetSelects( $query )
    {

        $query->select('restaurants.*',
            DB::raw('coalesce( ratings.rating, 0) as rating'),
            DB::raw('coalesce( ratings.interest, 0) as interest'),
        );

        if( !request()->match ){
            return $query;
        } else {
            return $query->setMatchSelects();
        }
    }

    public function scopeSetMatchSelects( $query ){

        $query->addSelect(
            DB::raw('coalesce( match.rating, 0) as match_rating'),
            DB::raw('coalesce( match.interest, 0) as match_interest')
        );

        switch( request()->type ){
            case "interest":
                $query->addSelect(
                    DB::raw('coalesce( (match.interest * 5), 0) as combined_rating'),
                    )->where( 'match.interest', '>=', 1 );
                break;

            case "ratings":
                $query->addSelect(
                    DB::raw('coalesce( match.rating, 0) as combined_rating'),
                    )->where( 'match.rating', '>=', 1 );
                break;

            default:
                $query->addSelect(
                    DB::raw('coalesce( (match.interest * 5) + (ratings.interest * 5) + match.rating + ratings.rating, 0) as combined_rating'),
                    )->where( function($query) {
                        $query->where( function( $query ){
                            $query->where( 'match.rating', '>=', 4 )
                                ->orWhere( 'match.interest', '>=', 1 );
                        })->where( function( $query ){
                            $query->where( 'ratings.rating', '>=', 4 )
                                ->orWhere( 'ratings.interest', '>=', 1 );
                        }) ;
                });
                break;
        }

        return $query;
    }


    public function scopeSearch( $query, $searchTerm )
    {
        return $query->where( 'name', 'like', "%{$searchTerm}%" )
            ->active()
            ->withRelations()
            ->setSelects()
            ->joinRatings()
            ->take( 10 )
            ->orderBy( 'name', 'asc' );
    }


    public function scopeActive( $query )
    {
        return $query->where( 'active', true )
                ->where( function($query) {
                    $query->where( 'ratings.interest', '!=', -1 )
                        ->orWhereNull( 'ratings.interest' );
                });
    }


    public function scopeWithLimitedRelations( $query )
    {
        return $query->with([
            'photos',
            'categories'
        ]);

    }

    public function scopeWithRelations( $query )
    {

        return $query->with([
                'locations',
                'photos',
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
                });
    }


    public function scopeSetOrder( $query )
    {
        // all match searches are sorted by combined rating
        if( request()->match ) return $query->orderBy( 'combined_rating', 'desc' );

        $sort = request()->sort ?? 'interest';

        // add our manual sort if included
        if( $sort ){
            $query->orderBy( $sort, request()->direction ?? 'desc' );
        }

        // add default sorts
        if( $sort !== 'rating' ) $query->orderBy( 'rating', 'desc' );
        if( $sort !== 'interest' ) $query->orderBy( 'interest', 'desc' );
        if( $sort !== 'name' ) $query->orderBy( 'name', 'asc' );

        return $query;
    }


    public function scopeSetRatedFilter( $query )
    {
        if( request()->match ) return $query;

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
        if( request()->match ) return $query;

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

    protected function getMatchId(){
          if( $this->match_id ) return $this->match_id;

          $match = User::where( 'uuid', request()->match )->first();
          $this->match_id = $match->id;

          return $this->match_id;
    }

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

        $this->update([ 'active' => false, 'name' => $this->name . 'x' ]);
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
