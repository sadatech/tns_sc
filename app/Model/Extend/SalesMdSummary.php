<?php

namespace App\Model\Extend;

use Illuminate\Database\Eloquent\Model;
use App\SalesMd as SalesMd;
use App\StockMdHeader;
use Carbon\Carbon;
use App\AttendanceOutlet;
use App\SubCategory;
use App\FokusProduct;
use App\Distribution;
use App\Cbd;
use App\StockMdDetail;
use DB;
use App\ProductFokusGtc;

class SalesMdSummary extends SalesMd
{
    protected $appends = ['new_id', 'area', 'nama_smd', 'jabatan', 'nama_pasar', 'nama_stokist', 'tanggal', 'call', 'ro', 'sub_category_for_pf_list', 'distribusi_pf', 'sales_pf', 'eff_call', 'value_pf', 'periode', 'value_non_pf', 'cbd', 'oos', 'value_total'];

    public function getNewIdAttribute(){
        return $this->id;
    }

    public function getAreaAttribute(){
        return @$this->outlet->employeePasar->pasar->subarea->area->name;
    }

    public function getNamaSmdAttribute(){
        return @$this->employee->name;
    }

    public function getJabatanAttribute(){
        return 'SMD';
    }

    public function getNamaPasarAttribute(){
        return @$this->outlet->employeePasar->pasar->name;
    }

    public function getPeriodeAttribute(){
    	return Carbon::parse($this->date)->format('Y-m-d');
    }

    public function getNamaStokistAttribute(){
        return implode(", ", StockMdHeader::whereMonth('date', Carbon::parse($this->date)->month)
                             ->whereYear('date', Carbon::parse($this->date)->year)
                             ->where('id_employee', $this->id_employee)
                             ->where('id_pasar', $this->outlet->employeePasar->pasar->id)
                             ->pluck('stockist')->toArray());
    }

    public function getTanggalAttribute(){
        return Carbon::parse($this->date)->format('d F Y');
    }

    public function getCallAttribute(){
        return AttendanceOutlet::whereHas('attendance', function ($query){
				        			return $query->whereMonth('date', Carbon::parse($this->date)->month)
				        						 ->whereYear('date', Carbon::parse($this->date)->year)
				        						 ->where('id_employee', $this->id_employee);
				        		})
        						->whereHas('outlet.employeePasar.pasar', function ($query){
				        			return $query->where('id', $this->outlet->employeePasar->pasar->id);
				        		})
        						->count();
    }

    public function getRoAttribute(){
        return 'Under Construction';
    }

    public function getSubCategoryForPfListAttribute(){
        // $id_subcategories = array_unique(FokusProduct::whereHas('pf.Fokus.channel', function ($query){
        //                     return $query->where('name', 'GTC');
        //                 })
        //                 ->whereHas('pf', function ($query) {
        //                     return $query->whereDate('from', '<=', $this->periode)
        //                                  ->whereDate('to', '>=', $this->periode);
        //                 })                        
        //                 ->get()->pluck('product.subcategory.id')->toArray());

        $id_subcategories = array_unique(ProductFokusGtc::whereDate('from', '<=', $this->periode)->whereDate('to', '>=', $this->periode)->get()->pluck('product.subcategory.id')->toArray());

        return SubCategory::whereIn('id', $id_subcategories)->get();
    }

    public function getDistribusiPfAttribute(){
    	$result = array();

        foreach ($this->sub_category_for_pf_list as $item) {

        	$data = Distribution::join('distribution_details', 'distribution_details.id_distribution', 'distributions.id')
        						->join('products', 'products.id', 'distribution_details.id_product')
        						->join('sub_categories', 'sub_categories.id', 'products.id_subcategory')
        						->where('sub_categories.id', $item->id)
        						->groupBy('distributions.id_outlet')
        						->count();
        	

            $result[$item->id] = $data;
        }

        return $result;
    }

