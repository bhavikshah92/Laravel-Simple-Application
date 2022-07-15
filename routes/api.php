<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class,'loginAPI']);

Route::get('employee', [EmployeeController::class,'fetchAllEmployeeAPI']);
Route::get('employee/{id}',  [EmployeeController::class,'findEmployeeByIdAPI']);
Route::post('employee', [EmployeeController::class,'createEmployeeAPI']);
Route::post('employee/{id}', [EmployeeController::class,'updateEmployeeProfileAPI']);
Route::delete('employee/{id}', [EmployeeController::class,'deleteEmployeeByIdAPI']);