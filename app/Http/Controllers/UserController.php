<?php

namespace App\Http\Controllers;

use App\Mail\NewUser;
use App\Models\User;
use App\Services\UserService;
use Gate;
use Illuminate\Http\Request;
use Mail;
use Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct(
        protected UserService $userService
     ){

     }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createSuperAdminUser(){
        $superAdmin = User::where('email', '=', 'super_admin@ism.com')->first();
        if($superAdmin === null){
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

    public function createUser(Request $request){
        if(! Gate::allows('create-user', auth()->user())){
            return response()->json([
                "message" => "You are not an Admin"
            ], 403);
        }
        $validated = $request->validate([
            "email" => "required|email|unique:users,email",
            "phone_number" => 'required|unique:users,phone_number',
            "first_name" => 'required|string',
            "last_name" => 'required|string',
        ]);
        $password = Str::password(8, true, true, true, false);
        $validated["password"] = $password;
        $user = $this->userService->create($validated);
        $user["gen_pass"] = $password;
        Mail::to($user)->send(new NewUser($user));
        return response()->json([
            "message" => "successful",
            "user" => $user
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
