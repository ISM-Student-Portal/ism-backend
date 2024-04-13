<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // dd(auth()->user()->id);
        $submissions = Submission::with(['assignment'])->where('student_id', '=', auth()->user()->id)->get();
        return response()->json([
            "message" => 'Success',
            "submission" => $submissions
        ]);

    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            "assignment_id" => "required|exists:assignments,id",
            "link" => "required|string",
            "feedbacks" => "sometimes|string",
        ]);

        $assignment = Assignment::find($validated['assignment_id']);
        $assignment->submissions()->create([
            "student_id" => auth()->user()->id,
            "feedbacks" => $validated['feedbacks'] ?? null,
            "link" => $validated['link'],
        ]);
        $submission = Submission::where('student_id', '=', auth()->user()->id)->where('assignment_id', '=', $validated['assignment_id'])->first();
        return response()->json([
            "message" => 'Success',
            "submission" => $submission
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Submission $submission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Submission $submission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Submission $submission)
    {
        //
        $res = $submission->update([
            'grade' => $request->input('grade')
        ]);

        return response()->json([
            "message" => 'Success',
            "submission" => $res
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Submission $submission)
    {
        //
    }
}
