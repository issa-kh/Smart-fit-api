<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\vendor\ProductResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        //return $request->query('brand_id');
        $products = Product::where('is_deleted','=',0)
                            ->where('vendor_id',Auth::user()->id);
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'size' => 'required|in:small,meduim,large',
            'color' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image',
            'first_category' => 'required|in:male,female',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ],422);
        }
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public'); 
            
        }
        $user = User::find(Auth::user()->id);
        $user->products()->create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'size' => $request->size,
            'color' => $request->color,
            'stock' => $request->stock,
            'image_url' => $imagePath, 
            'first_category' => $request->first_category,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
        ]);
        
        
    
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully!',
        ], 201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $product = Product::find($id);

        
        if (!$product || $product->is_deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found!'
            ], 404);
        }
        if (Auth::id() !== $product->vendor_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action!',
            ], 403);
        }
    
        
        return response()->json([
            'success' => true,
            'product' => new ProductResource($product)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $product = Product::find($id);

    if (!$product || $product->is_deleted) {
        return response()->json([
            'success' => false,
            'message' => 'Product not found!',
        ], 404);
    }

    
    if (Auth::id() !== $product->vendor_id) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized action!',
        ], 403);
    }

    
    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|string|max:255',
        'description' => 'nullable|string',
        'price' => 'sometimes|numeric|min:0',
        'size' => 'sometimes|in:small,meduim,large',
        'color' => 'sometimes|string|max:50',
        'stock' => 'sometimes|integer|min:0',
        'image' => 'nullable|image',
        'first_category' => 'sometimes|in:male,female',
        'category_id' => 'sometimes|exists:categories,id',
        'brand_id' => 'sometimes|exists:brands,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    
    $product->update($request->only([
        'name', 'description', 'price', 'size', 'color', 
        'stock', 'first_category', 'category_id', 'brand_id'
    ]));

    
    if ($request->hasFile('image')) {
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }
        
        
        $product->image_url = $request->file('image')->store('products', 'public');
        $product->save();
    }

    return response()->json([
        'success' => true,
        'message' => 'Product updated successfully!',
    ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'success' => false,
                'message' => 'product is not found'
            ],404);
        }
        if($product->promotions != null)
        $product->promotions()->delete();

        if (!$product || $product->is_deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found!',
            ], 404);
        }
    
        
        if (Auth::id() !== $product->vendor_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action!',
            ], 403);
        }
        $product->is_deleted = true;
        $product->save();
        return response()->json([
            'success' => true, 
            'message' => 'product deleted successfully'
        ],200);

    }
    public function search(Request $request){

        $query = $request->query('query');
        if (!$query) {
            return response()->json([
                'message' => 'enter any word for search'
            ], 400);
        }
        $products = Product::where('is_deleted', '=', '0')->where('vendor_id','=',Auth::user()->id)->where('name', 'LIKE', "%$query%")->get();
        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'there is not any products'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'products' => ProductResource::collection($products)
        ], 200);
    }
}
