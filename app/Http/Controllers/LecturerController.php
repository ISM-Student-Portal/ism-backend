<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Lecturer;
use App\Services\LecturerService;
use App\Services\TranscriptService;
use Auth;
use DB;
use GuzzleHttp\Psr7\Query;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller as BaseController;

class LecturerController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('auth:lecturer')->except(['login']);
    }
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

    public function getDashboardStats()
    {
        $stats = LecturerService::getDashboardStats();
        return response()->json([
            'status' => 'success',
            'stats' => $stats
        ], 200);
    }

    public function allCourses()
    {

        $courses = auth()->user()->courses->load(['assignments', 'classrooms']);
        return response()->json([
            "status" => "success",
            "courses" => $courses,

        ], 200);
    }

    public function coursesTranscript()
    {
        $courses = auth()->user()->courses->load(['assignments', 'classrooms']);
        $transcripts = [];
        foreach ($courses as $course) {
            $transcripts[] = [
                'course' => $course,
                'transcript' => auth()->user()->transcript($course->id)
            ];
        }
        return response()->json([
            "status" => "success",
            "transcripts" => $transcripts
        ], 200);
    }

    public function allClassrooms(Request $request)
    {
        $courses = auth()->user()->courses->pluck('id');
        $classrooms = Classroom::with([
            'attendance' => function (Builder $builder) {
                return $builder->with([
                    'students' => function (Builder $query) {
                        return $query->distinct();
                    }
                ]);
            },
            'course'
        ])->whereIn('course_id', $courses)->latest()->get();
        return response()->json([
            "status" => "success",
            "classrooms" => $classrooms
        ], 200);
    }

    public function getTranscript(Request $request, Course $course)
    {
        // dd($course);
        $lecturer = Lecturer::where('id', auth()->user()->id)->first();


        $transcript = TranscriptService::getLecturerTranscript($lecturer, $course->id);
        $attendance = TranscriptService::getStudentAttendance($course->id);
        // dd($attendance);
        if ($transcript) {
            return response()->json([
                "status" => "success",
                "transcripts" => [
                    "exam_result" => $transcript,
                    "attendance" => $attendance]
            ], 200);
        }
        return response()->json([
            "message" => "lecturer not found"
        ], 404);
    }

    public function allAssignments(Request $request)
    {
        $courses = auth()->user()->courses->pluck('id');
        $assignments = Assignment::with([
            'submissions' => function (Builder $query) {
                return $query->with([
                    'student' => function (Builder $query) {
                        return $query->distinct();
                    }
                ]);
            },
            'course'
        ])->whereIn('course_id', $courses)->latest()->get();
        return response()->json([
            "status" => "success",
            "assignments" => $assignments
        ], 200);
    }

    public function courseById(string $id)
    {

        $courses = Course::with([
            'classrooms' => function (Builder $query) {
                return $query->with([
                    'attendance' => function (Builder $builder) {
                        return $builder->with([
                            'students' => function (Builder $query) {
                                return $query->distinct();
                            }
                        ]);
                    }
                ])->latest();
            },

            'assignments' => function (Builder $query) {
                return $query->with([
                    'submissions' => function (Builder $query) {
                        return $query->with([
                            'student' => function (Builder $query) {
                                return $query->distinct();
                            }
                        ]);
                    }
                ])->latest();
            }
        ])->where('id', $id)->first();
        return response()->json([
            "status" => "success",
            "course" => $courses
        ], 200);
    }





}
