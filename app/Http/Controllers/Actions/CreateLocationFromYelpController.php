<?php

namespace App\Http\Controllers\Actions;

use App\Actions\CreateLocationFromYelp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateLocationFromYelpController extends Controller
{

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\Restaurant|\Illuminate\Http\JsonResponse|void
     */
    public function __invoke( Request $request, CreateLocationFromYelp $action, ?string $mode = 'id' )
    {
        try {
            $result = $action->execute( $request->yelp_id, $mode );
        } catch ( \Exception $e ){
            return error( $e->getMessage() );
        }

        return $result;
    }
}
