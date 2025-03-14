<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => ['required','string','max:255'],
            'email' => ['required','email','unique:users'],
            'password' => ['required','min:8','max:255','confirmed'],
            'type' => ['required']
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
                }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
            'token' => $token
        ],201);
    }
    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => ['required','email'],
            'password' => ['required','string'],
            
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
                }
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'invalid credentials'
            ],403);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
            'token' => $token
        ],200);



    }
}
