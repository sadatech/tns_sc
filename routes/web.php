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

Route::get('/', function(){
	return view('dashboard.home');
})->name('dashboard')->middleware('auth');

Route::prefix('dashboard')->group(function () {
	Route::get('/', function(){
		return view('dashboard.home');
	})->name('dashboard')->middleware('auth');
	
	Route::prefix('gtc')->group(function () {
		Route::get('/', function(){
			return view('dashboard.gtc.smd');
		})->name('dashboard.gtc')->middleware('auth');

		Route::get('/smd', function(){
			return view('dashboard.gtc.smd');
		})->name('dashboard.gtc.smd')->middleware('auth');

		Route::get('/spg', function(){
			return view('dashboard.gtc.spg');
		})->name('dashboard.gtc.spg')->middleware('auth');

		Route::get('/demo-cooking', function(){
			return view('dashboard.gtc.dc');
		})->name('dashboard.gtc.dc')->middleware('auth');

		Route::get('/motorik', function(){
			return view('dashboard.gtc.motorik');
		})->name('dashboard.gtc.motorik')->middleware('auth');
	});
	
	Route::get('/mtc', function(){
		return view('dashboard.mtc');
	})->name('dashboard.mtc')->middleware('auth');
});

Route::prefix('data')->group(function () {
	Route::get('/dashboard', 'DashboardController@dashboard')->name('data.dashboard')->middleware('auth');
	Route::get('/gtc-smd', 'DashboardController@gtc_smd')->name('data.gtc_smd')->middleware('auth');
	Route::get('/gtc-spg', 'DashboardController@gtc_spg')->name('data.gtc_spg')->middleware('auth');
	Route::get('/gtc-dc', 'DashboardController@gtc_dc')->name('data.gtc_dc')->middleware('auth');
	Route::get('/gtc-motorik', 'DashboardController@gtc_motorik')->name('data.gtc_motorik')->middleware('auth');
});

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
		Route::get('/exportXLS', 'StoreController@exportXLS')->name('store.exportXLS')->middleware('auth');
		Route::get('/exampleSheet', 'StoreController@exampleSheet')->name('store.exampleSheet')->middleware('auth');
		Route::post('/importXLS', 'StoreController@importXLS')->name('store.importXLS')->middleware('auth');
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

	// Route Pages
	Route::prefix('root')->group(function(){
		Route::get('/', 'RouteController@baca')->name('root')->middleware('auth');
		Route::get('/data', 'RouteController@data')->name('root.data')->middleware('auth');
		Route::post('/create', 'RouteController@store')->name('root.add')->middleware('auth');
		Route::put('/update/{id}', 'RouteController@update')->name('root.update')->middleware('auth');
		Route::get('/delete/{id}', 'RouteController@delete')->name('root.delete')->middleware('auth');
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
			return response()->download(public_path('assets/StoreAccountImport.xlsx'));
		})->name('account.download-template')->middleware('auth');
	});

	//Sales Tiers

	Route::prefix('sales_tiers')->group(function()
	{
		Route::get('/','SalesTiersController@index')->name('sales_tiers')->middleware('auth');
		Route::get('/data', 'SalesTiersController@data')->name('sales_tiers.data')->middleware('auth');
		Route::post('/create', 'SalesTiersController@store')->name('sales_tiers.add')->middleware('auth');
		Route::get('/edit/{id}', 'SalesTiersController@edit')->name('sales_tiers.ubah')->middleware('auth');
		Route::put('/update/{id}', 'SalesTiersController@update')->name('sales_tiers.update')->middleware('auth');
		Route::get('/delete/{id}', 'SalesTiersController@delete')->name('sales_tiers.delete')->middleware('auth');
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
		// View
		Route::get('/', 'EmployeeController@baca')->name('employee')->middleware('auth');
		Route::get('/pasar', 'Employee\PasarController@baca')->name('employee.pasar')->middleware('auth');
		Route::get('/dc', 'Employee\DcController@baca')->name('employee.dc')->middleware('auth');

		// Crud
		Route::get('/create/{param?}', 'EmployeeController@read')->name('tambah.employee')->middleware('auth');
		Route::get('/update/{id}/{param?}', 'EmployeeController@readupdate')->name('ubah.employee')->middleware('auth');
		Route::post('/create', 'EmployeeController@store')->name('employee.add')->middleware('auth');
		Route::put('/update/{id}', 'EmployeeController@update')->name('employee.update')->middleware('auth');
		Route::get('/delete/{id}', 'EmployeeController@delete')->name('employee.delete')->middleware('auth');

		// Datatable
		Route::get('/data', 'EmployeeController@data')->name('employee.data')->middleware('auth');
		Route::get('/data/pasar', 'Employee\PasarController@data')->name('employee.data.pasar')->middleware('auth');
		Route::get('/data/dc', 'Employee\DcController@data')->name('employee.data.dc')->middleware('auth');

		//Export Import
		Route::get('/export', 'EmployeeController@export')->name('employee.export')->middleware('auth');
		Route::get('/dc/export', 'Employee\DcController@export')->name('employeedc.export')->middleware('auth');
		Route::get('/pasar/export', 'Employee\PasarController@export')->name('employeepasar.export')->middleware('auth');
		Route::post('/dc/import','Employee\DcController@import')->name('employeedc.import')->middleware('auth');
		Route::post('/pasar/import','Employee\PasarController@import')->name('employeesmd.import')->middleware('auth');
		Route::post('/import','EmployeeController@import')->name('employeess.import')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/EmployeeSmdImport.xlsx'));
		})->name('smd.download-template')->middleware('auth');
		Route::get('/dc/download-template', function()
		{
			return response()->download(public_path('assets/EmployeeDcImport.xlsx'));
		})->name('dc.download-template')->middleware('auth');
		Route::get('/employee/download-template', function()
		{
			return response()->download(public_path('assets/EmployeeImport.xlsx'));
		})->name('employee.download-template')->middleware('auth');
		
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
		Route::get('/export', 'RejoinController@export')->name('rejoin.export')->middleware('auth');
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
		Route::post('/import', 'SubCategoryController@import')->name('sub-category.import')->middleware('auth');
		Route::put('/update/{id}', 'SubCategoryController@update')->name('sub-category.update')->middleware('auth');
		Route::get('/delete/{id}', 'SubCategoryController@delete')->name('sub-category.delete')->middleware('auth');
		Route::get('/export', 'SubCategoryController@export')->name('sub-category.export')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/SubCategoryImport.xlsx'));
		})->name('subcategory.download-template')->middleware('auth');
	});

	//SKU Unit Pages
	Route::prefix('sku-unit')->group(function () {
		Route::get('/', 'SkuUnitController@baca')->name('sku-unit')->middleware('auth');
		Route::get('/data', 'SkuUnitController@data')->name('sku-unit.data')->middleware('auth');
		Route::post('/create', 'SkuUnitController@store')->name('sku-unit.add')->middleware('auth');
		Route::put('/update/{id}', 'SkuUnitController@update')->name('sku-unit.update')->middleware('auth');
		Route::get('/delete/{id}', 'SkuUnitController@destroy')->name('sku-unit.delete')->middleware('auth');
		Route::get('/export', 'SkuUnitController@export')->name('sku-unit.export')->middleware('auth');
		Route::post('/import', 'SkuUnitController@import')->name('sku-unit.import')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/SkuUnitImport.xlsx'));
		})->name('sku-unit.download-template')->middleware('auth');
	});

	//Product Summary Pages
	Route::prefix('summary')->group(function () {
		Route::get('/', 'ProductController@baca')->name('product')->middleware('auth');
		Route::get('/data', 'ProductController@data')->name('product.data')->middleware('auth');
		Route::get('/export', 'ProductController@export')->name('product.export')->middleware('auth');
		Route::post('/create', 'ProductController@store')->name('product.add')->middleware('auth');
		Route::put('/update/{id}', 'ProductController@update')->name('product.update')->middleware('auth');
		Route::get('/delete/{id}', 'ProductController@delete')->name('product.delete')->middleware('auth');
		Route::post('/import', 'ProductController@import')->name('product.import')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/ProductImport.xlsx'));
		})->name('product.download-template')->middleware('auth');
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
		Route::post('/import', 'PriceController@importXLS')->name('price.import')->middleware('auth');
		Route::put('/update/{id}', 'PriceController@update')->name('price.update')->middleware('auth');
		Route::get('/export', 'PriceController@exportXLS')->name('price.export')->middleware('auth');
		Route::get('/delete/{id}', 'PriceController@delete')->name('price.delete')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/PriceImport.xlsx'));
		})->name('price.download-template')->middleware('auth');
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
		Route::get('/export', 'ProductFokusController@export')->name('fokus.export')->middleware('auth');
		Route::post('/import', 'ProductFokusController@importXLS')->name('fokus.import')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/ProductFokusImport.xlsx'));
		})->name('fokus.download-template')->middleware('auth');
	});

	Route::prefix('fokus-mtc')->group(function () {
		Route::get('/', 'ProductFokusMtcController@baca')->name('fokusMtc')->middleware('auth');
		Route::get('/data', 'ProductFokusMtcController@data')->name('fokusMtc.data')->middleware('auth');
		Route::post('/create', 'ProductFokusMtcController@store')->name('fokusMtc.add')->middleware('auth');
		Route::put('/update/{id}', 'ProductFokusMtcController@update')->name('fokusMtc.update')->middleware('auth');
		Route::get('/delete/{id}', 'ProductFokusMtcController@delete')->name('fokusMtc.delete')->middleware('auth');
		Route::get('/export', 'ProductFokusMtcController@export')->name('fokusMtc.export')->middleware('auth');
		Route::post('/import', 'ProductFokusMtcController@importXLS')->name('fokusMtc.import')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/ProductFokusImport.xlsx'));
		})->name('fokusMtc.download-template')->middleware('auth');
	});

	//Fokus MD Pages
	Route::prefix('fokusMD')->group(function () {
		Route::get('/', 'ProductFokusMdController@baca')->name('fokusMD')->middleware('auth');
		Route::get('/data', 'ProductFokusMdController@data')->name('fokusMD.data')->middleware('auth');
		Route::post('/create', 'ProductFokusMdController@store')->name('fokusMD.add')->middleware('auth');
		Route::post('/import', 'ProductFokusMdController@import')->name('fokusMD.import')->middleware('auth');
		Route::get('/export', 'ProductFokusMdController@export')->name('fokusMD.export')->middleware('auth');
		Route::put('/update/{id}', 'ProductFokusMdController@update')->name('fokusMD.update')->middleware('auth');
		Route::get('/delete/{id}', 'ProductFokusMdController@delete')->name('fokusMD.delete')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/FokusMDImport.xlsx'));
		})->name('fokusMD.download-template')->middleware('auth');
	});

	//Fokus GTC Pages
	Route::prefix('fokusGTC')->group(function () {
		Route::get('/', 'ProductFokusGtcController@baca')->name('fokusGTC')->middleware('auth');
		Route::get('/data', 'ProductFokusGtcController@data')->name('fokusGTC.data')->middleware('auth');
		Route::post('/create', 'ProductFokusGtcController@store')->name('fokusGTC.add')->middleware('auth');
		Route::post('/import', 'ProductFokusGtcController@import')->name('fokusGTC.import')->middleware('auth');
		Route::get('/export', 'ProductFokusGtcController@export')->name('fokusGTC.export')->middleware('auth');
		Route::put('/update/{id}', 'ProductFokusGtcController@update')->name('fokusGTC.update')->middleware('auth');
		Route::get('/delete/{id}', 'ProductFokusGtcController@delete')->name('fokusGTC.delete')->middleware('auth');
		Route::get('/download-template', function(){
			return response()->download(public_path('assets/ProductFokusImportGTC.xlsx'));
		})->name('fokusGTC.download-template')->middleware('auth');
	});

	//Fokus Spg Pages
	Route::prefix('fokusSpg')->group(function () {
		Route::get('/', 'ProductFokusSpgController@baca')->name('fokusSpg')->middleware('auth');
		Route::get('/data', 'ProductFokusSpgController@data')->name('fokusSpg.data')->middleware('auth');
		Route::post('/create', 'ProductFokusSpgController@store')->name('fokusSpg.add')->middleware('auth');
		Route::post('/import', 'ProductFokusSpgController@import')->name('fokusSpg.import')->middleware('auth');
		Route::get('/export', 'ProductFokusSpgController@export')->name('fokusSpg.export')->middleware('auth');
		Route::put('/update/{id}', 'ProductFokusSpgController@update')->name('fokusSpg.update')->middleware('auth');
		Route::get('/delete/{id}', 'ProductFokusSpgController@delete')->name('fokusSpg.delete')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/FokusSpgImport.xlsx'));
		})->name('fokusSpg.download-template')->middleware('auth');
	});
});

