<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    public function index(){
        $categories = Category::all();
        if($categories->isEmpty()){
            return response()->json([
                'success' => false,
                'message' => 'there are not any categories'
            ],404);
        }
        return response()->json([
            'success' => true,
            'categories' => CategoryResource::collection($categories)
        ],200);
    }
}
