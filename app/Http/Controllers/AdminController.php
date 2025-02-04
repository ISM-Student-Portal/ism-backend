<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\UserService;
use Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //

    public function __construct(
        protected UserService $userService
    ) {

    }

    public function login(Request $request)
    {
        $login = $request->input('email');
        $admin = Admin::where('email', $login)->orWhere('username', $login)->first();
        if (!$admin) {
            return response()->json([
                "message" => "invalid email or password"
            ], 401);
        }
        // dd($admin);
        $auth = Auth::guard("admin");
        
        if ($auth->attempt(["email" => $admin->email, "password" => $request->input("password")]) || $auth->attempt(["username" => $admin->username, "password" => $request->input("password")])) {
            $token = auth()->guard('admin')->user()->createToken('admin');
            return response()->json([
                "status" => "success",
                'user' => $admin,
                "token" => $token
            ], 201);
        }
        return response()->json([
            "message" => "invalid email or password"
        ], 401);
    }

    public function createSuperAdminUser()
    {
        $superAdmin = Admin::where('email', '=', 'super_admin@ism.com')->first();
        if ($superAdmin === null) {
            // dd('got here');
            $user = $this->userService->createSuperAdmin();
            return response()->json([
                "message" => "successful",
                "user" => $user
            ], 200);
        }
        return response()->json([
            "message" => "Super Admin exists already"
        ], 422);
    }

}
