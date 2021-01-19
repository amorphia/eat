<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
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


    public function post()
    {
        return $this->belongsTo( Post::class );
    }


    public function restaurant()
    {
        return $this->belongsTo( Restaurant::class );
    }
}
