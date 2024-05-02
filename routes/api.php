<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\PdfExportController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/create-super-admin', [UserController::class, 'createSuperAdminUser'])->name('create_admin');

Route::middleware('auth:sanctum')->post('/create-user', [UserController::class, 'createUser'])->name('create_user');
Route::middleware('auth:sanctum')->post('/create-admin', [UserController::class, 'CreateAdmin'])->name('create_admin');

Route::middleware('auth:sanctum')->get('/students', [UserController::class, 'getStudents'])->name('students');
Route::middleware('auth:sanctum')->get('/admins', [UserController::class, 'getAdmins'])->name('admins');


Route::middleware('auth:sanctum')->post('/create-profile', [UserController::class, 'createProfile'])->name('create_profile');

Route::middleware('auth:sanctum')->put('/update-profile', [UserController::class, 'updateProfile'])->name('update_profile');
Route::middleware('auth:sanctum')->put('/update-profile-pix', [UserController::class, 'updateProfilePix'])->name('update_profile');

Route::middleware('auth:sanctum')->post('/batch-create', [UserController::class, 'batchCreateUser'])->name('batch-create');

Route::middleware('auth:sanctum')->get('/dashboard-stats', [UserController::class, 'getDashboardStats'])->name('dashboard');
Route::middleware('auth:sanctum')->get('/attendance-report', [UserController::class, 'attendanceReport'])->name('dashboard');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
Route::post('/password-reset', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::post('/resend-mail', [UserController::class, 'updateUserMails'])->name('password.change');

Route::middleware('auth:sanctum')->post('/password-update', [AuthController::class, 'updatePassword'])->name('password.reset');

Route::middleware('auth:sanctum')->get('/get-admission-letter', [PdfExportController::class, 'admissionLetter'])->name('admission.letter');




Route::middleware('auth:sanctum')->get('/student-dashboard-stats', [UserController::class, 'getStudentDashboardStats'])->name('dashboard');

Route::middleware('auth:sanctum')->put('/set-admin-status/{id}', [UserController::class, 'setAdminStatus']);
Route::middleware('auth:sanctum')->put('/set-active-status/{id}', [UserController::class, 'setActiveStatus']);


Route::middleware('auth:sanctum')->get('/get-assignments', [AssignmentController::class, 'getAssignments']);
Route::middleware('auth:sanctum')->get('/get-classrooms', [ClassroomController::class, 'getClassrooms']);





Route::middleware('auth:sanctum')->controller(ClassroomController::class)->group(function () {
    Route::post('/classroom', 'store');
    Route::get('/classroom', 'index');
    Route::delete('/classroom/{id}', 'destroy');

    Route::put('/mark-attendance/{id}', 'markAttendance');
    Route::get('/view-attendance/{id}', 'getClassAttendance');
    Route::get('/attendance-export/{id}', 'exportClassAttendance');
});

Route::middleware('auth:sanctum')->resource('assignments', AssignmentController::class);
Route::middleware('auth:sanctum')->resource('submissions', SubmissionController::class);





