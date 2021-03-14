<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {
        $validated = $request->validate([
            'body' => 'string',
            'restaurant_id' => 'exists:restaurants,id'
        ]);

        return user()->posts()->create( $validated );
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, Post $post )
    {
        if( !user()->can( 'update', $post ) ) return error();

        $validated = $request->validate([
            'body' => 'string',
        ]);

        // update with new values then return
        $post->update( $validated );

        return $post;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy( Post $post )
    {
        if( !user()->can( 'delete', $post ) ) return error();
        return $post->delete();
    }
}
