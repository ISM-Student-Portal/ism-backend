<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;


class TranscriptService
{
    public function __construct(
    ) {
    }

    public static function getStudentTranscript(Student $student)
    {
        $courses = Course::with([
            'assignments' => function (Builder $query) use ($student) {
                $query->with([
                    'submissions' => function (Builder $query) use ($student) {
                        $query->select('id', 'assignment_id', 'student_id', 'grade')
                            ->where('student_id', $student->id);
                    }
                ]);
            }

        ])->get();

        $transcript = [];

        foreach ($courses as $course) {
            $totalAssignments = $course->assignments()->count();
            $submittedAssignments = $course->assignments()
                ->whereHas('submissions', function (Builder $query) use ($student) {
                    $query->where('student_id', $student->id);
                })->count();
            $grade = $course->assignments()->where('use_for_transcript', true)->first();
            if (!$grade) {
                $transcript[] = [
                    'course_name' => $course->title,
                    'lecturer' => $course->lecturer->username ?? 'N/A',
                    'total_assignments' => $totalAssignments,
                    'grade' => 0,
                    'submitted_assignments' => $submittedAssignments,
                    // 'grade' => $grade
                ];
                continue; // Skip if no assignments are marked for transcript
            }

            $grade = $grade->submissions()
                ->where('student_id', $student->id)
                ->sum('grade');
            $transcript[] = [
                'course_name' => $course->title,
                'lecturer' => $course->lecturer->username ?? 'N/A',
                'total_assignments' => $totalAssignments,
                'grade' => $grade,
                'submitted_assignments' => $submittedAssignments,
                // 'grade' => $grade
            ];
        }

        return $transcript;
    }

    public static function getLecturerTranscript(Lecturer $lecturer, int $courseId)
    {
        $courses = Course::with([
            'assignments' => function ($query) {
                $query->with([
                    'submissions' => function ($query) {
                        $query->select('id', 'assignment_id', 'student_id', 'grade')
                            ->with([
                                'student' => function ($query) {
                                    $query->where('matric_no', '!=', null)->select('id', 'email', 'matric_no', 'first_name', 'last_name');
                                }
                            ]);
                    }
                ])->where('use_for_transcript', true)->select('id', 'course_id', 'title', 'use_for_transcript');
            }
        ])->where('id', $courseId)->first();


        return $courses;
    }

    public static function getStudentAttendance(int $courseId)
    {
        $attendance = Classroom::with([
            'attendance' => function ($query) {
                $query->with([
                    'students' => function ($query) {
                        $query->select('email', 'matric_no', 'first_name', 'last_name');
                    }
                ]);
            }
        ])->where('course_id', $courseId)->get();

        return $attendance;
    }

    public static function getStudentDashboardStats()
    {
        $assignmentCount = Assignment::count();
        $classes = Classroom::count();
        $assignmentSubmitted = Submission::where('student_id', '=', auth()->user()->id)->count();
        $aggregateGrades = Submission::where('student_id', '=', auth()->user()->id)->sum('grade');
        $percentGrades = round($aggregateGrades / 800 * 100, 2);

        return [
            'total_assignment' => $assignmentCount,
            'classes' => $classes,
            'assign_sub' => $assignmentSubmitted,
            'aggregate_grade' => $aggregateGrades,
            'percent_grade' => $percentGrades
        ];
    }


}