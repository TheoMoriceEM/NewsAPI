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

Route::controller(CountryController::class)->group(function () {
    Route::get('/countries', 'index')->name('country.index');
    Route::get('/country/{country}', 'show')->name('country.show')->missing(function () {
        return response()->json(['message' => 'Country not found in the database'], 404);
    });
    Route::post('/country/{country}/{category}', 'toggleCategory')->withoutScopedBindings()->name('country_category.toggle')->missing(function () {
        return response()->json(['message' => 'Country or category not found in the database'], 404);
    });
});

Route::controller(NewsDataApiController::class)->group(function () {
    Route::get('/country/{country}/{language}/{category?}', 'getLatestNews')->name('latest_news')->missing(function () {
        return response()->json(['message' => 'A resource has not been found in the database'], 404);
    });
    Route::get('/news/{country}/{page?}', 'getLatestNews')->where('page', '[1-9]+')->name('latest_news_by_page')->missing(function () {
        return response()->json(['message' => 'Country not found in the database'], 404);
    });
});
