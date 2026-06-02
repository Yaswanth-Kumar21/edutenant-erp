<?php

use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\Student360Controller;
use App\Http\Controllers\Admin\ScholarshipController;

/*
|--------------------------------------------------------------------------
| Students Module Routes
|--------------------------------------------------------------------------
*/

// Redirect legacy create URL to admission wizard
Route::get('students/create', fn() => redirect()->route('admin.admissions.create'))
    ->name('students.create');

// ── Student 360° View ─────────────────────────────────────────────────────
Route::get('students/360/search', [Student360Controller::class, 'search'])
    ->name('students.360.search');
Route::get('students/{student}/360', [Student360Controller::class, 'show'])
    ->name('students.360');

// ── Scholarship Management ────────────────────────────────────────────────
Route::resource('scholarships', ScholarshipController::class)
    ->except(['show']);

// ── Standard Student CRUD ─────────────────────────────────────────────────
Route::resource('students', StudentController::class)->except(['create', 'store']);
Route::get('students/{student}/profile', [StudentController::class, 'profile'])
    ->name('students.profile');
Route::post('students/{student}/create-login', [StudentController::class, 'createLogin'])
    ->name('students.create-login');
