<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->integer( 'user_id' );
            $table->integer( 'restaurant_id' );
            $table->boolean( 'visited' )->nullable();
            $table->integer( 'rating' )->nullable();
            $table->boolean( 'starred' )->default( false );
            $table->timestamps();
            $table->unique(['user_id', 'restaurant_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
