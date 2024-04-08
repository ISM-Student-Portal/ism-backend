<?php

namespace App\Services;

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






}