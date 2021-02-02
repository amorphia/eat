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


    /**
     *
     *  Statics
     *
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

    public static function scaleImage( $img )
    {
        // set starting and ending dimensions
        $start_width = $img->getImageWidth();
        $start_height = $img->getImageHeight();

        $excess_width = $start_width - 1000;
        $excess_height = $start_height - 1000;

        // get the current dimension ratio
        if( $excess_height <= 0 && $excess_width <= 0 ){
            return $img;
        }

        // set final size parameters
        if( $excess_width > $excess_height ){
            $width = 1000;
            $height = 0;
        } else {
            $height = 1000;
            $width = 0;
        }

        // scale image
        $img->scaleImage( $width, $height );

        return $img;
    }

}
