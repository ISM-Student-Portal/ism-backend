<?php

namespace App\Http\Controllers;

use App\Events\NewAdminEvent;
use App\Events\NewLecturerEvent;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Student;
use App\Services\UserService;
use Auth;
use Illuminate\Http\Request;
use Str;

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
        if (!$admin->is_active) {
            return response()->json([
                "message" => "Account is inactive"
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
            ], 200);
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

    public function addAdmin(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'email' => 'required|email:rfc,dns|unique:admins',
                'username' => 'required|string|unique:admins',
                'phone' => 'required|string',
                'super_admin' => 'sometimes|boolean'
            ]);
            $password = Str::password(8, true, true, false, false);


            $admin = Admin::create([
                'email' => $validated['email'],
                'username' => $validated['username'],
                'phone_number' => $validated['phone'],
                'password' => bcrypt($password),
                'super_admin' => $validated['super_admin'] ?? false,
                'is_active' => true

            ]);
            event(new NewAdminEvent($admin, $password));

            return response()->json([
                "message" => "successful",
                "user" => $admin
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => 'fail',
                'message' => $th->getMessage()
            ], 400);
        }





    }

    public function addLecturer(Request $request)
    {
        try {
            //code...
            $validated = $request->validate([
                'email' => 'required|email:rfc,dns|unique:lecturers',
                'username' => 'required|string|unique:lecturers',
                'phone' => 'required|string'
            ]);
            $password = Str::password(8, true, true, false, false);


            $lecturer = Lecturer::create([
                'email' => $validated['email'],
                'username' => $validated['username'],
                'phone_number' => $validated['phone'],
                'password' => bcrypt($password),
                'is_active' => true

            ]);
            event(new NewLecturerEvent($lecturer, $password));

            return response()->json([
                "message" => "successful",
                "user" => $lecturer
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => 'fail',
                'message' => $th->getMessage()
            ], 400);
        }





    }

    public function createCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'lecturer_id' => 'sometimes|exists:lecturers,id'
        ]);
        // dd(auth()->user());

        $course = Course::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'lecturer_id' => $validated['lecturer_id'],
            'admin_id' => auth()->user()->id
        ]);

        return response()->json([
            "message" => "successful",
            "course" => $course
        ]);
    }
    public function updateCourse(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'lecturer_id' => 'sometimes|exists:lecturers,id'
        ]);
        // dd(auth()->user());

        $course = Course::findOrFail($id);
        $course->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'lecturer_id' => $validated['lecturer_id'],
            'admin_id' => auth()->user()->id
        ]);

        return response()->json([
            "message" => "successful",
            "course" => $course
        ]);
    }
    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return response()->json([
            "message" => "successful",
            "course" => $course
        ]);
    }

    public function deactivateAdmin($id)
    {
        $admin = Admin::findOrFail($id);
        if ($admin->is_active) {

            $admin->update([
                'is_active' => false
            ]);

        } else {
            $admin->update([
                'is_active' => true
            ]);
        }

        return response()->json([
            "message" => "successful",
            "admin" => $admin
        ]);
    }
    public function deactivateLecturer($id)
    {
        $lecturer = Lecturer::findOrFail($id);
        if ($lecturer->is_active) {

            $lecturer->update([
                'is_active' => false
            ]);

        } else {
            $lecturer->update([
                'is_active' => true
            ]);
        }
        return response()->json([
            "message" => "successful",
            "lecturer" => $lecturer
        ]);
    }

    public function deactivateStudent($id)
    {
        $lecturer = Student::findOrFail($id);
        if ($lecturer->is_active) {

            $lecturer->update([
                'is_active' => false
            ]);

        } else {
            $lecturer->update([
                'is_active' => true
            ]);
        }
        return response()->json([
            "message" => "successful",
            "lecturer" => $lecturer
        ]);
    }
    public function getAllLecturers()
    {

        $students = Lecturer::query()->get();
        return response()->json([
            "status" => "success",
            "lecturers" => $students
        ], 200);
    }
    public function allCourses()
    {
        $students = Course::query()->with(['lecturer'])->get();
        return response()->json([
            "status" => "success",
            "courses" => $students
        ], 200);
    }

    public function getAllAdmins()
    {
        $students = Admin::query()->get();
        return response()->json([
            "status" => "success",
            "admins" => $students
        ], 200);
    }


}
