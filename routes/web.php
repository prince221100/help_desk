<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
});
Route::post('/login',[UserController::class,'login'])->name('login');
Route::get('/dashboard',[UserController::class,'dashboard'])->name('dashboard');

Route::get('/logout',[UserController::class,'logout'])->name('logout');

// Forgot password :-

Route::get('/forgot_password',[UserController::class,'forgotpassword'])->name('forgot_password');
Route::post('/forgot',[UserController::class,'forgot'])->name('forgot');

Route::get('/reset_password/{token}',[UserController::class,'reset_password'])->name('reset_password');
Route::post('/reset',[UserController::class,'reset'])->name('reset');

// Employee Add :-
Route::get('/add_employee',[UserController::class,'add_employee'])->name('add_employee');
Route::post('/add_employeedata',[UserController::class,'add_employeedata'])->name('add_employeedata');

// Employee View :-
Route::get('/show_employee',[UserController::class,'show_employee'])->name('show_employee');

// Profile page :
Route::get('/profile',[UserController::class,'profile'])->name('profile');
Route::post('/profile_update',[UserController::class,'profile_update'])->name('profile_update');
