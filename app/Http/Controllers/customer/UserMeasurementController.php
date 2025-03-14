<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\customer\MeasurementResource;
use App\Models\Measurement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserMeasurementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'chest' => 'required|numeric',
            'waist' => 'required|numeric',
            'hips' => 'required|numeric',
            'gender' => 'required|in:male,female',
            
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ],422);
        }
        $user = User::find(Auth::user()->id);
        if($user->measurement){
            return response()->json([
                'success' => false,
                'message' => 'user measurements is already exist'
            ],400);
        }
        $measurement =  $user->measurement()->create($request->all());
    
       // $measurement = Measurement::create($request->all());
    
        return response()->json([
            'success' => true,
            'message' => 'Measurement created successfully',
            'measurement' => new MeasurementResource($measurement),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $measurement = Measurement::find($id);

    if (!$measurement) {
        return response()->json([
            'message' => 'Measurement not found',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'measurement' => new MeasurementResource($measurement),
    ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $measurement = Measurement::find($id);

    if (!$measurement) {
        return response()->json([
            'success' => false,
            'message' => 'Measurement not found',
        ], 404);
    }

    $validator = Validator::make($request->all(),[
        'height' => 'required|numeric',
        'weight' => 'required|numeric',
        'chest' => 'required|numeric',
        'waist' => 'required|numeric',
        'hips' => 'required|numeric',
        'gender' => 'required|in:male,female'
    ]);
    if($validator->fails()){
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ],422);
    }

    $measurement->update($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Measurement updated successfully',
        'data' => new MeasurementResource($measurement),
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
