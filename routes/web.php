<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;


Route::get('/', function () {
    return view('welcome');
});
