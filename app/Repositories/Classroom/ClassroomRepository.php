<?php

namespace App\Repositories\Classroom;

use App\Models\Classroom;

use Carbon\Carbon;

class ClassroomRepository implements ClassroomRepositoryInterface
{
    public function all()
    {
        return Classroom::latest()->get();
    }

    public function create(array $data)
    {
        // return User::create($data);

        if (array_key_exists('expires_on', $data)) {
            $dt = Carbon::create($data['expires_on']);
            $dt = $dt->toDateTimeString();
        }
        return Classroom::create([
            "course_id" => $data['course_id'],
            "title" => $data["title"],
            "description" => $data["description"],
            "link" => $data["link"],
            "expires_on" => $dt ?? null,
        ]);
    }




    public function update(array $data, $id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->update($data);
        return $classroom;
    }

    public function delete($id)
    {
        $user = Classroom::findOrFail($id);
        $user->delete();
    }

    public function find($id)
    {
        return Classroom::findOrFail($id);
    }






}