<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class Photo extends Model
{
    use HasFactory;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * The maximum height/width of our image
     *
     * @var int
     */
    const MAX_SIZE = 1000;


    /**
     *
     *  Relationships
     *
     */

    /**
     * get the user who owns this image
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo( User::class );
    }


    /**
     * get the post this photo is associated with (if any)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo( Post::class );
    }


    /**
     * get the restaurant this photo belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function restaurant()
    {
        return $this->belongsTo( Restaurant::class );
    }


    /**
     *
     *  Statics
     *
     */


    /**
     * Store a photo from validated request data
     *
     * @param $request
     * @param $validated
     * @return Model
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function storePhoto( $request, $validated )
    {
        // save image if file supplied rather than url
        if( $validated['image'] ){
            $validated['url'] = Photo::saveUpload( $request );
        }

        // get parent restaurant
        $restaurant = Restaurant::where( 'id', $validated['restaurant_id'] )->first();

        // store in db
        $photo = $restaurant->photos()->create([
            'url' => $validated['url'],
            'body' => $validated['body'],
            'user_id' => user()->id
        ]);

        return $photo;
    }


    /**
     * @param Request $request
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function saveUpload( Request $request )
    {

        $path = $request->image->store( 'public/uploads' );
        return Storage::url( $path );
    }

}
