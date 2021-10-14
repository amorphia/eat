<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/');

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