    public function getSalesPfAttribute(){

    	$result = array();

    	// return SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
     //    					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
     //    					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
     //    					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
     //    					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
     //    					->join('areas', 'areas.id', 'sub_areas.id_area')
     //    					->join('products', 'products.id', 'sales_md_details.id_product')
     //    					->join('sub_categories', 'sub_categories.id', 'products.id_subcategory')
     //    					->join('fokus_products', 'products.id', 'fokus_products.id_product')
     //    					->join('product_fokuses', 'product_fokuses.id', 'fokus_products.id_pf')
     //    					->join('fokus_channels', 'product_fokuses.id', 'fokus_channels.id_pf')
     //    					->join('channels', 'channels.id', 'fokus_channels.id_channel')
     //    					->join('fokus_areas as fa2', 'product_fokuses.id', 'fa2.id_pf')
     //    					->where('channels.name', 'GTC')
     //    					->whereDate('product_fokuses.from', '<=', $periode)
     //    					->whereDate('product_fokuses.to', '>=', $periode)
     //    					->count();

    	foreach ($this->sub_category_for_pf_list as $item) {

        	// $data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        	// 				->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
        	// 				->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
        	// 				->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
        	// 				->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
        	// 				->join('areas', 'areas.id', 'sub_areas.id_area')
        	// 				->join('products', 'products.id', 'sales_md_details.id_product')
        	// 				->join('sub_categories', 'sub_categories.id', 'products.id_subcategory')
        	// 				->join('fokus_products', 'products.id', 'fokus_products.id_product')
        	// 				->join('product_fokuses', 'product_fokuses.id', 'fokus_products.id_pf')
        	// 				->join('fokus_channels', 'product_fokuses.id', 'fokus_channels.id_pf')
        	// 				->join('channels', 'channels.id', 'fokus_channels.id_channel')
        	// 				->join('fokus_areas', 'product_fokuses.id', 'fokus_areas.id_pf')
        	// 				->whereNotNull('fokus_areas.id_area')
        	// 				->where('fokus_products.deleted_at', null)
        	// 				->where('fokus_channels.deleted_at', null)
        	// 				->where('fokus_areas.deleted_at', null)
        	// 				->where('product_fokuses.deleted_at', null)
        	// 				->whereDate('sales_mds.date', $this->periode)
        	// 				->whereDate('product_fokuses.from', '<=', $this->periode)
        	// 				->whereDate('product_fokuses.to', '>=', $this->periode)
        	// 				->where('channels.name', 'GTC')
        	// 				->where('sub_categories.id', $item->id)
        	// 				// ->count();
        	// 				->select(DB::raw('(sum(sales_md_details.qty/products.pack)) as qty')); 
        					// ->select(DB::raw('(sum(sales_md_details.qty)) as qty')); 

        	$data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->join('sub_categories', 'sub_categories.id', 'products.id_subcategory')
        					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
        					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
        					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
        					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
        					->join('areas', 'areas.id', 'sub_areas.id_area')
        					->join('product_fokus_gtcs', function ($join){
        						return $join->on('product_fokus_gtcs.id_product', 'products.id')
        									->on('product_fokus_gtcs.id_area', 'areas.id');
        					})
        					->whereDate('sales_mds.date', $this->periode)
        					->whereDate('product_fokus_gtcs.from', '<=', $this->periode)
        					->whereDate('product_fokus_gtcs.to', '>=', $this->periode)
        					->where('sub_categories.id', $item->id)
        					->whereNull('product_fokus_gtcs.deleted_at')
        					->select(DB::raw('(sum(sales_md_details.qty/products.pack)) as qty'));

        	// $data2 = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        	// 				->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
        	// 				->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
        	// 				->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
        	// 				->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
        	// 				->join('areas', 'areas.id', 'sub_areas.id_area')
        	// 				->join('products', 'products.id', 'sales_md_details.id_product')
        	// 				->join('sub_categories', 'sub_categories.id', 'products.id_subcategory')
        	// 				->join('fokus_products', 'products.id', 'fokus_products.id_product')
        	// 				->join('product_fokuses', 'product_fokuses.id', 'fokus_products.id_pf')
        	// 				->join('fokus_channels', 'product_fokuses.id', 'fokus_channels.id_pf')
        	// 				->join('channels', 'channels.id', 'fokus_channels.id_channel')  
        	// 				->join('fokus_areas', 'product_fokuses.id', 'fokus_areas.id_pf')
        	// 				->where('fokus_products.deleted_at', null)
        	// 				->where('fokus_channels.deleted_at', null)
        	// 				->where('product_fokuses.deleted_at', null)
        	// 				->whereNull('fokus_areas.id_area')
        	// 				->whereDate('sales_mds.date', $this->periode)
        	// 				->whereDate('product_fokuses.from', '<=', $this->periode)
        	// 				->whereDate('product_fokuses.to', '>=', $this->periode)
        	// 				->where('channels.name', 'GTC')
        	// 				->where('sub_categories.id', $item->id)
        	// 				// ->count();
        	// 				->select(DB::raw('(sum(sales_md_details.qty/products.pack)) as qty'));      	
        					// ->select(DB::raw('(sum(sales_md_details.qty)) as qty'));

        	$data2 = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->join('sub_categories', 'sub_categories.id', 'products.id_subcategory')
        					->join('product_fokus_gtcs', 'products.id', 'product_fokus_gtcs.id_product')
        					->whereDate('sales_mds.date', $this->periode)
        					->whereDate('product_fokus_gtcs.from', '<=', $this->periode)
        					->whereDate('product_fokus_gtcs.to', '>=', $this->periode)
        					->where('sub_categories.id', $item->id)
        					->where('product_fokus_gtcs.id_area', null)
        					->whereNull('product_fokus_gtcs.deleted_at')
        					->select(DB::raw('(sum(sales_md_details.qty/products.pack)) as qty'));

        	// $result[$item->id] = $data->first()->qty;
            $result[$item->id] = ($data->first()->qty + $data2->first()->qty);
        	// $result[$item->id] = $data2;
        }

    	return $result;
    }

