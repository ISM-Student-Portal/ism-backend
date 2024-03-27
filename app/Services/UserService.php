<?php

namespace App\Services;

use App\Repositories\User\UserRepositoryInterface;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {
    }

    public function create(array $data)
    {
        return $this->userRepository->create($data);
    }

    public function createSuperAdmin()
    {
        return $this->userRepository->createSuperAdmin();
    }

    public function update(array $data, $id)
    {
        return $this->userRepository->update($data, $id);
    }

    public function delete($id)
    {
        return $this->userRepository->delete($id);
    }

    public function all()
    {
        return $this->userRepository->all();
    }
    
    public function find($id)
    {
        return $this->userRepository->find($id);
    }

    public function createProfile(array $data)
    {
        return $this->userRepository->createProfile($data);
    }

    public function updateProfile(array $data){
        return $this->userRepository->updateProfile($data);
    }


}