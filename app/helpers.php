<?php

/*
 *  Shorthand to return a json error response
 *  string $message
 *  int $status
 */
function error( string $message = 'Action Forbidden', int $status = 403 )
{
    return response()->json(['error' => $message], $status );
}

/*
 *  Shorthand to return the currently authenticated user for the current request
 */
function user()
{
    return request()->user();
}
