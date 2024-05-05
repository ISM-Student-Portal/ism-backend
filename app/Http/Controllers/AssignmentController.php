<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Storage;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $assignment = Assignment::with([
            'submissions' => function ($query) {
                $query->with('student');
            }
        ])->latest()->get();
        return response()->json([
            "message" => 'Success',
            "assignments" => $assignment
        ]);
    }

    public function getAssignments()
    {
        //
        $assignment = Assignment::with([
            'submissions' => function ($query) {
                $query->with([
                    'student'
                ])->where('student_id', '=', auth()->user()->id);
            }
        ])->get();
        return response()->json([
            "message" => 'Success',
            "assignments" => $assignment
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
        if (!Gate::allows('create-user', auth()->user())) {
            return response()->json([
                "message" => "You are not an Admin"
            ], 403);
        }
        $validated = $request->validate([
            "title" => "required|string",
            "description" => "required|string",
            "link" => "sometimes",
            "deadline" => "sometimes|date",
            "file" => "sometimes|max:10240"
        ]);
        $adminId = auth()->user()->id;
        if ($request->hasFile('file')) {
            $originalName = $request->file->getClientOriginalName();
            $path = "assignments/$adminId/" . $originalName;
            Storage::disk('local')->put($path, file_get_contents($request->file));
            $validated['file_url'] = $path;
            unset($validated['file']);
        }
        $data = [
            "title" => $validated["title"],
            "description" => $validated["description"],
            "link" => $validated["link"] ?? null,
            "file_url" => $validated["file_url"] ?? null,
            "created_by" => $adminId

        ];
        if (array_key_exists('deadline', $validated)) {
            $dt = Carbon::create($validated['deadline']);
            $dt = $dt->toDateTimeString();
            // dd($dt);
            $data['deadline'] = $dt;
        }
        $assignment = Assignment::create($data);
        return response()->json([
            "message" => 'Success',
            "assignment" => $assignment
        ]);




    }

    public function downloadFile(Request $request)
    {
        $url = $request->input('file_url');
        if ($url) {
            return Storage::download($url);
        }
        return response()->json([
            "message" => "error"
        ], 405);
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assignment $assignment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        //
        $res = $assignment->delete();
        return response()->json(
            [
                'message' => "success",
                'data' => $res
            ]

        );
    }
}