    public function getEffCallAttribute(){
    	return SalesMd::whereDate('sales_mds.date', $this->periode)->count();
    }

    public function getValuePfAttribute(){

    	// $data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
     //    					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
     //    					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
     //    					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
     //    					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
     //    					->join('areas', 'areas.id', 'sub_areas.id_area')
     //    					->join('products', 'products.id', 'sales_md_details.id_product')
     //    					->join('prices', function($join){
     //                            return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
     //                        })
     //    					->join('fokus_products', 'products.id', 'fokus_products.id_product')
     //    					->join('product_fokuses', 'product_fokuses.id', 'fokus_products.id_pf')
     //    					->join('fokus_channels', 'product_fokuses.id', 'fokus_channels.id_pf')
     //    					->join('channels', 'channels.id', 'fokus_channels.id_channel')
     //    					->join('fokus_areas', 'product_fokuses.id', 'fokus_areas.id_pf')
     //    					->whereNotNull('fokus_areas.id_area')
     //    					->where('fokus_products.deleted_at', null)
     //    					->where('fokus_channels.deleted_at', null)
     //    					->where('fokus_areas.deleted_at', null)
     //    					->where('product_fokuses.deleted_at', null)
     //    					->whereDate('sales_mds.date', $this->periode)
     //    					->whereDate('product_fokuses.from', '<=', $this->periode)
     //    					->whereDate('product_fokuses.to', '>=', $this->periode)
     //    					->where('channels.name', 'GTC')
     //    					->select(DB::raw('(sum(sales_md_details.qty*price)) as value'));

       	$data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->join('prices', function($join){
                                return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
                            })
        					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
        					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
        					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
        					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
        					->join('areas', 'areas.id', 'sub_areas.id_area')
        					->join('product_fokus_gtcs', function ($join){
        						return $join->on('product_fokus_gtcs.id_product', 'products.id')
        									->on('product_fokus_gtcs.id_area', 'areas.id');
        					})
        					->whereDate('sales_mds.date', $this->periode)
        					->whereDate('product_fokus_gtcs.from', '<=', $this->periode)
        					->whereDate('product_fokus_gtcs.to', '>=', $this->periode)
        					->whereNull('product_fokus_gtcs.deleted_at')
        					->whereNull('prices.deleted_at')
        					->select(DB::raw('(sum(sales_md_details.qty*price)) as value'));


        // $data2 = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        // 					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
        // 					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
        // 					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
        // 					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
        // 					->join('areas', 'areas.id', 'sub_areas.id_area')
        // 					->join('products', 'products.id', 'sales_md_details.id_product')
        // 					->join('prices', function($join){
        //                         return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
        //                     })
        // 					->join('fokus_products', 'products.id', 'fokus_products.id_product')
        // 					->join('product_fokuses', 'product_fokuses.id', 'fokus_products.id_pf')
        // 					->join('fokus_channels', 'product_fokuses.id', 'fokus_channels.id_pf')
        // 					->join('channels', 'channels.id', 'fokus_channels.id_channel')  
        // 					->join('fokus_areas', 'product_fokuses.id', 'fokus_areas.id_pf')
        // 					->where('fokus_products.deleted_at', null)
        // 					->where('fokus_channels.deleted_at', null)
        // 					->where('product_fokuses.deleted_at', null)
        // 					->whereNull('fokus_areas.id_area')
        // 					->whereDate('sales_mds.date', $this->periode)
        // 					->whereDate('product_fokuses.from', '<=', $this->periode)
        // 					->whereDate('product_fokuses.to', '>=', $this->periode)
        // 					->where('channels.name', 'GTC')
        // 					->select(DB::raw('(sum(sales_md_details.qty*price)) as value'));

        $data2 = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->join('prices', function($join){
                                return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
                            })
        					->join('product_fokus_gtcs', 'products.id', 'product_fokus_gtcs.id_product')
        					->whereDate('sales_mds.date', $this->periode)
        					->whereDate('product_fokus_gtcs.from', '<=', $this->periode)
        					->whereDate('product_fokus_gtcs.to', '>=', $this->periode)
        					->where('product_fokus_gtcs.id_area', null)
        					->whereNull('product_fokus_gtcs.deleted_at')
        					->whereNull('prices.deleted_at')
        					->select(DB::raw('(sum(sales_md_details.qty*price)) as value'));

        return ($data->first()->value + $data2->first()->value);
    }

