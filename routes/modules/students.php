<?php

use App\Http\Controllers\Admin\StudentController;

/*
|--------------------------------------------------------------------------
| Students Module Routes
|--------------------------------------------------------------------------
*/

// Redirect legacy create URL to admission wizard
Route::get('students/create', fn() => redirect()->route('admin.admissions.create'))
    ->name('students.create');

Route::resource('students', StudentController::class)->except(['create', 'store']);
Route::get('students/{student}/profile', [StudentController::class, 'profile'])
    ->name('students.profile');
Route::post('students/{student}/create-login', [StudentController::class, 'createLogin'])
    ->name('students.create-login');
