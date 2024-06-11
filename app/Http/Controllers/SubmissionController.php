<?php

namespace App\Http\Controllers;

use App\Imports\AssignmentSubmissionImport;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


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

    public function bulkSubmission(Request $request)
    {
        set_time_limit(0);
        $examId1 = $request->input('id1');
        $examId2 = $request->input('id2');

        $exam1 = Assignment::find($examId1);
        $exam2 = Assignment::find($examId2);

        // dd($exam);

        // $existingAttendance = $exam->submissions()->create([]);
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls',
            ]);
            // ...
            $file = $request->file;
            $import = new AssignmentSubmissionImport();
            $import->onlySheets(1, 2);
            $array = Excel::toArray($import, $file);
            $sheets = $array;
            foreach ($sheets as $key => $sheet) {
                $exam = $key == 1 ? $exam1 : $exam2;
                foreach ($sheet as $entry) {
                    if ($entry[0] === "# reg_no") {
                        continue;
                    }
                    if ($entry[0] === null) {
                        break;
                    }

                    # code...
                    try {
                        $user = User::where('reg_no', '=', $entry[0])->first();
                        if (!is_null($user)) {
                            $exam->submissions()->create([
                                "student_id" => $user->id,
                                "link" => $entry[1],
                            ]);

                        }
                        //code...
                        // $user = User::where('reg_no', '=', )

                    } catch (Exception $e) {
                        //throw $th;
                        $errors = [];
                        array_push($errors, $e);
                    }
                }

            }
            return response()->json([
                "message" => "Attendance Marked Successfully",
                "errors" => $errors ?? []
            ], 200);

        }
    }
}
