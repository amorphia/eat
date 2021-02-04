<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\YelpSort;

class CreateYelpSortsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yelp_sorts', function (Blueprint $table) {
            $table->id();
            $table->string( 'sort' )->unique();
            $table->boolean( 'scanned' )->default( false );
            $table->timestamps();
        });

        $sorts = config( 'services.yelp.sorts' );
        foreach( $sorts as $sort  ){
            YelpSort::create([ 'sort' => $sort ]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yelp_sorts');
    }
}
