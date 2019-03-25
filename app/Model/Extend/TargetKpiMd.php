<?php

namespace App\Model\Extend;

use Illuminate\Database\Eloquent\Model;
use App\Employee as Employee;
use App\EmployeePasar;
use App\TargetGtc;
use Carbon\Carbon;
use App\SalesMd;
use DB;
use App\AttendanceOutlet;
use App\Attendance;
use App\Cbd;
use App\Pf;

class TargetKpiMd extends Employee
{
    protected $appends = ['area'];	

    public function getAreaAttribute(){
    	return implode(", ",array_unique(EmployeePasar::where('id_employee', $this->id)->get()->pluck('pasar.subarea.name')->toArray()));
    }

    /** KPI **/

    public function getHkActual($periode){
    	return Attendance::whereMonth('date', Carbon::parse($periode)->month)
				        ->whereYear('date', Carbon::parse($periode)->year)
				        ->where('id_employee', $this->id)
                        ->groupBy(DB::raw('DATE(date)'))
        				->pluck('id');
    }

    public function getTotalValue($periode){
    	// return Carbon::parse($periode);

    	$data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->join('prices', function($join){
                                return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
                            })
        					->join('employees', 'employees.id', 'sales_mds.id_employee')
        					// ->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
        					// ->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
        					// ->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
        					// ->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
        					// ->join('areas', 'areas.id', 'sub_areas.id_area')
        					// ->join('product_fokus_gtcs', function ($join){
        					// 	return $join->on('product_fokus_gtcs.id_product', 'products.id')
        					// 				->on('product_fokus_gtcs.id_area', 'areas.id');
        					// })
        					->whereMonth('sales_mds.date', Carbon::parse($periode)->month)
        					->whereYear('sales_mds.date', Carbon::parse($periode)->year)
        					// ->whereDate('product_fokus_gtcs.from', '<=', Carbon::parse($periode))
        					// ->whereDate('product_fokus_gtcs.to', '>=', Carbon::parse($periode))
        					->where('employees.id', $this->id)
        					// ->whereNull('product_fokus_gtcs.deleted_at')
        					->whereNull('prices.deleted_at')
        					->select(DB::raw('(sum(sales_md_details.qty*price)) as value'));

        return $data->first()->value;
    }

    public function getAvgCbd($periode){
    	return ($this->getHkActual($periode) == 0 || $this->getHkActual($periode) == null) ? 0 : $this->getCbd($periode)/$this->getHkActual($periode);
    }

    public function getAvgCall($periode){
    	return ($this->getHkActual($periode) == 0 || $this->getHkActual($periode) == null) ? 0 : $this->getCall($periode)/$this->getHkActual($periode);
    }

    public function getAvgEc($periode){
    	return ($this->getHkActual($periode) == 0 || $this->getHkActual($periode) == null) ? 0 : $this->getEc($periode)/$this->getHkActual($periode);
    }

    public function getAvgSalesValue($periode){
    	return ($this->getHkActual($periode) == 0 || $this->getHkActual($periode) == null) ? 0 : $this->getSalesValue($periode)/$this->getHkActual($periode);
    }

    public function getAvgTotalValue($periode){
    	return ($this->getHkActual($periode) == 0 || $this->getHkActual($periode) == null) ? 0 : $this->getTotalValue($periode)/$this->getHkActual($periode);
    }

    public function getBestCbd($periode){
    	return ($this->getAvgCbd($periode) > 4) ? 1 : 0;
    }

    public function getBestCall($periode){
    	return ($this->getAvgCall($periode) > 4) ? 1 : 0;
    }

    public function getBestEc($periode){
    	return ($this->getAvgEc($periode) > 4) ? 1 : 0;
    }

    public function getBestSalesValue($periode){
    	return ($this->getAvgSalesValue($periode) > 4) ? 1 : 0;
    }

    public function getBestTotalValue($periode){
    	return ($this->getAvgTotalValue($periode) > 4) ? 1 : 0;
    }

