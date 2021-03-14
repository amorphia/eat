<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Imagick;

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
     * @throws \ImagickException
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
     * @throws \ImagickException
     */
    public static function saveUpload( Request $request )
    {
        // set a temp name equal to the unixtimestamp with microseconds
        $name = implode( '', hrtime() );
        $extension = $request->image->extension();

        // store the file
        $request->image->storeAs( '/public/uploads', "{$name}.{$extension}" );

        // open source file with imagick
        $img = new Imagick();
        $img->readImageBlob( Storage::get( "/public/uploads/{$name}.{$extension}" ) );

        // resize image
        $img = self::scaleImage( $img );

        // save as jpg
        $img->setImageFormat( 'jpg' );
        $img->stripImage();
        $img->writeImage( storage_path() . "/app/public/uploads/{$name}.jpg" );
        $img->clear();
        $img->destroy();

        return "/storage/uploads/{$name}.jpg";
    }


    /**
     * Scale the image
     *
     * @param Imagick $img
     * @return Imagick $image
     * @throws \ImagickException
     */
    public static function scaleImage( Imagick $img )
    {
        // set starting and ending dimensions
        $start_width = $img->getImageWidth();
        $start_height = $img->getImageHeight();

        $excess_width = $start_width - self::MAX_SIZE;
        $excess_height = $start_height - self::MAX_SIZE;

        // get the current dimension ratio
        if( $excess_height <= 0 && $excess_width <= 0 ){
            return $img;
        }

        // set final size parameters
        if( $excess_width > $excess_height ){
            $width = self::MAX_SIZE;
            $height = 0;
        } else {
            $height = self::MAX_SIZE;
            $width = 0;
        }

        // scale image
        $img->scaleImage( $width, $height );

        return $img;
    }

}
