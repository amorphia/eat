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
            $table->foreignId( 'user_id' )->constrained()->onDelete( 'cascade' );
            $table->foreignId( 'restaurant_id' )->constrained()->onDelete( 'cascade' );
            $table->integer( 'rating' )->nullable();
            $table->integer( 'interest' )->default( 0 );
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