    public function getTotalPoint($periode){
    	return $this->getBestCbd($periode)+$this->getBestCall($periode)+$this->getBestEc($periode)+$this->getBestSalesValue($periode)+$this->getBestTotalValue($periode);
    }

    public function getSumCat1($periode){
    	$pf = Pf::whereDate('from', '<=', Carbon::parse($periode))
    			->whereDate('to', '>=', Carbon::parse($periode))
    			->first()->id_category1;

    	$data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->join('employees', 'employees.id', 'sales_mds.id_employee')
        					->whereMonth('sales_mds.date', Carbon::parse($periode)->month)
        					->whereYear('sales_mds.date', Carbon::parse($periode)->year)
        					->where('employees.id', $this->id)
        					->where('products.id_subcategory', $pf)        					
        					->select(DB::raw('(sum(sales_md_details.qty)) as qty'));

    	return $data->first()->qty * 1;
    }

    public function getSumCat2($periode){
    	$pf = Pf::whereDate('from', '<=', Carbon::parse($periode))
    			->whereDate('to', '>=', Carbon::parse($periode))
    			->first()->id_category2;

    	$data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->join('employees', 'employees.id', 'sales_mds.id_employee')
        					->whereMonth('sales_mds.date', Carbon::parse($periode)->month)
        					->whereYear('sales_mds.date', Carbon::parse($periode)->year)
        					->where('employees.id', $this->id)
        					->where('products.id_subcategory', $pf)        					
        					->select(DB::raw('(sum(sales_md_details.qty)) as qty'));

    	return $data->first()->qty * 1;
    }

    public function getAvgCat1($periode){
    	return ($this->getHkActual($periode) == 0 || $this->getHkActual($periode) == null) ? 0 : $this->getSumCat1($periode)/$this->getHkActual($periode);
    }

    public function getAvgCat2($periode){
    	return ($this->getHkActual($periode) == 0 || $this->getHkActual($periode) == null) ? 0 : $this->getSumCat2($periode)/$this->getHkActual($periode);
    }

    public function getBestCat1($periode){
    	return ($this->getAvgCat1($periode) > 4) ? 1 : 0;
    }

    public function getBestCat2($periode){
    	return ($this->getAvgCat2($periode) > 4) ? 1 : 0;
    }

    /** TARGET KPI **/

    public function getTarget($periode){
    	return TargetGtc::where('id_employee', $this->id)
    						->whereMonth('rilis', Carbon::parse($periode)->month)
    						->whereYear('rilis', Carbon::parse($periode)->year)
    						->orderBy('rilis', 'DESC')
    						->first();

    	// foreach ($targets as $item) {
    	// 	$detail = array();
    	// 	$detail['hk'] = $item->hk;
    	// 	$detail['value_sales'] = $item->value_sales;
    	// 	$detail['ec'] = $item->ec;
    	// 	$detail['cbd'] = $item->cbd;
    	// 	$result[Carbon::parse($item->rilis)->format('Y-m')] = $detail;
    	// }

    	// return $targets;
    }

    public function getCall($periode){
    	return AttendanceOutlet::whereHas('attendance', function ($query) use ($periode){
				        			return $query->whereMonth('date', Carbon::parse($periode)->month)
				        						 ->whereYear('date', Carbon::parse($periode)->year)
				        						 ->where('id_employee', $this->id);
				        		})
        						->count();
    }

