<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Lecturer extends Authenticatable
{
    //
    use HasFactory, Notifiable, HasApiTokens, CanResetPassword;

    protected $fillable = [
        'email',
        'password',
        'phone_number',
        'super_admin',
        'username'
    ];
}
