<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
	Route::post('/company', 'API\AuthController@company')->name('api.company.auth');
	Route::post('/user', 'API\AuthController@user')->name('api.user.auth');
	Route::post('/login', 'API\AuthController@login')->name('api.login.auth');
	Route::get('/getUser', 'API\AuthController@getUser');
});

Route::prefix('attendance')->group(function () {
	Route::post('/checkin', 'API\AttendanceController@absen')->name('api.checkin');
	Route::post('/checkout', 'API\AttendanceController@checkout')->name('api.checkout');
	Route::get('/status', 'API\AttendanceController@status')->name('api.status');
});

Route::prefix('store')->group(function () {
	Route::get('/list', 'API\StoreController@list')->name('api.store.list');
});

Route::prefix('place')->group(function () {
	Route::get('/list', 'API\PlaceController@list')->name('api.place.list');
});
Route::prefix('category')->group(function () {
	Route::get('/list', 'API\CategoryController@list')->name('api.category.list');
});
Route::prefix('product')->group(function () {
	Route::post('/list', 'API\ProductController@list')->name('api.product.list');
});
Route::prefix('sales')->group(function () {
	Route::post('/process/{type}', 'API\SellController@store')->name('api.sales.add');
});
Route::prefix('competitor')->group(function () {
	Route::post('/promo', 'API\CompetitorController@promo')->name('api.competitor.promo');
});

Route::prefix('dataprice')->group(function () {
	Route::post('/add', 'API\DataPriceController@store')->name('api.dataprice.add');
});

Route::prefix('availability')->group(function () {
	Route::post('/set', 'API\AvailabilityController@store')->name('api.availability.set');
});

// Outlet
Route::prefix('outlet')->group(function () {
	Route::post('/add', 'API\OutletController@store')->name('api.outlet.add');
});

/**
 * Employee
 */
Route::prefix("employee")->group(function(){
	Route::post("edit/password", "API\EmployeeController@editPassword")->name("api.employee.edit.password");
	Route::post("edit/profile", "API\EmployeeController@editProfile")->name("api.employee.edit.profile");
	Route::post("edit/profile/photo/{type?}", "API\EmployeeController@editProfilePhoto")->name("api.employee.edit.profile.photo");
});