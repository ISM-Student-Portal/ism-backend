<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\User;
use App\Repositories\Classroom\ClassroomRepositoryInterface;

class ClassroomService
{
    public function __construct(
        protected ClassroomRepositoryInterface $classroomRepository
    ) {
    }

    public function create(array $data)
    {
        return $this->classroomRepository->create($data);
    }



    public function update(array $data, $id)
    {
        return $this->classroomRepository->update($data, $id);
    }

    public function delete($id)
    {
        return $this->classroomRepository->delete($id);
    }

    public function all()
    {
        return $this->classroomRepository->all();
    }

    public function find($id)
    {
        return $this->classroomRepository->find($id);
    }

    public function markAttendance($id)
    {
        $existingAttendance = Attendance::where('classroom_id', $id)->first();
        if (is_null($existingAttendance)) {
            $classroom = Classroom::findOrFail($id);
            $existingAttendance = $classroom->attendance()->create([]);
        }
        $user = User::find(auth()->user()->id);
        $user->attendances()->attach($existingAttendance->id);

        return $user->attendances;
        // $classroom->
    }

    public function getClassAttendance($id)
    {
        $attendance = Attendance::where('classroom_id', $id)->first();

        return $attendance->users()->distinct()->get();
    }








}