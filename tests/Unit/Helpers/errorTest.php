<?php

namespace Tests\Unit\Helpers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class errorTest extends TestCase
{
    /**
     * @return void
     */
    public function test_returns_403_action_forbidden_by_default()
    {
        $response = error();

        $this->assertTrue( $response->getStatusCode() === 403 );
        $this->assertTrue( $response->getData()->error === 'Action Forbidden' );
    }

    /**
     * @return void
     */
    public function test_returns_custom_status_code_and_message()
    {

        $response = error( 'Warning: A Conflict Prevented this Request', 409 );

        $this->assertTrue( $response->getStatusCode() === 409 );
        $this->assertTrue( $response->getData()->error === 'Warning: A Conflict Prevented this Request' );
    }
}
