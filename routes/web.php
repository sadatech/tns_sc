<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'DashboardController@dashboard')->name('dashboard')->middleware('auth');


Route::get('/dashboard', 'DashboardController@dashboard')->name('dashboard')->middleware('auth');

/**
*	Store Master Data
*/
Route::prefix('store')->group(function () {
	//Store Pages
	Route::prefix('summary')->group(function(){
		Route::get('/', 'StoreController@baca')->name('store')->middleware('auth');
		Route::get('/create', 'StoreController@readStore')->name('tambah.store')->middleware('auth');
		Route::get('/update/{id}', 'StoreController@readUpdate')->name('ubah.store')->middleware('auth');
		Route::get('/data', 'StoreController@data')->name('store.data')->middleware('auth');
		Route::post('/create', 'StoreController@store')->name('store.add')->middleware('auth');
		Route::put('/update/{id}', 'StoreController@update')->name('store.update')->middleware('auth');
		Route::get('/delete/{id}', 'StoreController@delete')->name('store.delete')->middleware('auth');
	});

	//Pasar Pages
	Route::prefix('pasar')->group(function(){
		Route::get('/', 'PasarController@read')->name('pasar')->middleware('auth');
		Route::get('/create', 'PasarController@readStore')->name('tambah.pasar')->middleware('auth');
		Route::get('/update/{id}', 'PasarController@readUpdate')->name('ubah.pasar')->middleware('auth');
		Route::get('/data', 'PasarController@data')->name('pasar.data')->middleware('auth');
		Route::post('/create', 'PasarController@store')->name('pasar.add')->middleware('auth');
		Route::put('/update/{id}', 'PasarController@update')->name('pasar.update')->middleware('auth');
		Route::get('/delete/{id}', 'PasarController@delete')->name('pasar.delete')->middleware('auth');
		Route::post('/import', 'PasarController@importXLS')->name('import')->middleware('auth');
		Route::get('/export', 'PasarController@exportXLS')->name('pasar.export')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/PasarImport.xlsx'));
		})->name('pasar.download-template')->middleware('auth');
	});

	//Sub Area Pages
	Route::prefix('subarea')->group(function(){
		Route::get('/', 'SubareaController@baca')->name('subarea')->middleware('auth');
		Route::get('/data', 'SubareaController@data')->name('subarea.data')->middleware('auth');
		Route::post('/create', 'SubareaController@store')->name('subarea.add')->middleware('auth');
		Route::put('/update/{id}', 'SubareaController@update')->name('subarea.update')->middleware('auth');
		Route::get('/delete/{id}', 'SubareaController@delete')->name('subarea.delete')->middleware('auth');
		Route::post('/import', 'SubareaController@importXLS')->name('import')->middleware('auth');
		Route::get('/export', 'SubareaController@exportXLS')->name('subarea.export')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/SubAreaImport.xlsx'));
		})->name('subarea.download-template')->middleware('auth');
	});

	//Region Pages
	Route::prefix('region')->group(function(){
		Route::get('/', 'RegionController@baca')->name('region')->middleware('auth');
		Route::get('/data', 'RegionController@data')->name('region.data')->middleware('auth');
		Route::post('/create', 'RegionController@store')->name('region.add')->middleware('auth');
		Route::put('/update/{id}', 'RegionController@update')->name('region.update')->middleware('auth');
		Route::get('/confirm/delete/{id}', 'RegionController@deleteConfirm')->name('region.deleteConfirm')->middleware('auth');
		Route::get('/delete/{id}', 'RegionController@delete')->name('region.delete')->middleware('auth');
	});
	
	//Channel Pages
	Route::prefix('channel')->group(function(){
		Route::get('/', 'ChannelController@baca')->name('channel')->middleware('auth');
		Route::get('/data', 'ChannelController@data')->name('channel.data')->middleware('auth');
		Route::post('/create', 'ChannelController@store')->name('channel.add')->middleware('auth');
		Route::put('/update/{id}', 'ChannelController@update')->name('channel.update')->middleware('auth');
		Route::get('/delete/{id}', 'ChannelController@delete')->name('channel.delete')->middleware('auth');
	});

	//Account Pages
	Route::prefix('account')->group(function(){
		Route::post('/import', 'AccountController@import')->name('account.import')->middleware('auth');
		Route::get('/', 'AccountController@baca')->name('account')->middleware('auth');
		Route::get('/data', 'AccountController@data')->name('account.data')->middleware('auth');
		Route::post('/create', 'AccountController@store')->name('account.add')->middleware('auth');
		Route::put('/update/{id}', 'AccountController@update')->name('account.update')->middleware('auth');
		Route::get('/delete/{id}', 'AccountController@delete')->name('account.delete')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/AccountImport.xlsx'));
		})->name('account.download-template')->middleware('auth');
	});

	//Distributor Pages
	Route::prefix('distributor')->group(function(){
		Route::get('/', 'DistributorController@baca')->name('distributor')->middleware('auth');
		Route::get('/data', 'DistributorController@data')->name('distributor.data')->middleware('auth');
		Route::post('/create', 'DistributorController@store')->name('distributor.add')->middleware('auth');
		Route::put('/update/{id}', 'DistributorController@update')->name('distributor.update')->middleware('auth');
		Route::get('/delete{id}', 'DistributorController@delete')->name('distributor.delete')->middleware('auth');
		Route::post('/import', 'DistributorController@import')->name('distributor.import')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/DistributorImport.xlsx'));
		})->name('distributor.download-template')->middleware('auth');
	});

	//Place Pages
	Route::prefix('place')->group(function(){
		Route::get('/', 'PlaceController@baca')->name('place')->middleware('auth');
		Route::get('/data', 'PlaceController@data')->name('place.data')->middleware('auth');
		Route::post('/create', 'PlaceController@store')->name('place.add')->middleware('auth');
		Route::put('/update/{id}', 'PlaceController@update')->name('place.update')->middleware('auth');
		Route::get('/delete/{id}', 'PlaceController@delete')->name('place.delete')->middleware('auth');
		Route::post('/import', 'PlaceController@import')->name('place.import')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/PlaceImport.xlsx'));
		})->name('place.download-template')->middleware('auth');
	});

	//Classification Pages
	// Route::prefix('classification')->group(function(){
	// 	Route::get('/', 'ClassificationController@baca')->name('classification')->middleware('auth');
	// 	Route::get('/data', 'ClassificationController@data')->name('classification.data')->middleware('auth');
	// 	Route::post('/create', 'ClassificationController@store')->name('classification.add')->middleware('auth');
	// 	Route::put('/update/{id}', 'ClassificationController@update')->name('classification.update')->middleware('auth');
	// 	Route::get('/delete/{id}', 'ClassificationController@delete')->name('classification.delete')->middleware('auth');
	// });

	//Area Pages
	Route::prefix('area')->group(function () {
		Route::get('/', 'AreaController@index')->name('area')->middleware('auth');
		Route::get('/data', 'AreaController@data')->name('area.data')->middleware('auth');
		Route::post('/create', 'AreaController@store')->name('area.add')->middleware('auth');
		Route::put('/update/{id}', 'AreaController@update')->name('area.update')->middleware('auth');
		Route::get('/delete/{id}', 'AreaController@delete')->name('area.delete')->middleware('auth');
	});
});

