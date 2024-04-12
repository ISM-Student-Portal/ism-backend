<?php

namespace App\Services;
use App\Models\Assignment;
use App\Models\Classroom;
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
        $studentCount = User::where('is_admin', '=', 0)->count();
        $classes = Classroom::count();
        $basicSub = User::with('profile')->whereHas('profile', function(Builder $query){
            $query->where('subscription', '=', 'basic');
        } )->count();
        $premiumSub = User::with('profile')->whereHas('profile', function(Builder $query){
            $query->where('subscription', '=', 'premium');
        } )->count();
        return [
            'students' => $studentCount,
            'classes' => $classes,
            'basicSub' => $basicSub,
            'premiumSub' => $premiumSub,
        ];
   }

   public static function getStudentDashboardStats()
   {
        $assignmentCount = Assignment::count();
        $classes = Classroom::count();
        $assignmentSubmitted = Submission::where('student_id', '=', auth()->user()->id)->count();
        
        return [
            'total_assignment' => $assignmentCount,
            'classes' => $classes,
            'assign_sub' => $assignmentSubmitted,
        ];
   }


}