<?php

use App\Http\Controllers\Admin\MessageController;

/*
|--------------------------------------------------------------------------
| Notifications Module Routes
|--------------------------------------------------------------------------
*/

Route::resource('messages', MessageController::class);