// Master Target
Route::prefix('target')->group(function () {
	Route::prefix('mtc')->group(function () {
		Route::get('/', 'TargetController@baca')->name('mtc')->middleware('auth');
		Route::get('/data', 'TargetController@data')->name('mtc.data')->middleware('auth');
		Route::post('/create', 'TargetController@store')->name('mtc.add')->middleware('auth');
		Route::put('/update/{id}', 'TargetController@update')->name('mtc.update')->middleware('auth');
		Route::get('/delete/{id}', 'TargetController@delete')->name('mtc.delete')->middleware('auth');
		Route::get('/sample-form/download/{employee_id}', 'TargetController@downloadSampleForm')->name('mtc.download-sample')->middleware('auth');
		Route::any('/export', 'TargetController@exportXLS')->name('mtc.exportXLS')->middleware('auth');
	});

	Route::prefix('dc')->group(function () {
		Route::get('/', 'Target\DcController@baca')->name('target.dc')->middleware('auth');
		Route::get('/data', 'Target\DcController@data')->name('target.dc.data')->middleware('auth');
		Route::post('/create', 'Target\DcController@store')->name('target.dc.add')->middleware('auth');
		Route::put('/update/{id}', 'Target\DcController@update')->name('target.dc.update')->middleware('auth');
		Route::get('/delete/{id}', 'Target\DcController@delete')->name('target.dc.delete')->middleware('auth');
	});

	Route::prefix('smd')->group(function () {
		Route::get('/', 'Target\SmdController@baca')->name('target.smd')->middleware('auth');
		Route::get('/data', 'Target\SmdController@data')->name('target.smd.data')->middleware('auth');
		Route::get('/export', 'Target\SmdController@export')->name('target.smd.export')->middleware('auth');
		Route::post('/create', 'Target\SmdController@store')->name('target.smd.add')->middleware('auth');
		Route::put('/update/{id}', 'Target\SmdController@update')->name('target.smd.update')->middleware('auth');
		Route::get('/delete/{id}', 'Target\SmdController@delete')->name('target.smd.delete')->middleware('auth');
		Route::post('/import', 'Target\SmdController@importXLS')->name('target.smd.import')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/TargetSmdImport.xlsx'));
		})->name('targetsmd.download-template')->middleware('auth');
	});

	Route::prefix('spg')->group(function () {
		Route::get('/', 'Target\SpgController@baca')->name('target.spg')->middleware('auth');
		Route::get('/data', 'Target\SpgController@data')->name('target.spg.data')->middleware('auth');
		Route::post('/create', 'Target\SpgController@store')->name('target.spg.add')->middleware('auth');
		Route::put('/update/{id}', 'Target\SpgController@update')->name('target.spg.update')->middleware('auth');
		Route::get('/delete/{id}', 'Target\SpgController@delete')->name('target.spg.delete')->middleware('auth');
	});
});

