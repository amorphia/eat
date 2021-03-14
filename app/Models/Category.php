<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
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
     * Get the restaurants associated with this category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class );
    }


    /**
     * Get any users blocking this category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users_blocking()
    {
        return $this->belongsToMany(User::class );
    }
}
