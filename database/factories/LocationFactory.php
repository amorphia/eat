<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * The default hours entries as a JSON string
     *
     * @var string
     */
    protected $default_hours = "[
        {
            \"day\": 2,
            \"end\": \"2100\",
            \"start\": \"1700\",
            \"is_overnight\": false
        },
        {
            \"day\": 3,
            \"end\": \"2100\",
            \"start\": \"1700\",
            \"is_overnight\": false
        },
        {
            \"day\": 4,
            \"end\": \"2200\",
            \"start\": \"1700\",
            \"is_overnight\": false
        },
        {
            \"day\": 5,
            \"end\": \"2200\",
            \"start\": \"1700\",
            \"is_overnight\": false
        },
        {
            \"day\": 6,
            \"end\": \"2100\",
            \"start\": \"1700\",
            \"is_overnight\": false
        }
    ]";

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->unique()->text( rand( 10, 20 ) );
        $name = preg_replace("/(\W)+/", "", $name );

        return [
            'yelp_id' => $this->faker->uuid,
            'phone' => $this->faker->e164PhoneNumber,
            'slug' => str_replace( " ", "-", $name ),
            'name' => $name,
            'yelp_url' => "https://www.yelp.com/biz/${name}",
            'latitude' => $this->faker->latitude( 39.9, 41.9 ),
            'longitude' => $this->faker->longitude( -82.0, -87.7 ),
            'street' => $this->faker->numberBetween( 2, 400 ) . " " . $this->faker->streetName,
            'city' => 'Columbus',
            'zip' => $this->faker->postcode,
            'hours' => $this->default_hours,
        ];
    }
}
