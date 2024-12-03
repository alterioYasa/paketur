<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'v1'], function () {
    Route::group(['middleware' => 'role:Super Admin'], function () {
        Route::post('companies', [CompanyController::class, 'store']);
        Route::delete('companies/{id}', [CompanyController::class, 'destroy']);
        Route::put('companies/{id}', [CompanyController::class, 'update']);
    });
    Route::get('companies', [CompanyController::class, 'index']);
    Route::get('companies/{id}', [CompanyController::class, 'show']);

    Route::group(['middleware' => 'role:Manager,Super Admin'], function () {
        Route::post('employees', [EmployeeController::class, 'store']);
        Route::put('employees/{userId}', [EmployeeController::class, 'update']);
        Route::delete('employees/{userId}', [EmployeeController::class, 'destroy']);
    });

    Route::group(['middleware' => 'role:Manager,Super Admin,Employee'], function () {
        Route::get('employees', [EmployeeController::class, 'index']);
        Route::get('employees/{userId}', [EmployeeController::class, 'show']);
    });
});
