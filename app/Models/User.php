<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function profile(){
        return $this->hasOne(Profile::class);
    }
    public function attendances(){
        return $this->belongsToMany(Attendance::class) ->as('user_attendance');
    }
    

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function toArray(){
        return [
            'id' => $this->id,
            'email'=> $this->email,
            'is_admin'=> $this->is_admin,
            'profile_done'=>$this->profile_done,

            'is_superadmin'=>$this->is_superadmin,
            'profile'=>$this->profile
        ];
    }
}
