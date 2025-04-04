<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Student;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;


class AdminService
{
    public function __construct(
    ) {
    }

    public static function getDashboardStats()
    {
        $studentCount = Student::count();
        $classes = Course::count();
        $basicSub = Student::where('plan', '=', 'basic')->count();
        $premiumSub = Student::where('plan', '=', 'premium')->count();
        $paidStudent = Student::where('payment_complete', 1)->orWhere('balance', '!=', null)->count();
        $partPaymentStudent = Student::where('balance', '!=', null)->count();
        return [
            'students' => $studentCount,
            'classes' => $classes,
            'basicSub' => $basicSub,
            'premiumSub' => $premiumSub,
            'paidStudent' => $paidStudent,
            'partPaymentStudent' => $partPaymentStudent
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