/**
*	PlanDc Pages
*/
Route::prefix('planDc')->group(function () {
	Route::get('/', 'PlandcController@read')->name('planDc')->middleware('auth');
	Route::get('/data', 'PlandcController@data')->name('plan.data')->middleware('auth');
	Route::post('/import','PlandcController@import')->name('plan.import')->middleware('auth');
	Route::get('/update/{id}', 'PlandcController@readupdate')->name('ubah.plan')->middleware('auth');
	Route::put('/update/{id}', 'PlandcController@update')->name('plan.update')->middleware('auth');
	Route::get('/delete/{id}', 'PlandcController@delete')->name('plan.delete')->middleware('auth');
	Route::get('/export', 'PlandcController@exportXLS')->name('plan.export')->middleware('auth');
	Route::get('/download-template', function()
	{
		return response()->download(public_path('assets/PlanDcImport.xlsx'));
	})->name('plan.download-template')->middleware('auth');
});

/**
*	PropertiDc Pages
*/
Route::prefix('propertiDc')->group(function () {
	Route::get('/', 'PlandcController@readProperti')->name('propertiDc')->middleware('auth');
	Route::get('/data', 'PlandcController@dataProperti')->name('propertiDc.data')->middleware('auth');
	Route::post('/add','PlandcController@storeProperti')->name('properti.add')->middleware('auth');
	Route::post('/import','PlandcController@importProperti')->name('properti.import')->middleware('auth');
	Route::put('/update/{id}', 'PlandcController@updateProperti')->name('properti.update')->middleware('auth');
	Route::get('/delete/{id}', 'PlandcController@deleteProperti')->name('properti.delete')->middleware('auth');
	Route::get('/export', 'PlandcController@exportProperti')->name('properti.export')->middleware('auth');
	Route::get('/download-template', function()
	{
		return response()->download(public_path('assets/PropertiImport.xlsx'));
	})->name('properti.download-template')->middleware('auth');
});

