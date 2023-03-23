<?php

use App\Http\Controllers\CountryController;
use App\Http\Controllers\NewsDataApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/countries', [CountryController::class, 'index'])->name('country_index');
Route::get('/country/{country}', [CountryController::class, 'show'])->name('country_show');
Route::post('/country/{country}/{category}', [CountryController::class, 'toggleCategory'])->withoutScopedBindings()->name('country_category_toggle');
Route::get('/country/{country}/{language}/{category?}', [NewsDataApiController::class, 'getLatestNews'])->name('latest_news');
Route::get('/news/{country}/{page?}', [NewsDataApiController::class, 'getLatestNews'])->name('latest_news_by_page');
