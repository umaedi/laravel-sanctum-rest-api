<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validateData = $request->validate([
            'name'  => 'required',
            'email'     => 'required|unique:users',
            'password'  => 'required|min:6'
        ]);
        $validateData['password'] = Hash::make($validateData['password']);
        $user = User::create($validateData);
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required',
            'password'  => 'required'
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Login failed!'], 401);
        }
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function profile(Request $request)
    {
        return response()->json(['data' => $request->user()]);
    }

    public function logout()
    {
        // // Revoke all tokens...
        // $user->tokens()->delete();

        // // Revoke the token that was used to authenticate the current request...
        // $request->user()->currentAccessToken()->delete();

        // // Revoke a specific token...
        // $user->tokens()->where('id', $tokenId)->delete();
        $user = User::findOrfail(auth()->user()->id);
        $user->tokens()->delete();
        return response()->json(['messgae', 'Logout success!']);
    }
}
