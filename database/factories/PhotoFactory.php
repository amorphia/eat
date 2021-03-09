<?php

namespace Database\Factories;

use App\Models\Photo;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Photo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'url' => "https://picsum.photos/1000/800?" . $this->faker->uuid
        ];
    }
}
