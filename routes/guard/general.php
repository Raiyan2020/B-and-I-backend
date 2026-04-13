<?php

use App\Http\Controllers\Api\V1\General\CategoryController;
use App\Http\Controllers\Api\V1\General\HomeController;
use App\Http\Controllers\Api\V1\General\InvestorDirectoryController;
use App\Http\Controllers\Api\V1\General\OpportunityController;
use App\Http\Controllers\Api\V1\General\PackagesController;
use App\Http\Controllers\Api\V1\General\ReferenceDataController;
use Illuminate\Support\Facades\Route;

Route::get('categories', [CategoryController::class, 'index']);
Route::get('investor-types', [ReferenceDataController::class, 'investorTypes']);
Route::get('investor-experience', [ReferenceDataController::class, 'investorExperience']);
Route::get('preferred-sectors', [ReferenceDataController::class, 'preferredSectors']);
Route::get('who-we-are', [HomeController::class, 'whoWeAre']);
Route::get('home-page', [HomeController::class, 'homePage']);
Route::get('investors', [InvestorDirectoryController::class, 'index']);
Route::get('packages', [PackagesController::class, 'index']);
Route::get('opportunities', [OpportunityController::class, 'index']);
Route::get('opportunities/{opportunity}', [OpportunityController::class, 'show']);
Route::get('privacy-policy', [HomeController::class, 'privacyPolicy']);
Route::get('terms-and-conditions', [HomeController::class, 'termsAndConditions']);
Route::match(['get', 'post'], 'change-lang',[HomeController::class,'changeLang']);

