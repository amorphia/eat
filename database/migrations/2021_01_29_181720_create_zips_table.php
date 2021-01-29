<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Zip;

class CreateZipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zips', function (Blueprint $table) {
            $table->id();
            $table->integer( 'zip' )->unique();
            $table->boolean( 'scanned' )->default( false );
            $table->timestamps();
        });

        $seedZips = config( 'services.yelp.zip_codes' );
        foreach( $seedZips as $zip  ){
            Zip::create([ 'zip' => $zip ]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zips');
    }
}
