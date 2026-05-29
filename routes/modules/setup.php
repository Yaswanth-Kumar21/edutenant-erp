<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\StreamController;

/*
|--------------------------------------------------------------------------
| Setup Module Routes
|--------------------------------------------------------------------------
| College configuration: streams, courses, branches, academic years.
*/

Route::prefix('setup')->name('setup.')->group(function () {
    Route::resource('streams',        StreamController::class);
    Route::resource('courses',        CourseController::class);
    Route::resource('branches',       BranchController::class);
    Route::resource('academic-years', AcademicYearController::class);
});
