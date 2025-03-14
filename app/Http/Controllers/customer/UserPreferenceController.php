<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\customer\UserPreferenceResource;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
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
            'brands' => ['json','required'],
            'colors' => ['json','required']
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }
        $user = User::find(Auth::user()->id);
        if($user->userPrefernces){
            return response()->json([
                'success' => false,
                'message' => 'user preference is already exist'
            ],400);
        }
        $user->userPrefernces()->create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'user preference created successfully'
        ],201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $userPreference = UserPreference::find($id);

    if (!$userPreference) {
        return response()->json([
            'success' => false,
            'message' => 'user preference not found',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'userPreference' => new UserPreferenceResource($userPreference),
    ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $userPreference = UserPreference::find($id);

        if (!$userPreference) {
            return response()->json([
                'success' => false,
                'message' => 'user preference not found',
            ], 404);
        }
    
        $validator = Validator::make($request->all(),[
           'brands' => ['required','json'],
           'colors' => ['required','json']
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ],422);
        }
    
        $userPreference->update($request->all());
    
        return response()->json([
            'success' => true,
            'message' => 'user preference updated successfully',
            
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
