<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/login','Auth\AuthController@login')->name('login');
//Route::post('/login','Auth\AuthController@authenticate');
//Route::get('/register','Auth\AuthController@register')->name('register');
//Route::post('/register','Auth\AuthController@storeUser');
//Route::get('/logout','Auth\AuthController@logout')->name('logout');
//Route::get('/home','Auth\AuthController@home')->name('home');

Route::get('/login',[AuthController::class, 'login'])->name('login');
Route::get('/register',[AuthController::class, 'register'])->name('register');
Route::get('/logout',[AuthController::class, 'logout'])->name('logout');

Route::post('/login',[AuthController::class, 'authenticate']);
Route::post('/register',[AuthController::class, 'storeUser']);

Route::get('/forget-password', [ForgotPasswordController::class, 'getEmail'])->name('forget-password');
Route::post('/forget-password', [ForgotPasswordController::class, 'postEmail']);
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'getPassword']);
Route::post('/reset-password',  [ResetPasswordController::class, 'updatePassword']);

Route::middleware(['auth', 'admin:Admin'])->group(function () {
    // User is authentication and has admin role
    Route::get('/home',[AuthController::class, 'home'])->name('home');
    Route::get('/employee',[EmployeeController::class, 'index'])->name('employee');
    Route::get('employee/create',[EmployeeController::class, 'showAddScreen'])->name('employee.create');
    Route::get('employee/edit/{id}',[EmployeeController::class, 'edit'])->name('employee.edit');
    Route::delete('employee/destroy/{id}',[EmployeeController::class, 'destroy'])->name('employee.destroy');
    Route::post('/employeeSave',[EmployeeController::class, 'storeEmployee'])->name('employee.save');
    Route::post('/employeeUpdate',[EmployeeController::class, 'updateEmployee'])->name('employee.update');
    Route::post('/upload-content',[EmployeeController::class,'uploadContent'])->name('import.content');
});

Route::middleware(['auth', 'user:User'])->group(function () {
    // User is authentication and has user role
    Route::get('/userhome',[AuthController::class, 'home'])->name('userhome');
    Route::get('userprofile/{id}',[AuthController::class, 'edit'])->name('userprofile');
    Route::post('/userprofile',[EmployeeController::class, 'updateprofile'])->name('user.updateprofile');
});
