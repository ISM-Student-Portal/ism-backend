<?php

namespace App\Http\Controllers;

use App\Imports\UserEmailImport;
use App\Mail\NewUser;
use App\Models\User;
use App\Services\UserService;
use Gate;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(
        protected UserService $userService
    ) {

    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createSuperAdminUser()
    {
        $superAdmin = User::where('email', '=', 'super_admin@ism.com')->first();
        if ($superAdmin === null) {
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

    public function createUser(Request $request)
    {
        if (!Gate::allows('create-user', auth()->user())) {
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

    public function createProfile(Request $request)
    {
        $validated = $request->validate([
            "address" => "required|string",
            "profile_pix_url" => 'required|string',
            "country" => 'required|string',
        ]);
        $profile = $this->userService->createProfile($validated);

        return response()->json([
            "message" => "Profile Created Successfully",
            "user_profile" => $profile
        ], 200);

    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            "address" => "sometimes|string",
            "profile_pix_url" => 'sometimes|string',
            "country" => 'sometimes|string',
        ]);
        $profile = $this->userService->updateProfile($validated);

        return response()->json([
            "message" => "Profile Updated Successfully",
            "user_profile" => $profile
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
    public function batchCreateUser(Request $request)
    {

        if (!Gate::allows('create-user', auth()->user())) {
            return response()->json([
                "message" => "You are not an Admin"
            ], 403);
        }
        // dd("I got here");



        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls',
            ]);
            // ...
            $file = $request->file;
            // $path=storage_path('app').'/'.$file;
            $array = Excel::toArray(new UserEmailImport, $file);
            dd($array[0]);
            return response()->json([], 200);

        }






    }
}