/*
	Setting PF
*/
	Route::prefix('pf')->group(function () {
		Route::get('/', 'PfController@read')->name('pf')->middleware('auth');
		Route::get('/data', 'PfController@data')->name('pf.data')->middleware('auth');
		Route::post('/create', 'PfController@store')->name('pf.add')->middleware('auth');
		Route::put('/update/{id}', 'PfController@update')->name('pf.update')->middleware('auth');
		Route::get('/delete/{id}', 'PfController@delete')->name('pf.delete')->middleware('auth');
	});
/*
	USERS
*/

	Route::prefix('user')->group(function(){
		Route::get('/','UserController@index')->name('user')->middleware('auth');
		Route::post('/create','UserController@store')->name('user.add')->middleware('auth');
		Route::get('/data','UserController@data')->name('user.data')->middleware('auth');
		Route::put('/update/{id}','UserController@update')->name('user.update')->middleware('auth');

		Route::get('/delete/{id}','UserController@destroy')->name('user.delete')->middleware('auth');
	});


/*
	NEWS
*/

	Route::prefix('news')->group(function(){
		Route::get('/','NewsController@index')->name('news')->middleware('auth');
		Route::get('/data', 'NewsController@data')->name('news.data')->middleware('auth');
		Route::get('/create','NewsController@create')->name('tambah.news')->middleware('auth');
		Route::post('/store','NewsController@store')->name('news.store')->middleware('auth');
		Route::get('/edit/{id}','NewsController@edit')->name('ubah.news')->middleware('auth');
		Route::post('/update/{id}','NewsController@update')->name('update.news')->middleware('auth');
		Route::get('/delete/{id}','NewsController@delete')->name('news.delete')->middleware('auth');

	});

/*
	PRODUCT KNOWLEDGES
*/

	Route::prefix('pk')->group(function(){
		Route::get('/','PKController@index')->name('pk')->middleware('auth');
		Route::get('/data', 'PKController@data')->name('pk.data')->middleware('auth');
		Route::get('/create','PKController@create')->name('tambah.pk')->middleware('auth');
		Route::post('/store','PKController@store')->name('pk.store')->middleware('auth');
		Route::get('/edit/{id}','PKController@edit')->name('ubah.pk')->middleware('auth');
		Route::post('/update/{id}','PKController@update')->name('update.pk')->middleware('auth');
		Route::get('/delete/{id}','PKController@destroy')->name('pk.delete')->middleware('auth');

	});


/*
	FAQ
*/

	Route::prefix('faq')->group(function(){
		Route::get('/','FAQController@index')->name('faq')->middleware('auth');
		Route::get('/data', 'FAQController@data')->name('faq.data')->middleware('auth');
		Route::get('/create','FAQController@create')->name('tambah.faq')->middleware('auth');
		Route::post('/store','FAQController@store')->name('faq.store')->middleware('auth');
		Route::get('/edit/{id}','FAQController@edit')->name('ubah.faq')->middleware('auth');
		Route::post('/update/{id}','FAQController@update')->name('update.faq')->middleware('auth');
		Route::get('/delete/{id}','FAQController@delete')->name('faq.delete')->middleware('auth');
	});


/**
*	Company Pages
*/
Route::prefix('company')->group(function () {
	Route::get('/', 'CompanyController@baca')->name('company')->middleware('auth');
	Route::put('/update/{id}', 'CompanyController@update')->name('company.update')->middleware('auth');
});


// ***************** REPORTING (START) ***********************

