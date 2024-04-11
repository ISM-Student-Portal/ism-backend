<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public function users(){
        return $this->belongsToMany(User::class)->as('user_attendance');
    }

    public function classroom(){
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }
}