/**
*	Employee Master Data
*/
Route::prefix('employee')->group(function () {

	//Position Pages
	Route::prefix('position')->group(function () {
		Route::get('/', 'PositionController@baca')->name('position')->middleware('auth');
		Route::get('/data', 'PositionController@data')->name('position.data')->middleware('auth');
		Route::put('/update/{id}', 'PositionController@update')->name('position.update')->middleware('auth');
		Route::get('/delete/{id}', 'PositionController@delete')->name('position.delete')->middleware('auth');
	});

	//Agency Pages
	Route::prefix('agency')->group(function () {
		Route::get('/', 'AgencyController@baca')->name('agency')->middleware('auth');
		Route::get('/data', 'AgencyController@data')->name('agency.data')->middleware('auth');
		Route::post('/add', 'AgencyController@store')->name('agency.add')->middleware('auth');
		Route::put('/update/{id}', 'AgencyController@update')->name('agency.update')->middleware('auth');
		Route::get('/delete/{id}', 'AgencyController@delete')->name('agency.delete')->middleware('auth');
	});

	//Employee Pages
	Route::prefix('summary')->group(function () {
		Route::get('/', 'EmployeeController@baca')->name('employee')->middleware('auth');
		Route::get('/create', 'EmployeeController@read')->name('tambah.employee')->middleware('auth');
		Route::get('/update/{id}', 'EmployeeController@readupdate')->name('ubah.employee')->middleware('auth');
		Route::get('/data', 'EmployeeController@data')->name('employee.data')->middleware('auth');
		Route::post('/create', 'EmployeeController@store')->name('employee.add')->middleware('auth');
		Route::put('/update/{id}', 'EmployeeController@update')->name('employee.update')->middleware('auth');
		Route::get('/delete/{id}', 'EmployeeController@delete')->name('employee.delete')->middleware('auth');
	});

	//Resign Pages
	Route::prefix('resign')->group(function (){
		Route::get('/', 'ResignController@baca')->name('resign')->middleware('auth');
		Route::get('/data', 'ResignController@data')->name('resign.data')->middleware('auth');
		Route::post('/create', 'ResignController@store')->name('resign.add')->middleware('auth');
		Route::put('/update/{id}', 'ResignController@update')->name('resign.update')->middleware('auth');
	});
	
	//Rejoin Pages
	Route::prefix('rejoin')->group(function () {
		Route::get('/', 'RejoinController@baca')->name('rejoin')->middleware('auth');
		Route::get('/data', 'RejoinController@data')->name('rejoin.data')->middleware('auth');
		Route::post('/create', 'RejoinController@store')->name('rejoin.add')->middleware('auth');
	});
});

