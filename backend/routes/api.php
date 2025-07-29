<?php

use App\Http\Controllers\CountryCategoryController;
use App\Http\Controllers\CountryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/country/{code}', [CountryController::class, 'show']);

Route::post('/country-category', [CountryCategoryController::class, 'store']);
Route::delete('/country-category', [CountryCategoryController::class, 'destroy']);
