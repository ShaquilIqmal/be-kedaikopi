<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{

    public function register(Request $request){

        $field = $request->validate([
            'username' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::create($field);

        $token = $user->createToken($request->email);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credentials are incorrect.'
            ], 401);
        }

        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }


    public function logout(Request $request){
        
        $request->user()->tokens()->delete();

        return 'Logged Out';
    }

    public function index(Request $request){
        
        $user = User::get();

        return response()->json([
            'user' => $user
        ]);
    }


}
