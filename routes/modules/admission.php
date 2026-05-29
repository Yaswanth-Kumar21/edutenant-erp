<?php

use App\Http\Controllers\Admin\AdmissionController;

/*
|--------------------------------------------------------------------------
| Admission Module Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admissions')->name('admissions.')->group(function () {
    Route::get('create', [AdmissionController::class, 'create'])->name('create');
    Route::post('/', [AdmissionController::class, 'store'])->name('store');
    Route::get('{student}/receipt', [AdmissionController::class, 'receipt'])->name('receipt');
    Route::get('{student}/receipt/print', [AdmissionController::class, 'printReceipt'])->name('receipt.print');
    Route::post('{student}/certificates', [AdmissionController::class, 'uploadCertificate'])->name('certificates.upload');
    Route::delete('certificates/{certificate}', [AdmissionController::class, 'deleteCertificate'])->name('certificates.delete');
});
