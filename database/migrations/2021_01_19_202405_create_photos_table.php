<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function ( Blueprint $table ) {
            $table->id();
            $table->integer( 'user_id' )->nullable();
            $table->integer( 'restaurant_id' );
            $table->integer( 'post_id' )->nullable();
            $table->string( 'url' )->unique();
            $table->text( 'body' )->nullable();
            $table->boolean( 'priority' )->default( false );
            $table->boolean( 'public' )->default( true );
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
        Schema::dropIfExists('photos');
    }
}
