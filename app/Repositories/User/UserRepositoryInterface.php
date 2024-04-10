<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function all();

    public function create(array $data);

    public function createSuperAdmin();

    public function createProfile(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function find($id);

    public function updateProfile(array $data);

    public function getStudents();

    public function setAdminStatus($id, $is_admin);

    public function setActiveStatus($id, $is_active);


    
}