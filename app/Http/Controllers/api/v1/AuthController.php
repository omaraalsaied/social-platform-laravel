<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        if(Auth::attempt($credentials)) {
            $user = User::find(Auth::user()->id);

            $user_token['token'] = $user->createToken('appToken')->accessToken;

            return response()->json([
                'success' => true,
                'token' => $user_token,
                'user' => $user,
            ], 200);
        }
        return response()->json(['error'=>'Unauthourized'], 401);
    }

    public function register(Request $request)
    {

        $valid_data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255',
            'password'      => 'required|string|min:8',
            'phone'         => 'required|string|min:11|max:13',
        ]);

            $user = User::create($valid_data);
            $token = $user->createToken('Personal Access Token')->accessToken;
            return response()->json(['user' => $user, 'token' => $token], 201);

    }

    public function logout (Request $request){
        if (Auth::user()) {
            $request->user()->token()->revoke();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ], 200);
        }
    }
}
