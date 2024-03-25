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
        $data["password"] = bcrypt("password");
        return User::create($data);
    } 


    public function createSuperAdmin()
    {
        return User::create([
            "email"=> 'super_admin@ism.com',
            "first_name" => "super",
            "last_name" => "admin",
            "phone_number" => "08135321769",
            "password"=> bcrypt('password'),
            "is_admin" => true,
            "is_superadmin" => true
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
}