<?php

namespace Tests\Feature\Controllers;

use App\Models\Location;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class LocationControllerTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs( User::factory()->create( [ 'admin' => 1 ] ) );
    }


    /**
     * @return void
     */
    public function test_can_create_location_from_yelp_id()
    {
        $this->withoutExceptionHandling();
        $yelp_id = 'RtrZohTzYi4EK8vqmioANg';

        $url = route( 'locations.createFromYelp', ['mode' => 'id'] );
        $response = $this->post( $url, [ 'yelp_id' => $yelp_id ]);

        $this->assertDatabaseHas('locations', [ 'slug' => 'd-antonios-pizza-lodi-lodi' ]);
        $this->assertDatabaseHas('restaurants', [ 'name' => "D'antonio's Pizza Lodi" ]);
    }


    /**
    * @return void
    */
    public function test_can_create_location_from_yelp_url()
    {
        $yelp_url = 'https://www.yelp.com/biz/d-antonios-pizza-lodi-lodi';

        $url = route( 'locations.createFromYelp', ['mode' => 'page'] );
        $response = $this->post( $url, [ 'yelp_id' => $yelp_url ]);

        $this->assertDatabaseHas('locations', [ 'yelp_url' => $yelp_url ]);
        $this->assertDatabaseHas('restaurants', [ 'name' => "D'antonio's Pizza Lodi" ]);
    }


    /**
    * @return void
    */
    public function test_prevents_creation_of_duplicate_locations()
    {
        $yelp_url = 'https://www.yelp.com/biz/d-antonios-pizza-lodi-lodi';

        Location::factory()->create([ 'yelp_url' => $yelp_url ]);

        $url = route( 'locations.createFromYelp', ['mode' => 'page'] );
        $response = $this->post( $url, [ 'yelp_id' => $yelp_url ]);

        $response->assertStatus(403 );
    }


    /**
    * @return void
    */
    public function test_can_close_location_of_restaurant_with_one_location()
    {
        $restaurant = Restaurant::factory()->create();
        $location = Location::factory()->create(['restaurant_id' => $restaurant->id]);

        $response = $this->delete( route( 'locations.destroy', ['location' => $location->id] ) );

        $this->assertDatabaseHas('locations', [ 'id' => $location->id, 'active' => false ]);
        $this->assertDatabaseHas('restaurants', [ 'id' => $restaurant->id, 'active' => false ]);
    }


    /**
     * @return void
     */
    public function test_can_close_location_of_restaurant_with_multiple_location()
    {
        $restaurant = Restaurant::factory()->create();
        $locations = Location::factory( 3 )->create(['restaurant_id' => $restaurant->id]);

        $response = $this->delete( route( 'locations.destroy', ['location' => $locations->first()->id] ) );

        $this->assertDatabaseHas('locations', [ 'id' => $locations->first()->id, 'active' => false ]);
        $this->assertDatabaseHas('restaurants', [ 'id' => $restaurant->id, 'active' => true ]);
    }

}
