<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function attendance(){
        return $this->hasOne(Attendance::class);
    }

    public function toArray(){
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "link" => $this->link,
            "expires_on" => $this->expires_on,
            "attendance" => $this->attendance ?? null

        ];
    }
}
