<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Student;
use App\Models\Submission;



class LecturerService
{
    public function __construct(
    ) {
    }

    public static function getDashboardStats()
    {
        //TODO create assignment count
        $course = Course::where('lecturer_id', '=', auth()->user()->id)->get()->pluck('id');
        $assignment = Assignment::whereIn('course_id', $course)->get()->pluck('id');
        $assignmentCount = Assignment::whereIn('course_id', $course)->count();
        $courses = auth()->user()->courses->count();
        $submissions = Submission::whereIn('assignment_id', $assignment)->count();
        $classrooms = Classroom::whereIn('course_id', $course)->count();
        return [
            'assignments' => $assignmentCount,
            'courses' => $courses,
            'submissions' => $submissions,
            'classrooms' => $classrooms
        ];
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