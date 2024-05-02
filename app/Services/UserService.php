<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

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

    public function updateProfile(array $data)
    {
        return $this->userRepository->updateProfile($data);
    }

    public function getStudents()
    {
        return $this->userRepository->getStudents();
    }

    public function getAdmins()
    {
        return $this->userRepository->getAdmins();
    }

    public function setAdminStatus($id, $is_admin)
    {
        return $this->userRepository->setAdminStatus($id, $is_admin);
    }
    public function setActiveStatus($id, $is_active)
    {
        return $this->userRepository->setActiveStatus($id, $is_active);
    }

    public function attendanceReport()
    {
        $totalClasses = Attendance::count();
        $students = User::where('is_admin', '=', 0)->get();
        foreach ($students as $student) {
            $count = $student->attendances()->count();
            $percentAttendance = $count / $totalClasses * 100;
            $student['attendance_count'] = $count;
            $student['attendance_count_percent'] = $percentAttendance;
        }
        return $students;


    }


}