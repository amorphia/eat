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

    public function users()
    {
        return $this->belongsToMany(User::class, 'ratings' )
                    ->as( 'rating' )
                    ->withPivot('visited', 'rating', 'priority' );
    }

}
