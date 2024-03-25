<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    
    public function login(Request $request){
        $validated = $request->validate([
            'email'=> 'required|email',
            'password'=>'required'
        ]);
        if(Auth::attempt($validated)){
            $token = auth()->user()->createToken('user');
            return response()->json([
                "status" => "success",
                "token" => $token
            ], 201);
        }
        return response()->json([
            "message"=> "invalid email or password"
        ], 401);
    }

    
}