Route::prefix('report')->group(function () {
	//GTC REPORT
	Route::prefix('gtc')->group(function () {
		// SMD REPORT
		Route::prefix('smd')->group(function () {

			Route::get('attendanceSMD', function(){
				return view('report.attendance-smd');
			})->name('report.attendance.smd')->middleware('auth');
			Route::post('/data/attendance', 'ReportController@SMDattendance')->name('data.attendance.smd.pasar')->middleware('auth');
			Route::get('/data', 'ReportController@SMDpasar')->name('data.smd.pasar')->middleware('auth');
			Route::get('/attendance/export', 'ReportController@exportAttandance')->name('export.attendance.smd.pasar')->middleware('auth');

			Route::prefix('stockist')->group(function () {
				Route::get('/', function(){
					$getId = array_column(\App\StockMdDetail::get(['id_product'])->toArray(),'id_product');
					$data['product'] = \App\Product::whereIn('id', $getId)->get();
					return view('report.smd.stockist', $data);
				})->name('report.stockist')->middleware('auth');
				Route::post('/data', 'ReportController@SMDstockist')->name('data.smd.stockist')->middleware('auth');
				Route::get('/export', 'ReportController@exportSMDstocking')->name('export.smd.stockist')->middleware('auth');
			});

			Route::prefix('cbd')->group(function () {
				Route::get('/', function(){
					$getId = array_column(\App\StockMdDetail::get(['id_product'])->toArray(),'id_product');
					$data['product'] = \App\Product::whereIn('id', $getId)->get();
					return view('report.smd.cbd', $data);
				})->name('report.cbd')->middleware('auth');
				Route::post('/data', 'ReportController@SMDcbd')->name('data.smd.cbd')->middleware('auth');
				Route::post('/export/{month?}/{year?}/{employee?}/{outlet?}/{new?}', 'ReportController@cbdGtcExportXLS')->name('export.smd.cbd')->middleware('auth');
			});

			Route::prefix('new-cbd')->group(function () {
				Route::get('/', function(){
					return view('report.smd.new-cbd');
				})->name('report.new-cbd')->middleware('auth');
				Route::post('/data', 'ReportController@SMDnewCbd')->name('data.smd.new-cbd')->middleware('auth');
				Route::post('/export/{month?}/{year?}/{employee?}/{outlet?}/{new?}', 'ReportController@cbdGtcExportXLS')->name('export.smd.new-cbd')->middleware('auth');
			});

			Route::prefix('sales')->group(function () {
				Route::get('/', function(){
					$getId = array_column(\App\SalesMdDetail::get(['id_product'])->toArray(),'id_product');
					$data['product'] = \App\Product::whereIn('id', $getId)->get();
					return view('report.smd.sales',$data);
				})->name('report.sales.pasar')->middleware('auth');
				Route::post('/data', 'ReportController@SMDsales')->name('data.sales.smd')->middleware('auth');
				Route::get('/export', 'ReportController@exportMdPasar')->name('export.sales.smd')->middleware('auth');
			});

			Route::prefix('achievement')->group(function () {
				Route::get('/', function(){
					return view('report.smd.achievement');
				})->name('report.achievement')->middleware('auth');
			});

			Route::prefix('distributorPf')->group(function () {
				Route::get('/', function(){
					$getId = array_column(\App\DistributionDetail::get(['id_product'])->toArray(),'id_product');
					$data['product'] = \App\Product::whereIn('id', $getId)->get();
					return view('report.smd.distpf',$data);
				})->name('report.dist.pf')->middleware('auth');
				Route::post('/data', 'ReportController@SMDdistpf')->name('data.distpf.smd')->middleware('auth');
				Route::get('/export', 'ReportController@exportSmdDist')->name('export.distpf.smd')->middleware('auth');
			});

			Route::prefix('summary')->group(function () {
				Route::get('/', function(){
					$getId = array_column(\App\StockMdDetail::get(['id_product'])->toArray(),'id_product');
					$data['product'] = \App\Product::whereIn('id', $getId)->get();
					return view('report.smd', $data);
				})->name('report.summary')->middleware('auth');
				Route::post('/data', 'ReportController@SMDpasar')->name('data.smd.pasar')->middleware('auth');
				Route::get('/export', 'ReportController@exportSMDsummary')->name('export.summary.smd')->middleware('auth');

			});

			Route::prefix('sales-summary')->group(function () {
				Route::get('/', function(){
					return view('report.smd.sales-summary');
				})->name('report.sales.summary.smd')->middleware('auth');
				Route::post('/data', 'ReportController@SMDsalesSummary')->name('smd.pasar.sales.summary.data')->middleware('auth');
				Route::any('/exportXLS/{filterdate?}', 'ReportController@SMDsalesSummaryExportXLS')->name('smd.pasar.sales.summary.exportXLS')->middleware('auth');
			});

			Route::prefix('target-kpi')->group(function () {
				Route::get('/', function(){
					return view('report.smd.target-kpi');
				})->name('report.target.kpi.smd')->middleware('auth');
				Route::post('/data', 'ReportController@SMDTargetKpi')->name('smd.pasar.target.kpi.data')->middleware('auth');
				Route::any('/exportXLS/{filterdate?}', 'ReportController@SMDTargetKpiExportXLS')->name('smd.pasar.target.kpi.exportXLS')->middleware('auth');
			});

			Route::prefix('kpi')->group(function () {
				Route::get('/', function(){
					return view('report.smd.kpi');
				})->name('report.kpi.smd')->middleware('auth');
				Route::post('/data', 'ReportController@SMDKpi')->name('smd.pasar.kpi.data')->middleware('auth');
				Route::any('/exportXLS/{filterdate?}', 'ReportController@SMDKpiExportXLS')->name('smd.pasar.kpi.exportXLS')->middleware('auth');
			});

		});

		// SPG PASAR
		Route::prefix('spg')->group(function () {
			Route::prefix('attendance')->group(function(){
				Route::get('/', function(){
					return view('report.spg.attendance');
				})->name('report.spg.attendance')->middleware('auth');
				Route::post('/data', 'ReportController@SPGattendance')->name('data.spg.attendance')->middleware('auth');
				Route::get('/export', 'ReportController@exportSpgAttandance')->name('export.spg.attendance')->middleware('auth');
			});
			Route::prefix('achievement')->group(function () {
				Route::get('/', function(){
					return view('report.spg.achievement');
				})->name('report.achievement.spg')->middleware('auth');
				Route::get('/data', 'ReportController@SPGsalesAchievement')->name('spg.pasar.sales.achievement.data')->middleware('auth');
				Route::post('/exportXLS', 'ReportController@SPGsalesAchievement_exportXLS')->name('spg.pasar.sales.achievement.data.exportXLS')->middleware('auth');
			});

			Route::prefix('sales')->group(function () {
				Route::get('/', function(){
					$getId = array_column(\App\SalesSpgPasarDetail::get(['id_product'])->toArray(),'id_product');
					$data['product'] = \App\Product::whereIn('id', $getId)->get();
					return view('report.spg.sales', $data);
				})->name('report.sales.spg')->middleware('auth');
				Route::post('/data', 'ReportController@SPGsales')->name('spg.pasar.sales.data')->middleware('auth');
				Route::get('/export', 'ReportController@exportSpgSales')->name('spg.pasar.sales.export')->middleware('auth');
			});

			Route::prefix('recap')->group(function () {
				Route::get('/', function(){
					return view('report.spg.recap');
				})->name('report.recap.spg')->middleware('auth');
				Route::post('/data', 'ReportController@SPGrekap')->name('spg.pasar.recap.data')->middleware('auth');
				Route::get('/export', 'ReportController@exportSPGrekap')->name('spg.pasar.recap.export')->middleware('auth');
			});

			Route::prefix('sales-summary')->group(function () {
				Route::get('/', function(){
					return view('report.spg.sales-summary');
				})->name('report.sales.summary.spg')->middleware('auth');
				Route::post('/data', 'ReportController@SPGsalesSummary')->name('spg.pasar.sales.summary.data')->middleware('auth');
				Route::post('/exportXLS/{subCategory?}/{date?}', 'ReportController@SPGsalesSummary_exportXLS')->name('spg.pasar.sales.summary.data.exportxls')->middleware('auth');
			});
		});

		// Demo Cooking Report
		Route::prefix('demo')->group(function () {
			Route::prefix('kunjungan')->group(function () {
				Route::get('/', function(){
					return view('report.democooking.kunjungan');
				})->name('report.demo.kunjungan')->middleware('auth');
				Route::post('/data', 'ReportController@kunjunganDc')->name('dc.kunjungan.data')->middleware('auth');
			});

			Route::prefix('sampling')->group(function () {
				Route::get('/', function(){
					$getId = array_column(\App\SamplingDcDetail::get(['id_product'])->toArray(),'id_product');
					$data['product'] = \App\Product::whereIn('id', $getId)->get();
					return view('report.democooking.sampling', $data);
				})->name('report.demo.sampling')->middleware('auth');
				Route::post('/data', 'ReportController@DcSampling')->name('dc.sampling.data')->middleware('auth');
				Route::get('/export', 'ReportController@exportDcSampling')->name('dc.sampling.export')->middleware('auth');
			});

			Route::prefix('salesDC')->group(function(){
				Route::get('/', function(){
					$getId = array_column(\App\SalesDcDetail::get(['id_product'])->toArray(),'id_product');
					$data['product'] = \App\Product::whereIn('id', $getId)->get();
					return view('report.democooking.salesDC', $data);
				})->name('report.demo.salesDC')->middleware('auth');
				Route::post('/data', 'ReportController@DcSales')->name('dc.sales.data')->middleware('auth');
				Route::get('/export', 'ReportController@exportDcSales')->name('dc.sales.export')->middleware('auth');
			});

			Route::prefix('activity')->group(function () {
				Route::get('/', function(){
					return view('report.democooking.activity');
				})->name('report.demo.activity')->middleware('auth');
				Route::post('/data', 'ReportController@documentationDC')->name('dc.documentation.data')->middleware('auth');
				Route::get('/export', 'ReportController@ExportdocumentationDC')->name('dc.documentation.export')->middleware('auth');
			});

			Route::prefix('cashAdvance')->group(function () {
				Route::get('/', 'CashAdvanceController@index')->name('report.demo.cashAdvance')->middleware('auth');
				Route::post('/data', 'CashAdvanceController@data')->name('report.demo.cashAdvance.data')->middleware('auth');
				Route::post('/import', 'CashAdvanceController@import')->name('report.demo.import')->middleware('auth');
				Route::any('/exportXLS/{subCategory?}/{date?}', 'CashAdvanceController@exportXLS')->name('report.demo.cashAdvance.exportXLS')->middleware('auth');
				Route::get('/download-template', function()
				{
					return response()->download(public_path('assets/CashAdvanceImport.xlsx'));
				})->name('report.dc.cash.download-template')->middleware('auth');
			});

			Route::prefix('inventori')->group(function(){
				Route::get('/', function(){
					return view('report.democooking.inventori');
				})->name('report.demo.inventori')->middleware('auth');
				Route::post('/data/{employee?}', 'ReportController@inventoriDC')->name('dc.inventori.data')->middleware('auth');
				Route::post('/add', 'ReportController@inventoriDCAdd')->name('dc.inventori.data.add')->middleware('auth');
				Route::any('/exportXLS', 'ReportController@inventoriDCExportXLS')->name('dc.inventori.data.exportXLS')->middleware('auth');
			});

		});

		// Motorik Report
		Route::prefix('motorik')->group(function () {
			Route::prefix('attendance')->group(function () {
				Route::get('/', function(){
					return view('report.motorik.attendance');
				})->name('report.motorik.attendance')->middleware('auth');
				Route::post('/data', 'ReportController@Motorikattendance')->name('report.motorik.attendance.data')->middleware('auth');
				Route::get('/export', 'ReportController@exportMptorikAttandance')->name('report.motorik.attendance.export')->middleware('auth');
			});

			Route::prefix('distPF')->group(function () {
				Route::get('/', function(){
					$getId = array_column(\App\DistributionMotoricDetail::get(['id_product'])->toArray(),'id_product');
					$data['product'] = \App\Product::whereIn('id', $getId)->get();
					return view('report.motorik.distPF', $data);
				})->name('report.motorik.distPF')->middleware('auth');
				Route::post('/data', 'ReportController@motorikDistPF')->name('report.motorik.distPF.data')->middleware('auth');
				Route::get('/export', 'ReportController@exportMotorikDistPF')->name('report.motorik.distPF.export')->middleware('auth');
			});

			Route::prefix('sales')->group(function () {
				Route::get('/', function(){
					$getId = array_column(\App\SalesMotoricDetail::get(['id_product'])->toArray(),'id_product');
					$data['product'] = \App\Product::whereIn('id', $getId)->get();
					return view('report.motorik.sales', $data);
				})->name('report.motorik.sales')->middleware('auth');
				Route::post('/data', 'ReportController@MotorikSales')->name('report.motorik.sales.data')->middleware('auth');
				Route::get('/export', 'ReportController@exportMotorikSales')->name('report.motorik.sales.export')->middleware('auth');
			});

		});
	});

Route::prefix('mtc')->group(function () {


	Route::prefix('attendance')->group(function(){
		Route::get('/', 'AttendanceController@index')->name('attendance')->middleware('auth');
		Route::post('/data', 'AttendanceController@data')->name('attendance.data')->middleware('auth');
		Route::get('/exportXLS', 'AttendanceController@exportXLS')->name('attendance.exportXLS')->middleware('auth');
	});

	Route::prefix('salesmtc')->group(function () {
		Route::get('/', 'ReportController@salesMtcIndex')->name('salesmtc')->middleware('auth');
		Route::post('/data', 'ReportController@salesMtcDataSalesAlt')->name('salesmtc.data')->middleware('auth');
	});

	Route::prefix('achievement')->group(function () {
		Route::get('/', 'ReportController@achievementSalesMtcIndex')->name('achievement-salesmtc')->middleware('auth');
		Route::post('/data-spg', 'ReportController@achievementSalesMtcDataSPG')->name('achievement-salesmtc-spg.data')->middleware('auth');
		Route::post('/data-md', 'ReportController@achievementSalesMtcDataMD')->name('achievement-salesmtc-md.data')->middleware('auth');
		Route::post('/data-tl', 'ReportController@achievementSalesMtcDataTL')->name('achievement-salesmtc-tl.data')->middleware('auth');
		Route::any('/exportXLS/{filterDate?}', 'ReportController@achievementSalesMtcExportXLS')->name('achievement-salesmtc.exportxls')->middleware('auth');
	});


	Route::prefix('priceData')->group(function () {
		Route::get('/', 'ReportController@priceDataIndex')->name('priceData')->middleware('auth');
		Route::get('/summary', 'ReportController@priceSummary')->name('priceData.summary')->middleware('auth');
		Route::get('/dataSummary', 'ReportController@priceDataSummary')->name('priceData.dataSummary')->middleware('auth');
		Route::get('/row', 'ReportController@priceRow')->name('priceData.row')->middleware('auth');
		Route::get('/dataRow', 'ReportController@priceDataRow')->name('priceData.dataRow')->middleware('auth');
		Route::get('/vs', 'ReportController@PriceVsIndex')->name('priceData.vs')->middleware('auth');
		Route::get('/dataVs', 'ReportController@priceDataVs')->name('priceData.dataVs')->middleware('auth');
		Route::post('/store', 'ReportController@store')->name('priceData.store')->middleware('auth');
		Route::post('/edit/{id}', 'ReportController@priceDataUpdate')->name('priceData.edit')->middleware('auth');
		Route::post('/import', 'ImportQueueController@ImportpriceData')->name('priceData.import')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/SellinImport.xlsx'));
		})->name('SellIn.download-template')->middleware('auth');
	});


	Route::prefix('availability')->group(function () {
		Route::get('/', 'ReportController@availabilityIndex')->name('availability')->middleware('auth');
		Route::get('/row', 'ReportController@availabilityRow')->name('availability.row')->middleware('auth');
		Route::get('/dataAccountRow', 'ReportController@availabilityAccountRowData')->name('availability.dataAccountRow')->middleware('auth');
		Route::get('/dataArea', 'ReportController@availabilityAreaData')->name('availability.dataArea')->middleware('auth');
		Route::get('/dataAccount', 'ReportController@availabilityAccountData')->name('availability.dataAccount')->middleware('auth');
		Route::any('/exportXLS', 'ReportController@availabilityExportXLS')->name('availability.exportXLS')->middleware('auth');
		Route::post('/edit/{id}', 'ReportController@availabilityUpdate')->name('availability.edit')->middleware('auth');
		Route::post('/import', 'ImportQueueController@Importavailability')->name('availability.import')->middleware('auth');
		Route::get('/download-template', function(){
			return response()->download(public_path('assets/SellinImport.xlsx'));
		})->name('SellIn.download-template')->middleware('auth');
	});
	
	Route::prefix('display_share')->group(function () {

		Route::get('/', 'ReportController@displayShareIndex')->name('display_share')->middleware('auth');

		Route::get('/dataSpg', 'ReportController@displayShareSpgData')->name('display_share.dataSpg')->middleware('auth');
		Route::any('/dataSpg/exportXLS', 'ReportController@displayShareSpgDataExportXLS')->name('display_share.dataSpg.exportXLS')->middleware('auth');

		Route::get('/ach', 'ReportController@displayShareAch')->name('display_share.ach')->middleware('auth');
		Route::get('/reportDataArea', 'ReportController@displayShareReportAreaData')->name('display_share.reportDataArea')->middleware('auth');
		Route::get('/reportDataSpg', 'ReportController@displayShareReportSpgData')->name('display_share.reportDataSpg')->middleware('auth');
		Route::get('/reportDataMd', 'ReportController@displayShareReportMdData')->name('display_share.reportDataMd')->middleware('auth');
		Route::any('/ach/exportXLS', 'ReportController@displayShareReportExportXLS')->name('display_share.report.exportXLS')->middleware('auth');

		Route::post('/edit/{id}', 'ReportController@displayShareUpdate')->name('display_share.edit')->middleware('auth');
		Route::post('/import', 'ImportQueueController@ImportdisplayShare')->name('display_share.import')->middleware('auth');
		Route::get('/download-template', function(){
			return response()->download(public_path('assets/SellinImport.xlsx'));
		})->name('SellIn.download-template')->middleware('auth');
	});
	
	Route::prefix('additional_display')->group(function () {
		Route::get('/', 'ReportController@additionalDisplayIndex')->name('additional_display')->middleware('auth');
		Route::get('/dataArea', 'ReportController@additionalDisplayAreaData')->name('additional_display.dataArea')->middleware('auth');
		Route::get('/dataSpg', 'ReportController@additionalDisplaySpgData')->name('additional_display.dataSpg')->middleware('auth');
		Route::get('/ach', 'ReportController@additionalDisplayAch')->name('additional_display.ach')->middleware('auth');
		Route::any('/ach/exportXLS', 'ReportController@additionalDisplayExportXLS')->name('additional_display.exportXLS')->middleware('auth');
		Route::get('/reportDataArea', 'ReportController@additionalDisplayReportAreaData')->name('additional_display.reportDataArea')->middleware('auth');
		Route::get('/reportDataSpg', 'ReportController@additionalDisplayReportSpgData')->name('additional_display.reportDataSpg')->middleware('auth');
		Route::get('/reportDataMd', 'ReportController@additionalDisplayReportMdData')->name('additional_display.reportDataMd')->middleware('auth');
		Route::post('/edit/{id}', 'ReportController@additionalDisplayUpdate')->name('additional_display.edit')->middleware('auth');
		Route::post('/import', 'ImportQueueController@ImportadditionalDisplay')->name('additional_display.import')->middleware('auth');
		Route::get('/download-template', function()
		{
			return response()->download(public_path('assets/SellinImport.xlsx'));
		})->name('SellIn.download-template')->middleware('auth');
	});

});


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

		Route::get('/tes', 'ReportController@tes')->name('sellin.export')->middleware('auth');
	});

});
Route::get('/sellout', 'DashboardController@dashboard')->name('sellout')->middleware('auth');
});