/**
*	Product Master Data
*/
Route::prefix('product')->group(function () {
	//Brand Pages
	Route::prefix('brand')->group(function () {
		Route::get('/', 'BrandController@baca')->name('brand')->middleware('auth');
		Route::get('/data', 'BrandController@data')->name('brand.data')->middleware('auth');
		Route::post('/add', 'BrandController@store')->name('brand.add')->middleware('auth');
		Route::put('/update/{id}', 'BrandController@update')->name('brand.update')->middleware('auth');
		Route::get('/delete/{id}', 'BrandController@delete')->name('brand.delete')->middleware('auth');
	});
	
	//Category Pages
	Route::prefix('category')->group(function () {
		Route::get('/', 'CategoryController@baca')->name('category')->middleware('auth');
		Route::get('/data', 'CategoryController@data')->name('category.data')->middleware('auth');
		Route::post('/create', 'CategoryController@store')->name('category.add')->middleware('auth');
		Route::put('/update/{id}', 'CategoryController@update')->name('category.update')->middleware('auth');
		Route::get('/delete/{id}', 'CategoryController@delete')->name('category.delete')->middleware('auth');
	});

	//Sub Category Pages
	Route::prefix('sub-category')->group(function () {
		Route::get('/', 'SubCategoryController@baca')->name('sub-category')->middleware('auth');
		Route::get('/data', 'SubCategoryController@data')->name('sub-category.data')->middleware('auth');
		Route::post('/create', 'SubCategoryController@store')->name('sub-category.add')->middleware('auth');
		Route::put('/update/{id}', 'SubCategoryController@update')->name('sub-category.update')->middleware('auth');
		Route::get('/delete/{id}', 'SubCategoryController@delete')->name('sub-category.delete')->middleware('auth');
	});

	//Product Summary Pages
	Route::prefix('summary')->group(function () {
		Route::get('/', 'ProductController@baca')->name('product')->middleware('auth');
		Route::get('/data', 'ProductController@data')->name('product.data')->middleware('auth');
		Route::post('/create', 'ProductController@store')->name('product.add')->middleware('auth');
		Route::put('/update/{id}', 'ProductController@update')->name('product.update')->middleware('auth');
		Route::get('/delete/{id}', 'ProductController@delete')->name('product.delete')->middleware('auth');
	});

	//Product Competitor Pages
	Route::prefix('product-competitor')->group(function () {
		Route::get('/', 'ProductCompetitorController@baca')->name('product-competitor')->middleware('auth');
		Route::get('/data', 'ProductCompetitorController@data')->name('product-competitor.data')->middleware('auth');
		Route::post('/create', 'ProductCompetitorController@store')->name('product-competitor.add')->middleware('auth');
		Route::put('/update/{id}', 'ProductCompetitorController@update')->name('product-competitor.update')->middleware('auth');
		Route::get('/delete/{id}', 'ProductCompetitorController@delete')->name('product-competitor.delete')->middleware('auth');
	});

	//Price Pages
	Route::prefix('price')->group(function () {
		Route::get('/', 'PriceController@baca')->name('price')->middleware('auth');
		Route::get('/data', 'PriceController@data')->name('price.data')->middleware('auth');
		Route::post('/create', 'PriceController@store')->name('price.add')->middleware('auth');
		Route::put('/update/{id}', 'PriceController@update')->name('price.update')->middleware('auth');
		Route::get('/delete/{id}', 'PriceController@delete')->name('price.delete')->middleware('auth');
	});

	//Promo Pages
	Route::prefix('promo')->group(function () {
		Route::get('/', 'ProductPromoController@baca')->name('promo')->middleware('auth');
		Route::get('/data', 'ProductPromoController@data')->name('promo.data')->middleware('auth');
		Route::post('/create', 'ProductPromoController@store')->name('promo.add')->middleware('auth');
		Route::put('/update/{id}', 'ProductPromoController@update')->name('promo.update')->middleware('auth');
		Route::get('/delete/{id}', 'ProductPromoController@delete')->name('promo.delete')->middleware('auth');
	});

	//Fokus Pages
	Route::prefix('fokus')->group(function () {
		Route::get('/', 'ProductFokusController@baca')->name('fokus')->middleware('auth');
		Route::get('/data', 'ProductFokusController@data')->name('fokus.data')->middleware('auth');
		Route::post('/create', 'ProductFokusController@store')->name('fokus.add')->middleware('auth');
		Route::put('/update/{id}', 'ProductFokusController@update')->name('fokus.update')->middleware('auth');
		Route::get('/delete/{id}', 'ProductFokusController@delete')->name('fokus.delete')->middleware('auth');
	});

	//Target Pages
	Route::prefix('target')->group(function () {
		Route::get('/', 'TargetController@baca')->name('target')->middleware('auth');
		Route::get('/data', 'TargetController@data')->name('target.data')->middleware('auth');
		Route::post('/create', 'TargetController@store')->name('target.add')->middleware('auth');
		Route::put('/update/{id}', 'TargetController@update')->name('target.update')->middleware('auth');
		Route::get('/delete/{id}', 'TargetController@delete')->name('target.delete')->middleware('auth');
	});
});

