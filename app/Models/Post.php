<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     *
     *  Relationships
     *
     */

    public function user()
    {
        return $this->belongsTo( User::class );
    }


    public function photos()
    {
        return $this->hasMany( Photo::class );
    }


    public function restaurant()
    {
        return $this->belongsTo( Restaurant::class );
    }
}
