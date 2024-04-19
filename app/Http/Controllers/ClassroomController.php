<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Services\ClassroomService;
use Carbon\Carbon;
use Gate;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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
                $query->whereHas('users', function(Builder $query) {
                    $query->where('attendance_user.user_id', '=', auth()->user()->id);
                });
            }
        ])->orderBy('created_at', 'desc')->get();
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
            "expires_on" =>"sometimes|date"
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

    public function getClassAttendance(string $id)
    {
        $attendance = $this->classroomSevice->getClassAttendance($id);
        return response()->json([
            "status" => "Success",
            "data" => $attendance
        ], 200);
    }
}
