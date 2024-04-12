<?php

namespace App\Repositories\User;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function all()
    {
        return User::all();
    }

    public function create(array $data)
    {
        // return User::create($data);
        
        return User::create([
            "email"=> $data["email"],            
            "password"=> bcrypt($data["password"]),
            "reg_no" => $data['reg_no']
        ]);
    } 


    public function createSuperAdmin()
    {
        return User::create([
            "email"=> 'super_admin@ism.com',            
            "password"=> bcrypt('password'),
            "is_admin" => true,
            "is_superadmin" => true,
            "profile_done" => true,
        ]);
    }

    public function update(array $data, $id)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function createProfile(array $data)
    {
        // dd(auth()->user()->id);
        $user = User::where('id', auth()->user()->id)->first();
        return $user->profile()->create($data);
    }

    public function updateProfile(array $data)
    {
        // dd(auth()->user()->id);
        $user = User::where('id', auth()->user()->id)->first();
        return $user->profile()->update($data);
    }

    public function getStudents()
    {
        $students = User::where('is_superadmin', '!=', 1)->with(['attendances', 'profile'])->latest()->get();
        return $students;
    }

    public function setAdminStatus($id, $data)
    {
        $student = User::where('id', '=', $id)->first();
        $student->is_admin = $data['is_admin'];
        $student->save();
        $student->profile->update([
            "subscription" => $data['subscription']
        ]);
        return $student;
    }

    public function setActiveStatus($id, $is_active)
    {
        $student = User::where('id', '=', $id)->first();
        $student->is_active = $is_active;
        $student->save();
        return $student;
    }
}