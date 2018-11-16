<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Filters\QueryFilters;
use DB;

class MtcReportTemplate extends Model
{
    protected $fillable = [
        'id_employee','id_store','date','id_product'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function product()
    {
    	return $this->belongsTo('App\Product', 'id_product');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'id_store');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }

    public function generateColumns(){
        return ['periode', 'region', 'is_jawa', 'jabatan', 'employee_name', 'area', 'sub_area', 'store_name', 'account', 'category', 'product_line', 'product_name', 'actual_out_qty', 'actual_in_qty', 'price', 'actual_out_value', 'actual_in_value', 'total_actual', 'target_qty', 'target_value'];
    }

    public function getActualQty($param){
        $str = 
        "
            SELECT SUM(detail_sales.qty) as result
            FROM detail_sales
            JOIN sales ON sales.id = detail_sales.id_sales
            WHERE sales.id_store = ".$this->id_store."
            AND sales.id_employee = ".$this->id_employee."
            AND MONTH(sales.date) = MONTH('".$this->date."')
            AND YEAR(sales.date) = YEAR('".$this->date."')
            AND detail_sales.id_product = ".$this->id_product."
            AND sales.type = '".$param."'
        ";

        return DB::select($str)[0]->result * 1;
    }

    public function getPrice(){
        $str = 
        "
             SELECT prices.price FROM prices 
             WHERE prices.id_product = ".$this->id_product."
             AND DATE(prices.rilis) <= DATE('".$this->date."')
             AND prices.deleted_at IS NULL
             ORDER BY prices.rilis DESC
             LIMIT 1
        ";

        return DB::select($str)[0]->price * 1;
    }

    public function getActualValue($param){
        $str = 
        "
            SELECT SUM(
                IF(sales.`type` = '".$param."', detail_sales.qty, 0) *
                IFNULL(
                    (
                     SELECT `prices`.price FROM `prices` 
                     WHERE `prices`.id_product = `detail_sales`.id_product
                     AND `prices`.`rilis` <= `sales`.`date` 
                     AND `prices`.`deleted_at` IS NULL
                     ORDER BY `prices`.`rilis` DESC
                     LIMIT 1
                    )
                , 0)
            ) AS result
            FROM detail_sales
            JOIN sales ON sales.id = detail_sales.id_sales
            WHERE sales.id_store = ".$this->id_store."
            AND sales.id_employee = ".$this->id_employee."
            AND MONTH(sales.date) = MONTH('".$this->date."')
            AND YEAR(sales.date) = YEAR('".$this->date."')
            AND detail_sales.id_product = ".$this->id_product."
            AND sales.type = '".$param."'
        ";

        return DB::select($str)[0]->result * 1;
    }

    public function getTarget(){
        $str = 
        "
             SELECT targets.quantity FROM targets 
             WHERE targets.id_product = ".$this->id_product."
             AND targets.id_store = ".$this->id_store."
             AND targets.id_employee = ".$this->id_employee."
             AND MONTH(targets.rilis) = MONTH('".$this->date."')
             AND YEAR(targets.rilis) = YEAR('".$this->date."')
             AND targets.deleted_at IS NULL
             LIMIT 1
        ";

        return DB::select($str)[0]->quantity * 1;
    }

    public function getSummary($param){
        switch ($param) {
            case 'periode':
                return Carbon::parse(@$this->date)->format('F Y');
                break;

            case 'region':
                return @$this->store->subarea->area->region->name;
                break;

            case 'is_jawa':
                return @$this->store->is_jawa;
                break;

            case 'jabatan':
                return (@$this->employee->position->level == 'mdmtc') ? 'MD' : @$this->employee;
                break;

            case 'employee_name':
                return @$this->employee->name;
                break;

            case 'area':
                return @$this->store->subarea->area->name;
                break;

            case 'sub_area':
                return @$this->store->subarea->name;
                break;

            case 'store_name':
                return @$this->store->name1;
                break;

            case 'account':
                return @$this->store->account->name;
                break;

            case 'category':
                return @$this->product->subcategory->category->name;
                break;

            case 'product_line':
                return @$this->product->subcategory->name;
                break;

            case 'product_name':
                return @$this->product->name;
                break;

            case 'actual_out_qty':
                return number_format(@$this->getActualQty('Sell Out'));
                break;

            case 'actual_in_qty':
                return number_format(@$this->getActualQty('Sell In'));
                break;

            case 'price':
                return number_format(@$this->getPrice());
                break;

            case 'actual_out_value':
                return number_format(@$this->getActualValue('Sell Out'));
                break;

            case 'actual_in_value':
                return number_format(@$this->getActualValue('Sell In'));
                break;

            case 'total_actual':
                return number_format((@$this->getActualValue('Sell In') == 0) ? @$this->getActualValue('Sell Out') : @$this->getActualValue('Sell In'));
                break;

            case 'target_qty':
                return number_format(@$this->getTarget());
                break;

            case 'target_value':
                return number_format(@$this->getTarget() * @$this->getPrice());
                break;
        }
    }
}
