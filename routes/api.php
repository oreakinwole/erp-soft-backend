<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PayrollController;
// Removed config-based PayrollConfigController; using DB-driven allowance types
use App\Http\Controllers\AllowanceTypeController;

// Staff endpoint for adding new staff records
Route::post('/staff', [StaffController::class, 'store']);
Route::get('/staff', [StaffController::class, 'index']);
Route::get('/staff/{id}', [StaffController::class, 'show']);
Route::put('/staff/{id}', [StaffController::class, 'update']);

// Location endpoints
Route::get('/states', [LocationController::class, 'getStates']);
Route::get('/lgas', [LocationController::class, 'getLGAs']);

// Auth endpoints
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot', [AuthController::class, 'forgot']);
Route::post('/auth/verify', [AuthController::class, 'verify']);
Route::post('/auth/reset', [AuthController::class, 'reset']);

// Payroll endpoints
Route::get('/payroll', [PayrollController::class, 'index']);
Route::get('/payroll/summary', [PayrollController::class, 'summary']);
Route::get('/payroll/runs', [PayrollController::class, 'runs']);
Route::get('/payroll/{id}', [PayrollController::class, 'show']);
Route::post('/payroll/generate', [PayrollController::class, 'generate']);
Route::post('/payroll/{id}/process', [PayrollController::class, 'process']);
Route::post('/payroll/{id}/mark-paid', [PayrollController::class, 'markPaid']);
Route::put('/payroll/{id}', [PayrollController::class, 'update']);
Route::delete('/payroll/{id}', [PayrollController::class, 'destroy']);

// Removed config-based allowance types endpoint; use DB-backed /allowance-types

// Allowance type endpoints (DB-backed)
Route::get('/allowance-types', [AllowanceTypeController::class, 'index']);
Route::post('/allowance-types', [AllowanceTypeController::class, 'store']);
Route::delete('/allowance-types/{id}', [AllowanceTypeController::class, 'destroy']);
