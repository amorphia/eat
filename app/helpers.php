<?php

/**
 * Shorthand to return a json error response
 *
 * @param string $message
 * @param int $status
 * @return \Illuminate\Http\JsonResponse
 */
function error( string $message = 'Action Forbidden', int $status = 403 )
{
    return response()->json(['error' => $message], $status );
}


/**
 * Shorthand to return the currently authenticated user for the current request
 *
 * @return \App\Models\User
 */
function user()
{
    return request()->user();
}


/**
 * Remove all parameters from a url string
 *
 * @param string $url
 * @return string
 */
function stripUrlParams( string $url ) : string
{
    $arr = explode("?", $url );
    return $arr[0];
}
