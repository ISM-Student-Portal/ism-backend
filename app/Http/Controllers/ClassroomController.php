<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
use App\Imports\AttendanceImport;
use App\Models\Classroom;
use App\Models\User;
use App\Services\ClassroomService;
use Carbon\Carbon;
use Exception;
use Gate;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use Response;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(
        protected ClassroomService $classroomSevice
    ) {

    }
    public function index()
    {
        //



        $classes = $this->classroomSevice->all();
        return response()->json([
            'status' => "Successful",
            'data' => $classes
        ], 200);

    }
    public function getClassrooms()
    {
        $classrooms = Classroom::with([
            'attendance' => function ($query) {
                $query->whereHas('users', function (Builder $query) {
                    $query->where('attendance_user.user_id', '=', auth()->user()->id);
                });
            }
        ])->where('mentorship', '=', false)->orderBy('created_at', 'desc')->get();
        return response()->json([
            "message" => 'Success',
            "classrooms" => $classrooms
        ]);
    }
    public function getMentorship()
    {
        $classrooms = Classroom::with([
            'attendance' => function ($query) {
                $query->whereHas('users', function (Builder $query) {
                    $query->where('attendance_user.user_id', '=', auth()->user()->id);
                });
            }
        ])->where('mentorship', '=', true)->orderBy('created_at', 'desc')->get();
        return response()->json([
            "message" => 'Success',
            "classrooms" => $classrooms
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        if (!Gate::allows('create-user', auth()->user())) {
            return response()->json([
                "message" => "You are not an Admin"
            ], 403);
        }
        // $expiry = Carbon::now()->addHours(24)->toDateTimeString();

        $validated = $request->validate([
            "title" => "required|string",
            "description" => "sometimes|string",
            "link" => "required|string",
            "expires_on" => "sometimes|date",
            "mentorship" => "sometimes|boolean"

        ]);
        // $validated['expires_on'] = $expiry;
        $classroom = $this->classroomSevice->create($validated);
        return response()->json([
            'status' => "Successful",
            'classroom' => $classroom
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $classroom = Classroom::find($id);
        $classroom->attendance()->delete();
        $classroom->delete();
        return response()->json([
            "status" => "Success",
            "data" => $classroom
        ], 200);
    }

    public function markAttendance(Request $request, string $id)
    {
        $attendance = $this->classroomSevice->markAttendance($id);
        return response()->json([
            "status" => "Success",
            "data" => $attendance
        ], 200);
    }
    public function bulkAttendanceMark(Request $request)
    {
        set_time_limit(0);
        $classId = $request->input('id');

        $classroom = Classroom::findOrFail($classId);
        $existingAttendance = $classroom->attendance()->create([]);
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls',
            ]);
            // ...
            $file = $request->file;
            $array = Excel::toArray(new AttendanceImport, $file);
            $list = $array[0];
            foreach ($list as $entry) {
                if ($entry[0] === "Matric Number") {
                    continue;
                }
                if ($entry[0] === null) {
                    break;
                }

                # code...
                try {
                    $user = User::where('reg_no', '=', $entry[0])->first();
                    if (!is_null($user)) {
                        $user->attendances()->attach($existingAttendance->id);

                    }
                    //code...
                    // $user = User::where('reg_no', '=', )

                } catch (Exception $e) {
                    //throw $th;
                    $errors = [];
                    array_push($errors, $e);
                }
            }
            return response()->json([
                "message" => "Attendance Marked Successfully",
                "errors" => $errors ?? []
            ], 200);

        }
    }


    public function getClassAttendance(string $id)
    {
        $attendance = $this->classroomSevice->getClassAttendance($id);
        return response()->json([
            "status" => "Success",
            "data" => $attendance
        ], 200);
    }

    public function exportClassAttendance(string $id)
    {
        return (new AttendanceExport($id))->download('attendance.xlsx');

    }
}