Route::get('/stock', 'DashboardController@dashboard')->name('stock')->middleware('auth');

Route::get('/achievement/{date?}', 'ReportController@getAchievement')->name('achievement')->middleware('auth');

Route::post('/export', 'ReportController@export')->name('report.export')->middleware('auth');

// ***************** REPORTING (END) ***********************

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
	Route::prefix('export-download')->group(function () {
		Route::get('/', 'UtilitiesController@reportDownloadIndex')->name('export-download')->middleware('auth');
		Route::post('/data', 'UtilitiesController@reportDownloadData')->name('export-download.data')->middleware('auth');
		Route::post('/explain/{param}', 'UtilitiesController@reportDownloadAddExplanation')->name('export-download.explain')->middleware('auth');
	});	
});

/**
*	Select2
*/
Route::prefix('select2')->group(function () {
	Route::post('/region-select2', 'RegionController@getDataWithFilters')->name('region-select2');
	Route::post('/area-select2', 'AreaController@getDataWithFilters')->name('area-select2');
	Route::post('/agency-select2', 'AgencyController@getDataWithFilters')->name('agency-select2');
	Route::post('/pasar-select2', 'PasarController@getDataWithFilters')->name('pasar-select2');
	Route::post('/outlet-select2', 'OutletController@getDataWithFilters')->name('outlet-select2');
	Route::post('/sub-area-select2', 'SubareaController@getDataWithFilters')->name('sub-area-select2');
	Route::post('/employee-select2', 'EmployeeController@getDataWithFilters')->name('employee-select2');
	Route::post('/employee-select2-for-report', 'EmployeeController@getDataWithFiltersForReport')->name('employee-select2-for-report');
	Route::post('/store-select2', 'StoreController@getDataWithFilters')->name('store-select2');
	Route::post('/block-select2', 'EmployeeController@getDataWithFiltersBlock')->name('block-select2');
	Route::post('/product-select2', 'ProductController@getDataWithFilters')->name('product-select2');
	Route::post('/sub-category-select2', 'SubCategoryController@getDataWithFilters')->name('sub-category-select2');
	Route::post('/employee-is-tl-select2', 'EmployeeController@getDataIsTL')->name('employee-is-tl-select2');
	Route::get('/product-byCategory-select2/{param}', 'ProductController@getProductByCategory')->name('product-byCategory-select2');
});

