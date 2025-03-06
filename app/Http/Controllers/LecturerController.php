<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Lecturer;
use Auth;
use Illuminate\Http\Request;

class LecturerController extends Controller
{
    //
    public function login(Request $request)
    {
        $login = $request->input('email');
        $admin = Lecturer::where('email', $login)->orWhere('username', $login)->first();
        if (!$admin) {
            return response()->json([
                "message" => "invalid email or password"
            ], 401);
        }
        // dd($admin);
        $auth = Auth::guard("lecturer");

        if ($auth->attempt(["email" => $admin->email, "password" => $request->input("password")]) || $auth->attempt(["username" => $admin->username, "password" => $request->input("password")])) {
            $token = auth()->guard('lecturer')->user()->createToken('lecturer');
            return response()->json([
                "status" => "success",
                'user' => $admin,
                "token" => $token
            ], 200);
        }
        return response()->json([
            "message" => "invalid email or password"
        ], 401);
    }

    

  

}
