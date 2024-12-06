<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
               
use App\Http\Controllers\AuthController;
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Hanya bisa diakses jika user memiliki token yang valid
Route::middleware('auth:api')->get('/user', [AuthController::class, 'getAuthenticatedUser']);

use App\Http\Controllers\UserController;
// Route untuk menambahkan user (hanya admin)
Route::middleware('auth:api', 'role:admin')->post('/user/create', [UserController::class, 'createUser']);
// Route untuk mengubah data user (hanya admin bersangkutan)
Route::middleware('auth:api', 'role:admin')->put('/user/{id}', [UserController::class, 'updateUser']);
// Route untuk mengambil data user berdasarkan ID
Route::middleware('auth:api')->get('/user/{id}', [UserController::class, 'getUserById']);
Route::delete('/user/{id}', [UserController::class, 'deleteUser']);


use App\Http\Controllers\PresencesController;

Route::post('/presensi', [PresencesController::class, 'store']);
Route::get('/presensi/riwayat', [PresencesController::class, 'riwayat'])
    ->middleware(['auth:api', 'role:admin,user']);
Route::get('/presensi/riwayat/{user_id}', [PresencesController::class, 'riwayatByUserId'])
    ->middleware(['auth:api', 'role:admin']);