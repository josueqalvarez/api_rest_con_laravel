<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request){
        $this->validateLogin($request);
    
        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'token' => $request->user()->createToken($request->user()->name)->plainTextToken,
                'message' => 'Success',
                'user' => Auth::user()
            ], 200);
        } 

        return response()->json([
            'message' => 'Login failed'
        ], 401);

    }

    public function validateLogin(Request $request){
        return $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'name' => 'required',
            ]);     
    }
}
