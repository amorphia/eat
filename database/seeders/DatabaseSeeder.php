<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create categories
        $categories = Category::factory()
            ->count( 20 )
            ->create();

        // create restaurants
        $restaurants = Restaurant::factory()
            ->count( 100 )
            ->hasPhotos( 3 )
            ->hasLocations( random_int( 1, 2 ) )
            ->create();

        // assign categories to restaurants
        $restaurants->each( function ( $item, $key ) use ( $categories ) {
            $cats = $categories->random( 2 )->pluck( 'id' );
            $item->categories()->sync( $cats );
        });

    }
}