    public function getSalesValue($periode){
    	// return Carbon::parse($periode);

    	$data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->join('prices', function($join){
                                return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
                            })
        					->join('employees', 'employees.id', 'sales_mds.id_employee')
        					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
        					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
        					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
        					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
        					->join('areas', 'areas.id', 'sub_areas.id_area')
        					->join('product_fokus_gtcs', function ($join){
        						return $join->on('product_fokus_gtcs.id_product', 'products.id')
        									->on('product_fokus_gtcs.id_area', 'areas.id');
        					})
        					->whereMonth('sales_mds.date', Carbon::parse($periode)->month)
        					->whereYear('sales_mds.date', Carbon::parse($periode)->year)
        					->whereDate('product_fokus_gtcs.from', '<=', Carbon::parse($periode))
        					->whereDate('product_fokus_gtcs.to', '>=', Carbon::parse($periode))
        					->where('employees.id', $this->id)
        					->whereNull('product_fokus_gtcs.deleted_at')
        					->whereNull('prices.deleted_at')
        					->select(DB::raw('(sum(sales_md_details.qty*price)) as value'));

        $data2 = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->join('prices', function($join){
                                return $join->on('prices.id_product', 'sales_md_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_md_details.id_product)"));
                            })
        					->join('employees', 'employees.id', 'sales_mds.id_employee')
        					->join('product_fokus_gtcs', 'products.id', 'product_fokus_gtcs.id_product')
        					->whereMonth('sales_mds.date', Carbon::parse($periode)->month)
        					->whereYear('sales_mds.date', Carbon::parse($periode)->year)
        					->whereDate('product_fokus_gtcs.from', '<=', Carbon::parse($periode))
        					->whereDate('product_fokus_gtcs.to', '>=', Carbon::parse($periode))
        					->where('employees.id', $this->id)
        					->where('product_fokus_gtcs.id_area', null)
        					->whereNull('product_fokus_gtcs.deleted_at')
        					->whereNull('prices.deleted_at')
        					->select(DB::raw('(sum(sales_md_details.qty*price)) as value'));

        return $data->first()->value + $data2->first()->value;
    }

    public function getEc($periode){
    	// return Carbon::parse($periode);

    	$data = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->join('employees', 'employees.id', 'sales_mds.id_employee')
        					->join('outlets', 'outlets.id', 'sales_mds.id_outlet')
        					->join('employee_pasars', 'employee_pasars.id', 'outlets.id_employee_pasar')
        					->join('pasars', 'pasars.id', 'employee_pasars.id_pasar')
        					->join('sub_areas', 'sub_areas.id', 'pasars.id_subarea')
        					->join('areas', 'areas.id', 'sub_areas.id_area')
        					->join('product_fokus_gtcs', function ($join){
        						return $join->on('product_fokus_gtcs.id_product', 'products.id')
        									->on('product_fokus_gtcs.id_area', 'areas.id');
        					})
        					->whereMonth('sales_mds.date', Carbon::parse($periode)->month)
        					->whereYear('sales_mds.date', Carbon::parse($periode)->year)
        					->whereDate('product_fokus_gtcs.from', '<=', Carbon::parse($periode))
        					->whereDate('product_fokus_gtcs.to', '>=', Carbon::parse($periode))
        					->where('employees.id', $this->id)
        					->whereNull('product_fokus_gtcs.deleted_at')
        					->count();

        $data2 = SalesMd::join('sales_md_details', 'sales_mds.id', 'sales_md_details.id_sales')
        					->join('products', 'products.id', 'sales_md_details.id_product')
        					->join('employees', 'employees.id', 'sales_mds.id_employee')
        					->join('product_fokus_gtcs', 'products.id', 'product_fokus_gtcs.id_product')
        					->whereMonth('sales_mds.date', Carbon::parse($periode)->month)
        					->whereYear('sales_mds.date', Carbon::parse($periode)->year)
        					->whereDate('product_fokus_gtcs.from', '<=', Carbon::parse($periode))
        					->whereDate('product_fokus_gtcs.to', '>=', Carbon::parse($periode))
        					->where('employees.id', $this->id)
        					->where('product_fokus_gtcs.id_area', null)
        					->whereNull('product_fokus_gtcs.deleted_at')
        					->count();

        return $data + $data2;
    }

    public function getCbd($periode){
    	return Cbd::whereMonth('date', Carbon::parse($periode)->month)->whereYear('date', Carbon::parse($periode)->year)->distinct('id_outlet')->count('id_outlet');
    }

    /**  **/

}
