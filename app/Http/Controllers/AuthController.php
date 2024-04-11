<?php

namespace App\Http\Controllers;
use App\Models\User;
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
        $validated['is_active'] = 1;
        if(Auth::attempt($validated)){
            $token = auth()->user()->createToken('user');
            $user = User::where('id',auth()->user()->id )->with(['attendances', 'profile'])->first();
            return response()->json([
                "status" => "success",
                'user'=> $user,
                "token" => $token
            ], 201);
        }
        return response()->json([
            "message"=> "invalid email or password"
        ], 401);
    }

    
}
