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

Route::prefix('subcategory')->group(function () {
	Route::get('/list/{id_category?}', 'API\SubCategoryController@list')->name('api.subcategory.list');
});

Route::prefix('product')->group(function () {
	Route::post('/list', 'API\ProductController@list')->name('api.product.list');
	Route::get('pf/list/{id_pasar}', 'API\ProductController@pfList')->name('api.product.pf.list');
});

Route::prefix('brand')->group(function () {
	Route::get('/list', 'API\BrandController@list')->name('api.brand.list');
});

Route::prefix('sales')->group(function () {
	Route::post('/process/{type}', 'API\SellController@store')->name('api.sales.process');
	Route::post('/add', 'API\SalesController@store')->name('api.sales.add');
});

Route::prefix('promo')->group(function () {
	Route::post('/add', 'API\PromoController@store')->name('api.promo.add');
});

Route::prefix('dataprice')->group(function () {
	Route::post('/add', 'API\DataPriceController@store')->name('api.dataprice.add');
});

Route::prefix('availability')->group(function () {
	Route::post('/set', 'API\AvailabilityController@store')->name('api.availability.set');
});

Route::prefix('cbd')->group(function () {
	Route::get('/list', 'API\CbdController@list')->name('api.cbd.list');
	Route::post('/add', 'API\CbdController@store')->name('api.cbd.add');
});

Route::prefix('distribution')->group(function () {
	Route::post('/add', 'API\DistributionController@store')->name('api.distribution.add');
});

// Pasar
Route::prefix('pasar')->group(function () {
	Route::get('/list', 'API\PasarController@list')->name('api.pasar.list');
});

// Stock
Route::prefix('stock')->group(function () {
	Route::post('/add', 'API\StockController@store')->name('api.stock.add');
});

// Outlet
Route::prefix('outlet')->group(function () {
	Route::post('/add', 'API\OutletController@store')->name('api.outlet.add');
	Route::post('/edit/{id}', 'API\OutletController@update')->name('api.outlet.edit');
	Route::get('/list/{id}', 'API\OutletController@list')->name('api.outlet.list');
	Route::get('/disable/{id}/{status}', 'API\OutletController@disable')->name('api.outlet.disable');
	Route::post('/checkin', 'API\OutletController@checkin')->name('api.outlet.checkin');
	Route::get('/checkout', 'API\OutletController@checkout')->name('api.outlet.checkout');
	Route::get('/status', 'API\OutletController@status')->name('api.status.list');
});

/**
 * Employee
 */
Route::prefix("employee")->group(function(){
	Route::get('/get', 'API\EmployeeController@get')->name('api.employee.get');
	Route::post("edit/password", "API\EmployeeController@editPassword")->name("api.employee.edit.password");
	Route::post("edit/profile", "API\EmployeeController@editProfile")->name("api.employee.edit.profile");
	Route::post("edit/profile/photo/{type?}", "API\EmployeeController@editProfilePhoto")->name("api.employee.edit.profile.photo");
});