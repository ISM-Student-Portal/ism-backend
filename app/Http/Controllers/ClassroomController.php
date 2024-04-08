<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Services\ClassroomService;
use Gate;
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

        if (!Gate::allows('create-user', auth()->user())) {
            return response()->json([
                "message" => "You are not an Admin"
            ], 403);
        }

        $classes = $this->classroomSevice->all();
        return response()->json([
            'status' => "Successful",
            'data' => $classes
        ], 200);
        
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

        $validated = $request->validate([
            "title" => "required|string",
            "description" => "sometimes|string",
            "link" => "required|string",
            "expires_on" =>"sometimes|date"
        ]);
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
    public function destroy(Classroom $classroom)
    {
        //
    }
}
