<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\customer\productResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function index(Request $request){
        $products = Product::where('is_deleted','=',0);
        if($request->query('brand_id')){
            $products->where('brand_id',$request->query('brand_id'));
        }
        if($request->query('category_id')){
            $products->where('category_id',$request->query('category_id'));
        }
        if($request->query('first_category')){
            $products->where('first_category',$request->query('first_category'));
        }
        if($request->query('size')){
            $products->where('size',$request->query('size'));
        }
        $products = $products->get();
        if($products->isEmpty()){
            return response()->json([
                'success' => false,
                'message' => 'there are not any products'
            ],404);
        }
        return response()->json([
            'success' => true,
            'products' => ProductResource::collection($products)
        ],200);
    }


    public function show($id){
        $product = Product::find($id);
        
        if (!$product || $product->is_deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => new ProductResource($product)
        ], 200);
        
    }
    public function search(Request $request){
        $query = $request->query('query');
        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'enter any word for search'
            ], 400);
        }
        $products = Product::where('is_deleted', '=', '0')->where('name', 'LIKE', "%$query%")->get();
        if ($products->isEmpty()) {
            return response()->json([
                'success' =>false,
                'message' => 'there is not any products'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'products' => productResource::collection($products)
        ], 200);
    }
}