    public function getValueNonPfAttribute(){

    	/* NOT PF WITH NO LINK */
    	$data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->leftJoin('prices', function($join){
                                return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
                            })
        					->join('sub_categories', 'sub_categories.id', 'products.id_subcategory')
        					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
        					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
        					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
        					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
        					->join('areas', 'areas.id', 'sub_areas.id_area')
        					->leftJoin('product_fokus_gtcs', 'product_fokus_gtcs.id_product', 'products.id')
        					->whereNull('product_fokus_gtcs.id')
        					->whereDate('sales_mds.date', $this->periode)
        					// ->whereNull('product_fokus_gtcs.deleted_at')
        					->whereNull('prices.deleted_at')
        					// ->pluck('products.id');
        					->select(DB::raw('(sum(sales_md_details.qty*price)) as value'));

       	/* NOT PF SOFT DELETE */
    	$data2 = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->join('prices', function($join){
                                return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
                            })
        					->join('product_fokus_gtcs', 'product_fokus_gtcs.id_product', 'products.id')
        					->whereDate('sales_mds.date', $this->periode)
        					->whereNotNull('product_fokus_gtcs.deleted_at')
        					->whereNull('prices.deleted_at')
        					// ->pluck('products.id');
        					->select(DB::raw('(sum(sales_md_details.qty*price)) as value'));


        /* PF OVER OR LESS PERIODE */
        $data3 = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->leftJoin('prices', function($join){
                                return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
                            })
        					->join('sub_categories', 'sub_categories.id', 'products.id_subcategory')
        					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
        					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
        					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
        					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
        					->join('areas', 'areas.id', 'sub_areas.id_area')
        					->join('product_fokus_gtcs', function ($join){
        						return $join->on('product_fokus_gtcs.id_product', 'products.id')
        									->on('product_fokus_gtcs.id_area', 'areas.id');
        					})
        					->whereDate('sales_mds.date', $this->periode)
        					->whereDate('product_fokus_gtcs.from', '>', $this->periode)
        					->whereDate('product_fokus_gtcs.to', '<', $this->periode)
        					->whereNull('product_fokus_gtcs.deleted_at')
        					->whereNull('prices.deleted_at')
        					// ->count();
        					->select(DB::raw('(sum(sales_md_details.qty*price)) as value'));
        

        /* PF WITH DIFF AREA */
        $data4 = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->leftJoin('prices', function($join){
                                return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
                            })
        					->join('sub_categories', 'sub_categories.id', 'products.id_subcategory')
        					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
        					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
        					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
        					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
        					->join('areas', 'areas.id', 'sub_areas.id_area')
        					->join('product_fokus_gtcs', function ($join){
        						return $join->on('product_fokus_gtcs.id_product', 'products.id')
        									->on('product_fokus_gtcs.id_area', '<>', 'areas.id');
        					})
        					->whereDate('sales_mds.date', $this->periode)
        					->whereDate('product_fokus_gtcs.from', '<=', $this->periode)
        					->whereDate('product_fokus_gtcs.to', '>=', $this->periode)
        					->whereNull('product_fokus_gtcs.deleted_at')
        					->whereNull('prices.deleted_at')
        					// ->count();
        					->select(DB::raw('(sum(sales_md_details.qty*price)) as value'));

        return ($data->first()->value + $data2->first()->value + $data3->first()->value + $data4->first()->value);
        // return $data2;

    }

