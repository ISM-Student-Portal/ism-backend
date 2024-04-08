<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/create-super-admin', [UserController::class, 'createSuperAdminUser'])->name('create_admin');

Route::middleware('auth:sanctum')->post('/create-user', [UserController::class, 'createUser'])->name('create_user');
Route::middleware('auth:sanctum')->get('/students', [UserController::class, 'getStudents'])->name('students');

Route::middleware('auth:sanctum')->post('/create-profile', [UserController::class, 'createProfile'])->name('create_profile');

Route::middleware('auth:sanctum')->patch('/update-profile', [UserController::class, 'updateProfile'])->name('update_profile');

Route::middleware('auth:sanctum')->post('/batch-create', [UserController::class, 'batchCreateUser'])->name('batch-create');

Route::middleware('auth:sanctum')->get('/dashboard-stats', [UserController::class, 'getDashboardStats'])->name('dashboard');


Route::middleware('auth:sanctum')->controller(ClassroomController::class)->group(function (){
    Route::post('/classroom', 'store');
    Route::get('/classroom', 'index');
});