Route::prefix('promoactivity')->group(function(){
	Route::get('/','PromoActivityController@index')->name('promoactivity')->middleware('auth');
	Route::get('/data', 'PromoActivityController@data')->name('pa.data')->middleware('auth');
	Route::get('/create','PromoActivityController@create')->name('tambah.pa')->middleware('auth');
	Route::post('/store','PromoActivityController@store')->name('pa.store')->middleware('auth');
	Route::get('/edit/{id}','PromoActivityController@edit')->name('ubah.pa')->middleware('auth');
	Route::post('/update/{id}','PromoActivityController@update')->name('update.pa')->middleware('auth');
	Route::get('/delete/{id}','PromoActivityController@delete')->name('pa.delete')->middleware('auth');
	Route::get('/exportXLS','PromoActivityController@exportXLS')->name('pa.exportXLS')->middleware('auth');
	Route::post('/importXLS','PromoActivityController@importXLS')->name('pa.importXLS')->middleware('auth');
});

/**
*	Necessary Data
*/

Route::prefix('data')->group(function () {
	Route::post('/subcategory-product-data', 'ReportController@SPGsalesSummaryHeader')->name('subcategory-product-data');
	Route::post('/product-fokus-gtc-data', 'ReportController@SMDsalesSummaryHeader')->name('product-fokus-gtc-data');
	Route::post('/product-fokus-gtc-cat1-cat2', 'ReportController@SMDCat1Cat2')->name('product-fokus-gtc-cat1-cat2');
});


Auth::routes();

Route::get('/tes', 'ReportController@sellInData');