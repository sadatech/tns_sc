<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Price;

class DetailSales extends Model
{
    protected $fillable = [
        'id_sales', 'id_product', 'id_measure', 'qty', 'qty_actual', 'satuan'
    ];

    public function sales()
    {
        return $this->belongsTo('App\Sales', 'id_sales');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

    public function getSummary($param){
        switch ($param) {
            case 'periode':
                return Carbon::parse(@$this->sales->date)->format('F Y');
                break;

            case 'region':
                return @$this->sales->store->subarea->area->region->name;
                break;

            case 'is_jawa':
                return @$this->sales->store->is_jawa;
                break;

            case 'jabatan':
                return (@$this->sales->employee->position->level == 'mdmtc') ? 'MD' : @$this->sales->employee;
                break;

            case 'employee_name':
                return @$this->sales->employee->name;
                break;

            case 'area':
                return @$this->sales->store->subarea->area->name;
                break;

            case 'sub_area':
                return @$this->sales->store->subarea->name;
                break;

            case 'store_name':
                return @$this->sales->store->name1;
                break;

            case 'account':
                return @$this->sales->store->account->name;
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
                return ($this->sales->type == 'Sell Out') ? $this->qty_actual : 0;
                break;

            case 'actual_in_qty':
                return ($this->sales->type == 'Sell In') ? $this->qty_actual : 0;
                break;

            case 'price':
                return $this->getPrice();
                break;

            case 'actual_out_value':
                return '';
                break;

            case 'actual_in_value':
                return '';
                break;

            case 'total_actual':
                return '';
                break;

            case 'target_qty':
                return '';
                break;

            case 'target_value':
                return '';
                break;
        }
    }

    public function getPrice(){
        $date = Carbon::parse(Carbon::parse($this->sales->date)->format('Y-m-').Carbon::parse($this->sales->date)->daysInMonth.' 23:59:59');

        return Price::where('id_product', $this->id_product)
                ->whereDate('rilis', '<=', $date)->orderBy('rilis', 'DESC')
                ->first()
                ->price;
    }

    public function summary(){

        return [
            'periode' => Carbon::parse(@$this->sales->date)->format('F Y'),
            'region' => @$this->sales->store->subarea->area->region->name,
            'is_jawa' => @$this->sales->store->is_jawa,
            'jabatan' => '',
            'employee_name' => @$this->sales->employee->name,
            'area' => @$this->sales->store->subarea->area->name,
            'sub_area' => @$this->sales->store->subarea->name,
            'store_name' =>  @$this->sales->store->name1,
            'account' => @$this->sales->store->account->name,
            'category' => @$this->product->subcategory->category->name,
            'product_line' => @$this->product->subcategory->name,
            'product_name' => @$this->product->name,
            'actual_out_qty' => '',
            'actual_in_qty' => '',
            'price' => '',
            'actual_out_value' => '',
            'actual_in_value' => '',
            'total_actual' => '',
            'target_qty' => '',
            'target_value' => '',
        ];

    }

    public function toArray(){
        $array = parent::toArray();
        $array['product_name'] = $this->product->name;
        return $array;
    }

}
