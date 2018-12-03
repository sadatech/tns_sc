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
	Route::post('/checkin/{type?}', 'API\AttendanceController@absen')->name('api.checkin');
	Route::post('/checkout/{type?}', 'API\AttendanceController@checkout')->name('api.checkout');
	Route::get('/status/{type?}', 'API\AttendanceController@status')->name('api.status');
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

Route::prefix('area')->group(function () {
	Route::get('/list', 'API\AreaController@list')->name('api.area.list');
});

Route::prefix('subarea')->group(function () {
	Route::get('/list/{id_area?}', 'API\SubAreaController@list')->name('api.subarea.list');
});

Route::prefix('product')->group(function () {
	Route::post('/list', 'API\ProductController@list')->name('api.product.list');
	Route::get('pf/list/{type}/{id}', 'API\ProductController@pfList')->name('api.product.pf.list');
});

Route::prefix('brand')->group(function () {
	Route::get('/list', 'API\BrandController@list')->name('api.brand.list');
});

Route::prefix('sales')->group(function () {
	Route::post('/process/{type}', 'API\SellController@store')->name('api.sales.process');
	Route::post('/add', 'API\SalesController@store')->name('api.sales.add');
});

Route::prefix('sales-md')->group(function () {
	Route::post('/add', 'API\SalesMdController@store')->name('api.sales-md.add');
	Route::get('/list/{date?}', 'API\SalesMdController@list')->name('api.sales-md.list');
});

Route::prefix('sales-spg-pasar')->group(function () {
	Route::post('/add', 'API\SalesSpgPasarController@store')->name('api.sales-spg-pasar.add');
	Route::get('/list/{date?}', 'API\SalesSpgPasarController@list')->name('api.sales-spg-pasar.list');
});

Route::prefix('sales-recap')->group(function () {
	Route::post('/add', 'API\SalesRecapController@store')->name('api.sales-recap.add');
	Route::get('/list/{date?}', 'API\SalesRecapController@list')->name('api.sales-recap.list');
});

Route::prefix('sales-dc')->group(function () {
	Route::post('/add/{type?}', 'API\SalesDcController@store')->name('api.sales-dc.add');
});

Route::prefix('sales-motoric')->group(function () {
	Route::post('/add', 'API\SalesMotoricController@store')->name('api.sales-motoric.add');
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

Route::prefix('display-share')->group(function () {
	Route::post('/add', 'API\DisplayShareController@store')->name('api.display-share.add');
});

Route::prefix('additional-display')->group(function () {
	Route::post('/add', 'API\AdditionalDisplayController@store')->name('api.additional-display.add');
});

Route::prefix('cbd')->group(function () {
	Route::get('/list', 'API\CbdController@list')->name('api.cbd.list');
	Route::post('/add', 'API\CbdController@store')->name('api.cbd.add');
});

Route::prefix('distribution')->group(function () {
	Route::post('/add', 'API\DistributionController@store')->name('api.distribution.add');
	Route::post('/add/{type}', 'API\DistributionController@store')->name('api.distribution.add.motoric');
});

Route::prefix('faq')->group(function () {
	Route::get('/list', 'API\FaqController@list')->name('api.faq.list');
});

Route::prefix('news')->group(function () {
	Route::get('/list', 'API\NewsController@list')->name('api.news.list');
});

Route::prefix('pk')->group(function () {
	Route::get('/list', 'API\PkController@list')->name('api.pk.list');
});

// Pasar
Route::prefix('pasar')->group(function () {
	Route::get('/list', 'API\PasarController@list')->name('api.pasar.list');	
	Route::post('/checkin', 'API\PasarController@checkin')->name('api.pasar.checkin');
	Route::get('/checkout', 'API\PasarController@checkout')->name('api.pasar.checkout');
	Route::get('/status', 'API\PasarController@status')->name('api.pasar.status');
});

// Stock
Route::prefix('stock')->group(function () {
	Route::post('/add', 'API\StockController@store')->name('api.stock.add');
});

// Plan
Route::prefix('plan')->group(function () {
	Route::get('/date', 'API\PlanController@date')->name('api.plan.date');
	Route::get('/month', 'API\PlanController@month')->name('api.plan.month');
	Route::post('/update', 'API\PlanController@update')->name('api.plan.update');
});

// Documentation
Route::prefix('documentation')->group(function () {
	Route::post('/add', 'API\DocumentationController@store')->name('api.plan.add');
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

// Block
Route::prefix('block')->group(function () {
	Route::post('/add', 'API\BlockController@store')->name('api.block.add');
	Route::post('/edit/{id}', 'API\BlockController@update')->name('api.block.edit');
	Route::get('/list/{id}', 'API\BlockController@list')->name('api.block.list');
	Route::get('/disable/{id}/{status}', 'API\BlockController@disable')->name('api.block.disable');
});

// History
Route::prefix('history')->group(function () {
	Route::get('/attendance/{type}/{date?}', 'API\HistoryController@attenadnceHistory')->name('api.attendance-history.list');
	Route::get('/sales/{type}/{date?}', 'API\HistoryController@salesHistory')->name('api.sales-history.list');
	Route::get('/sales-recap/{date?}', 'API\HistoryController@salesRecapHistory')->name('api.sales-recap-history.list');
	Route::get('/stockist/{date?}', 'API\HistoryController@stockistHistory')->name('api.stockist-history.list');
	Route::get('/distribution/{type}/{date?}', 'API\HistoryController@distributionHistory')->name('api.distribution-history.list');
	Route::get('/cbd/{date?}', 'API\HistoryController@cbdHistory')->name('api.cbd-history.list');
	Route::get('/dc/{type?}/{date?}', 'API\HistoryController@dcHistory')->name('api.dc-history.list');
	Route::get('/plan/{date?}', 'API\HistoryController@planHistory')->name('api.plan-history.list');
	Route::get('/documentation/{date?}', 'API\HistoryController@documentationHistory')->name('api.documentation-history.list');
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