/**
*	PlanDc Pages
*/
Route::prefix('planDc')->group(function () {
	Route::get('/', 'PlandcController@read')->name('planDc')->middleware('auth');
	Route::get('/data', 'PlandcController@data')->name('plan.data')->middleware('auth');
	Route::put('/update/{id}', 'PlandcController@update')->name('plan.update')->middleware('auth');
	Route::get('/delete/{id}', 'PlandcController@delete')->name('plan.delete')->middleware('auth');
});


/**
*	Company Pages
*/
Route::prefix('company')->group(function () {
	Route::get('/', 'CompanyController@baca')->name('company')->middleware('auth');
	Route::put('/update/{id}', 'CompanyController@update')->name('company.update')->middleware('auth');
});


// ***************** REPORTING ***********************

Route::prefix('report')->group(function () {
	Route::prefix('sales')->group(function () {
		Route::prefix('sellin')->group(function () {
			Route::get('/', 'ReportController@sellInIndex')->name('sellin')->middleware('auth');
			Route::post('/data', 'ReportController@sellInData')->name('sellin.data')->middleware('auth');
			Route::post('/edit/{id}', 'ReportController@sellInUpdate')->name('sellin.edit')->middleware('auth');
			Route::get('/delete/{id}', 'ReportController@sellInDelete')->name('sellin.delete')->middleware('auth');
			Route::post('/add', 'ReportController@sellInAdd')->name('sellin.add')->middleware('auth');
			Route::post('/import', 'ImportQueueController@ImportSellIn')->name('sellin.import')->middleware('auth');
			Route::get('/download-template', function()
			{
				return response()->download(public_path('assets/SellinImport.xlsx'));
			})->name('SellIn.download-template')->middleware('auth');
			});
		
		Route::get('/sellout', 'DashboardController@dashboard')->name('sellout')->middleware('auth');
	});	
	Route::get('/stock', 'DashboardController@dashboard')->name('stock')->middleware('auth');
});

// ***************** REPORTING ***********************

/**
*	Welcome Pages
*/
Route::prefix('welcome')->group(function () {
	Route::get('/', 'DashboardController@welcome')->name('welcome')->middleware('auth');
	Route::post('/create', 'DashboardController@create_company')->name('welcome_create')->middleware('auth');
});

/**
*	Utility
*/
Route::prefix('utility')->group(function () {
	Route::get('/getCity', 'DashboardController@getCity')->name('getCity');
});

/**
*	Select2
*/
Route::prefix('select2')->group(function () {
	Route::post('/region-select2', 'RegionController@getDataWithFilters')->name('region-select2');
	Route::post('/area-select2', 'AreaController@getDataWithFilters')->name('area-select2');
	Route::post('/sub-area-select2', 'SubareaController@getDataWithFilters')->name('sub-area-select2');
	Route::post('/employee-select2', 'EmployeeController@getDataWithFilters')->name('employee-select2');
	Route::post('/store-select2', 'StoreController@getDataWithFilters')->name('store-select2');
	Route::post('/product-select2', 'ProductController@getDataWithFilters')->name('product-select2');
});


Auth::routes();

Route::get('/tes', 'ReportController@sellInData');