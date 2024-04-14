<?php

namespace App\Http\Controllers;

use App\Imports\UserEmailImport;
use App\Mail\NewUser;
use App\Models\User;
use App\Services\AdminService;
use App\Services\UserService;
use Exception;
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
        ]);
        $password = Str::password(8, true, true, false, false);
        $validated["password"] = $password;
        $user = $this->userService->create($validated);
        $user->profile()->create([
            "last_name" => $request->input('last_name'),
            "phone" => $request->input('phone_number'),
            "first_name" => $request->input('first_name'),
        ]);
        // $user["gen_pass"] = $password;
        Mail::to($user)->send(new NewUser($user, $password));
        return response()->json([
            "message" => "successful",
            "user" => $user
        ], 200);
    }

    public function getStudents()
    {
        if (!Gate::allows('create-user', auth()->user())) {
            return response()->json([
                "message" => "You are not an Admin"
            ], 403);
        }
        ;
        $students = $this->userService->getStudents();
        return response()->json([
            "message" => "successful",
            "students" => $students
        ], 200);
    }

    public function createProfile(Request $request)
    {
        $validated = $request->validate([
            "first_name" => "sometimes|string",
            "last_name" => "sometimes|string",
            "phone_number" => 'sometimes|unique:profiles,phone_number',
            "address" => "sometimes|string",
            "profile_pix_url" => 'sometimes|string',
            "country" => 'sometimes|string',
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
            "address" => "sometimes",
            "profile_pix_url" => 'sometimes',
            "country" => 'sometimes',
            "middle_name" => 'sometimes',
            "last_name" => 'sometimes',
            "first_name" => 'sometimes',
            "city" => 'sometimes',
            "alt_email" => 'sometimes',
            "alt_phone" => 'sometimes',
            "name_on_cert" => 'sometimes'
        ]);
        $profile = $this->userService->updateProfile($validated);
        $user = User::with('profile')->find(auth()->user()->id);

        return response()->json([
            "status" => 'success',
            "message" => "Profile Updated Successfully",
            "user_profile" => $user
        ], 200);

    }

    public function updateProfilePix(Request $request)
    {
        $validated = $request->validate([
            'profile_pix_url' => 'sometimes'
        ]);
        $profile = $this->userService->updateProfile($validated);
        $user = User::with('profile')->find(auth()->user()->id);

        return response()->json([
            "status" => 'success',
            "message" => "Profile Pix Updated Successfully",
            "user_profile" => $user
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
    public function generateStudentReg()
    {
        //
        $latest = User::latest('created_at')->first();
        if (is_null($latest)) {
            $counter = 0;
        } else {
            $ar = (explode('/', $latest->reg_no));
            $counter = $ar[2];
        }

        $nextNum = intval($counter) + 1;

        if (strlen((string) $nextNum) === 1)
            $num = '00' . $nextNum;
        else if (strlen((string) $nextNum) === 2)
            $num = '0' . $nextNum;
        else if (strlen((string) $nextNum) >= 3)
            $num = $nextNum;
        $id = 'ISM/2024/' . $num;

        return $id;

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
            $list = $array[0];
            // dd($list);
            foreach ($list as $entry) {
                if ($entry[0] === "Registration Number") {
                    continue;
                }
                if ($entry[0] === null) {
                    break;
                }
                try {
                    $password = Str::password(8, true, true, false, false);
                    $data["password"] = $password;
                    $data["email"] = $entry[1];
                    $data["reg_no"] = $entry[0];
                    $user = $this->userService->create($data);

                    $user->profile()->create([
                        "last_name" => $entry[2],
                        "phone" => $entry[4],
                        "first_name" => $entry[3],
                        'subscription' => strtolower($entry[6]) == 'basic training' ? 'basic' : 'premium'
                    ]);
                    // $user['gen_pass'] = $password;
                    // dd($password);
                    Mail::to($user)->later(now()->addSeconds(3), new NewUser($user, $password));
                    // Mail::to($user)->send(new NewUser($user, $password));
                } catch (Exception $e) {
                    $errors = [];
                    array_push($errors, $e);
                }

                # code...
            }
            return response()->json([
                "message" => "Users created Successfully",
                "errors" => $errors ?? []
            ], 200);

        }
    }

    public function getDashboardStats()
    {
        $stats = AdminService::getDashboardStats();
        return response()->json([
            'status' => 'success',
            'stats' => $stats
        ], 200);
    }
    public function getStudentDashboardStats()
    {
        $stats = AdminService::getStudentDashboardStats();
        return response()->json([
            'status' => 'success',
            'stats' => $stats
        ], 200);
    }

    public function setAdminStatus($id, Request $request)
    {
        $res = $this->userService->setAdminStatus($id, $request->input());
        return response()->json([
            'status' => 'success',
            'stats' => $res
        ], 200);
    }

    public function setActiveStatus($id, Request $request)
    {
        $res = $this->userService->setActiveStatus($id, $request->input('is_active'));
        return response()->json([
            'status' => 'success',
            'stats' => $res
        ], 200);
    }
}
