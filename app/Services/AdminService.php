<?php

namespace App\Services;
use App\Models\Classroom;
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


}