<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;


class CategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index() : Collection
    {
        return Category::where( 'active', true )
            ->has( 'restaurants', '>=', 3 )
            ->orderBy( 'priority', 'desc' )
            ->orderBy( 'name', 'asc' )->get();
    }

}