    // public function getValueNonPf(){

    // 	$data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
    //     					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
    //     					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
    //     					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
    //     					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
    //     					->join('areas', 'areas.id', 'sub_areas.id_area')
    //     					->join('products', 'products.id', 'sales_md_details.id_product')
    //     					->join('prices', function($join){
    //                             return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
    //                         })
    //     					->leftJoin('fokus_products', 'products.id', 'fokus_products.id_product')
    //     					// ->leftJoin('product_fokuses', 'product_fokuses.id', 'fokus_products.id_pf')
    //     					// ->leftJoin('fokus_channels', 'product_fokuses.id', 'fokus_channels.id_pf')
    //     					// ->leftJoin('channels', 'channels.id', 'fokus_channels.id_channel')
    //     					// ->leftJoin('fokus_areas', 'product_fokuses.id', 'fokus_areas.id_pf')
    //     					->whereNull('fokus_products.id')
    //     					// ->where('fokus_products.deleted_at', null)
    //     					// ->where('fokus_channels.deleted_at', null)
    //     					// ->where('fokus_areas.deleted_at', null)
    //     					// ->where('product_fokuses.deleted_at', null)
    //     					->whereDate('sales_mds.date', $this->periode)
    //     					// ->whereDate('product_fokuses.from', '<=', $this->periode)
    //     					// ->whereDate('product_fokuses.to', '>=', $this->periode)
    //     					// ->where('channels.name', 'GTC')
    //     					->count();
    //     					// ->select(DB::raw('(sum(sales_md_details.qty*price)) as value'));

