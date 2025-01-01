<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, CanResetPassword, HasUUID;

    //
    protected $guarded = [];

    public function payments()
    {
        return $this->hasMany(Payments::class);
    }
}
