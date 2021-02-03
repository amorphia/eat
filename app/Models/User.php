<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    /**
     *
     *  Relationships
     *
     */

    /*
    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'ratings' )
            ->as( 'rating' )
            ->withPivot('visited', 'rating', 'priority' );
    }
    */

    public function blocked()
    {
        return $this->belongsToMany(Category::class );
    }

    public function ratings()
    {
        return $this->hasMany( Rating::class );
    }

    public function posts()
    {
        return $this->hasMany( Post::class );
    }

    public function photos()
    {
        return $this->hasMany( Photo::class );
    }
}
