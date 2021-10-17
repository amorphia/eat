<?php

namespace Tests\Unit\Helpers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class stripUrlParamsTest extends TestCase
{
    /**
     * @return void
     */
    public function test_can_strip_query_parameters()
    {
        $url = "https://fakeurl.com/?param=true&otherparam=false";
        $result = stripUrlParams( $url );
        $this->assertTrue( $result === 'https://fakeurl.com/');
    }

    /**
     * @return void
     */
    public function test_doesnt_modify_urls_without_parameters()
    {
        $url = "https://fakeurl.com/";
        $result = stripUrlParams( $url );
        $this->assertTrue( $result === 'https://fakeurl.com/');
    }


    /**
     * @return void
     */
    public function test_can_handle_malformed_urls()
    {
        $url = "https://fakeurl.com/?param=true&?otherparam=false";
        $result = stripUrlParams( $url );
        $this->assertTrue( $result === 'https://fakeurl.com/');
    }
}
