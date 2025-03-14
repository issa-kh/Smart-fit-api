<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    //
    public function index(){
        $brands = Brand::all();
        if($brands->isEmpty()){
            return response()->json([
                'success' => false,
                'message' => 'there are not any brands'
            ],404);
        }
        return response()->json([
            'success' => true, 
            'brands' => BrandResource::collection($brands)
        ],200);
    }
}
