<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\vendor\PromotionResource;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $promotions = Promotion::where('vendor_id','=',Auth::user()->id);
        if($request->query('product_id')){
            $promotions->where('product_id','=',$request->query('product_id'));
        }
        
        $promotions = $promotions->orderBy('created_at','desc')->get();
        if($promotions->isEmpty()){
            return response()->json([
                'success' => false,
                'message' => 'there are not any promotions' 
            ],404);
        }
        return response()->json([
            'success' => true,
            'promotions' => PromotionResource::collection($promotions)
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|between:0,100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = User::find(Auth::user()->id);
        $promotion = Promotion::where('product_id','=',$request->product_id)->orderBy('created_at','desc')->first();
        if($promotion){
            if($promotion->end_date >= $request->start_date){
                return response()->json([
                    'success' => false,
                    'message' => 'promotion already exist within the same range or there is new promotion'
                ],400);
            }
        }
        $user->promotions()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'promotion created successfully'
        ], 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $promotion = Promotion::find($id);
        if(!$promotion){
            return response()->json([
                'success' => false,
                'message' => 'promotion is not found'
            ],404);
        }
        if(Auth::user()->id != $promotion->vendor_id){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ],403);
        }
        return response()->json([
            'success' => true,
            'promotion' => new PromotionResource($promotion)
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'discount_percentage' => 'sometimes|numeric|between:0,100',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'product_id' => 'sometimes|exists:products,id',
        ]);
    
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        
        $promotion = Promotion::find($id);
    
        
        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion not found'
            ], 404);
        }
        if(Auth::user()->id != $promotion->vendor_id){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ],403);
        }
    
        $existingPromotion = Promotion::where('product_id', $request->product_id)
                                      //->where('id', '!=', $id)
                                      ->orderBy('created_at', 'desc')
                                      ->first();
    
        
        if ($existingPromotion && $existingPromotion->end_date >= $request->start_date) {
            return response()->json([
                'success' => false,
                'message' => 'A promotion already exists within this date range or there is new promotion'
            ], 400);
        }
    
        $promotion->update($request->all());
    
        return response()->json([
            'success' => true,
            'message' => 'Promotion updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $promotion = Promotion::find($id);
        if(!$promotion){
            return response()->json([
                'success' => false,
                'message' => 'promotion is not found'
            ],404);
        }
        if(Auth::user()->id != $promotion->vendor_id){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ],403);
        }
        $promotion->delete();
        return response()->json([
            'success' => true,
            'message' => 'promotion deleted'
        ],200);
    }
}
