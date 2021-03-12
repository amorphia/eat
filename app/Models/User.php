<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Webpatser\Uuid\Uuid;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'remember_token',
        'admin',
        'uuid'
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
     *  Events
     *
     */

    public static function boot()
    {
        parent::boot();

        // when creating a user, generate a UUID for that user
        self::creating( function ( $model ) {
            $model->uuid = (string) Uuid::generate( 4 );
        });
    }


    /**
    *
    *  Relationships
    *
    */


    // the restaurants we have blocked
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
