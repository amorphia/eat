<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RatingController extends Controller
{

    /**
     * Update the specified rating in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rating  $rating
     * @return Rating|\Illuminate\Database\Eloquent\Model
     */
    public function update( Request $request, Restaurant $restaurant )
    {
        $validated = $request->validate([
            'rating' => 'integer',
            'interest' => 'integer',
            'viewed' => 'boolean'
        ]);

        // get or create rating
        $rating = Rating::firstOrCreate([
            'restaurant_id' => $restaurant->id,
            'user_id' => $request->user()->id
        ]);

        // update rating with new values then return
        $rating->update( $validated );
        return $rating;
    }

}
