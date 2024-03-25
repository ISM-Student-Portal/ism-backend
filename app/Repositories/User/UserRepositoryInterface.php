<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function all();

    public function create(array $data);

    public function createSuperAdmin();

    public function update(array $data, $id);

    public function delete($id);

    public function find($id);

    
}