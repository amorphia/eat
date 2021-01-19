<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string( 'yelp_id' );
            $table->string( 'phone' )->nullable();
            $table->string( 'slug' );
            $table->string( 'name' );
            $table->string( 'yelp_url' );
            $table->decimal('latitude', 11, 8 );
            $table->decimal('longitude', 11, 8 );
            $table->string( 'street' );
            $table->string( 'city' );
            $table->integer( 'zip' );
            $table->boolean( 'active' )->default( true );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