    //     $data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
    //     					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
    //     					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
    //     					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
    //     					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
    //     					->join('areas', 'areas.id', 'sub_areas.id_area')
    //     					->join('products', 'products.id', 'sales_md_details.id_product')
    //     					->join('prices', function($join){
    //                             return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
    //                         })
    //     					->leftJoin('fokus_products', 'products.id', 'fokus_products.id_product')
    //     					->leftJoin('product_fokuses', 'product_fokuses.id', 'fokus_products.id_pf')
    //     					// ->leftJoin('fokus_channels', 'product_fokuses.id', 'fokus_channels.id_pf')
    //     					// ->leftJoin('channels', 'channels.id', 'fokus_channels.id_channel')
    //     					// ->leftJoin('fokus_areas', 'product_fokuses.id', 'fokus_areas.id_pf')
    //     					// ->whereNull('fokus_products.id')
    //     					// ->where('fokus_products.deleted_at', null)
    //     					// ->where('fokus_channels.deleted_at', null)
    //     					// ->where('fokus_areas.deleted_at', null)
    //     					// ->where('product_fokuses.deleted_at', null)
    //     					->whereDate('sales_mds.date', $this->periode)
    //     					->where(function ($query){
    //     						return $query->whereDate('product_fokuses.from', '>', $this->periode)
    //     									 ->orWhereDate('product_fokuses.to', '<', $this->periode);
    //     					})
    //     					// ->where('channels.name', '<>', 'GTC')
    //     					->count();

    //     $data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
    //     					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
    //     					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
    //     					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
    //     					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
    //     					->join('areas', 'areas.id', 'sub_areas.id_area')
    //     					->join('products', 'products.id', 'sales_md_details.id_product')
    //     					->join('prices', function($join){
    //                             return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
    //                         })
    //     					->leftJoin('fokus_products', 'products.id', 'fokus_products.id_product')
    //     					->leftJoin('product_fokuses', 'product_fokuses.id', 'fokus_products.id_pf')
    //     					->leftJoin('fokus_channels', 'product_fokuses.id', 'fokus_channels.id_pf')
    //     					->leftJoin('channels', 'channels.id', 'fokus_channels.id_channel')
    //     					// ->leftJoin('fokus_areas', 'product_fokuses.id', 'fokus_areas.id_pf')
    //     					// ->whereNull('fokus_products.id')
    //     					// ->where('fokus_products.deleted_at', null)
    //     					// ->where('fokus_channels.deleted_at', null)
    //     					// ->where('fokus_areas.deleted_at', null)
    //     					// ->where('product_fokuses.deleted_at', null)
    //     					->whereDate('sales_mds.date', $this->periode)
    //     					->whereDate('product_fokuses.from', '<=', $this->periode)
    //     					->whereDate('product_fokuses.to', '>=', $this->periode)
    //     					->where('channels.name', '<>', 'GTC')
    //     					->count();

    //     return $data;

    // 	$subs = $this->sub_category_for_pf_list->pluck('id')->toArray();
    // 	return $subs;

    // 	$id_products = FokusProduct::whereHas('product.subcategory', function($query) use ($subs){
				//     		return $query->whereIn('id', $subs);
				//     	})
    // 					->whereHas('pf', function($query){
    // 						return $query->whereDate('from', '<=', $this->periode)
    // 									 ->whereDate('to', '>=', $this->periode);
    // 					})
    // 					->get();

    // 	// $id_area = EmployeePasar::where('id_employee', $this)

    // 	return $id_products;
    // }

    public function getValueTotalAttribute(){
    	return $this->value_pf + $this->value_non_pf;
    }

    public function getCbdAttribute(){
    	return Cbd::whereDate('date', $this->periode)->distinct('id_outlet')->count('id_outlet');
    }

    public function getOosAttribute(){
    	$id_product_oos = StockMdDetail::whereHas('stock', function ($query){
                                return $query->whereMonth('date', Carbon::parse($this->periode)->month)->whereYear('date', Carbon::parse($this->periode)->year);
                            })->pluck('id_product')->toArray();

    	$result = array();

    	foreach ($id_product_oos as $item) {

    		$oos = StockMdDetail::whereHas('stock', function ($query){
                                return $query->whereDate('date', $this->periode)->where('id_pasar', $this->outlet->employeePasar->pasar->id)->where('id_employee', $this->id_employee);
                            })    						
    						->where('id_product', $item)
    						->first()->oos;

    		$result[$item] = $oos;

    	}

    	return $result;
    }
    
}
