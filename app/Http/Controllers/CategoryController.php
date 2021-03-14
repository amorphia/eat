<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Category::where( 'active', true )
            ->has( 'restaurants', '>=', 3 )
            ->orderBy( 'priority', 'desc' )
            ->orderBy( 'name', 'asc' )->get();
    }

}
