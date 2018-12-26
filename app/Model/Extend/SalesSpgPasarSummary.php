<?php

namespace App\Model\Extend;

use Illuminate\Database\Eloquent\Model;
use App\SalesSpgPasar as SalesSpgPasar;
use Carbon\Carbon;
use DB;
use App\ProductFokusSpg;
use App\Product;
use Illuminate\Http\Request;
use App\SalesRecap;
use App\Price;

class SalesSpgPasarSummary extends SalesSpgPasar
{
    protected $appends = ['area', 'nama_spg', 'tanggal', 'nama_pasar', 'nama_stokies', 'jumlah_beli', 'product_focus_list', 'detail', 'sales_other', 'sales_other_value', 'sales_pf', 'total_value'];

    public function getAreaAttribute(){
        return @$this->pasar->subarea->area->name;
    }

    public function getNamaSpgAttribute(){
        return @$this->employee->name;
    }

    public function getTanggalAttribute(){
        return Carbon::parse($this->date)->format('D, F d, Y');
    }

    public function getNamaPasarAttribute(){
        return @$this->pasar->name;
    }

    public function getNamaStokiesAttribute(){
        return implode(", ", array_unique(SalesRecap::where('id_employee', $this->id_employee)->whereDate('date', Carbon::parse($this->date))->get()->pluck('outlet.name')->toArray()));
    }

    public function getJumlahBeliAttribute(){
        return number_format(SalesSpgPasar::whereDate('date', Carbon::parse($this->date))
                            ->where('id_employee', $this->id_employee)
                            ->where('id_pasar', $this->id_pasar)
                            ->count() * 1);
    }

    public function getProductFocusListAttribute(){
        $products = ProductFokusSpg::whereDate('from', '<=', Carbon::parse($this->date))
                             ->whereDate('to', '>=', Carbon::parse($this->date))
                             ->where('id_employee', $this->id_employee)
                             ->pluck('id_product');

        // $products = ProductFokusSpg::whereHas('product', function($query) use ($id_cat){
        //                 return $query->where('id_subcategory', $id_cat);
        //             })->where('id_employee', $this->id_employee)->whereDate('from', '<=', Carbon::parse($this->date))->whereDate('to', '>=', Carbon::parse($this->date))->get();

        return array_unique($products->toArray());
    }

    public function getDetailAttribute(){
        // return [1=>100,3=>200];
    	$result = array();
    	$products = ProductFokusSpg::whereDate('from', '<=', Carbon::parse($this->date))
                             ->whereDate('to', '>=', Carbon::parse($this->date))
                             ->where('id_employee', $this->id_employee)
                             // ->where('id_product', $id_product)
                             ->get();

        foreach ($products as $item) {

        	$data = SalesSpgPasar::join('sales_spg_pasar_details', 'sales_spg_pasars.id', 'sales_spg_pasar_details.id_sales')
                                ->leftJoin('prices', function($join){
                                    return $join->on('prices.id_product', 'sales_spg_pasar_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_spg_pasar_details.id_product)"));
                                })
                                ->whereDate('sales_spg_pasars.date', Carbon::parse($this->date))
                                ->where('sales_spg_pasars.id_employee', $this->id_employee)
                                ->where('sales_spg_pasars.id_pasar', $this->id_pasar)
                                ->where('sales_spg_pasar_details.id_product', $item->id_product)
                                ->select(DB::raw('sum(qty) as qty'));

            $result[$item->id_product] = $data->first()->qty;
        }

        return $result;

    }

    public function getSalesOtherAttribute(){
    	return SalesSpgPasar::join('sales_spg_pasar_details', 'sales_spg_pasars.id', 'sales_spg_pasar_details.id_sales')
                            // ->join('prices', 'prices.id_product', 'sales_spg_pasar_details.id_product')
                            ->leftJoin('prices', function($join){
                                return $join->on('prices.id_product', 'sales_spg_pasar_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_spg_pasar_details.id_product AND deleted_at is null LIMIT 1)"));
                            })
                            ->whereDate('sales_spg_pasars.date', Carbon::parse($this->date))
                            // ->whereDate('prices.rilis', '<=', Carbon::parse($this->date))
                            ->where('sales_spg_pasars.id_employee', $this->id_employee)
                            ->where('sales_spg_pasars.id_pasar', $this->id_pasar)
                            ->whereNotIn('sales_spg_pasar_details.id_product', $this->product_focus_list)
                            ->select(DB::raw('sum(qty) as qty'))
                            ->first()->qty;
    }

    public function getSalesOtherValueAttribute(){
    	return @(SalesSpgPasar::join('sales_spg_pasar_details', 'sales_spg_pasars.id', 'sales_spg_pasar_details.id_sales')
                            ->join('prices', function($join){
                                return $join->on('prices.id_product', 'sales_spg_pasar_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_spg_pasar_details.id_product AND deleted_at is null LIMIT 1)"));
                            })
                            ->whereDate('sales_spg_pasars.date', Carbon::parse($this->date))
                            // ->whereDate('prices.rilis', '<=', Carbon::parse($this->date))
                            ->where('sales_spg_pasars.id_employee', $this->id_employee)
                            ->where('sales_spg_pasars.id_pasar', $this->id_pasar)
                            ->whereNotIn('sales_spg_pasar_details.id_product', $this->product_focus_list)
                            ->select(DB::raw('sum(qty*price) as value'))
                            ->first()->value) * 1;
    }

    public function getSalesPfAttribute(){
    	return @(SalesSpgPasar::join('sales_spg_pasar_details', 'sales_spg_pasars.id', 'sales_spg_pasar_details.id_sales')
                            ->join('prices', function($join){
                                return $join->on('prices.id_product', 'sales_spg_pasar_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_spg_pasar_details.id_product AND deleted_at is null LIMIT 1)"));
                            })
                            ->whereDate('sales_spg_pasars.date', Carbon::parse($this->date))
                            ->where('sales_spg_pasars.id_employee', $this->id_employee)
                            ->where('sales_spg_pasars.id_pasar', $this->id_pasar)
                            ->whereIn('sales_spg_pasar_details.id_product', $this->product_focus_list)
                            ->select(DB::raw('sum(qty*price) as value'))
                            ->first()->value) * 1;
    }

    public function getTotalValueAttribute(){
    	return $this->sales_other_value + $this->sales_pf;
    }

    // public function getDetailAttribute(){
    // 	$id_products = ProductFokusSpg::whereDate('from', '<=', Carbon::parse($this->date))
    //                              ->whereDate('to', '>=', Carbon::parse($this->date))
    //                              ->where('id_employee', $this->id_employee)
    //                              ->pluck('id_product');

    //     $thead = "<table><thead>";
    //     $tbody = "<tbody><tr>";

    //     $products = Product::whereIn('id', $id_products)->get();

    //     $value_pf = 0;
    //     $value_other = 0;

    //     /* SALES FOCUS */

    //     foreach ($products as $product) {
    //         $thead .= "<th>Sales ".$product->name."</th>";
    //         $tbody .= "<td>";

    //         $data = SalesSpgPasar::join('sales_spg_pasar_details', 'sales_spg_pasars.id', 'sales_spg_pasar_details.id_sales')
    //                             ->join('prices', 'prices.id_product', 'sales_spg_pasar_details.id_product')
    //                             ->whereDate('sales_spg_pasars.date', Carbon::parse($this->date))
    //                             ->whereDate('prices.rilis', '<=', Carbon::parse($this->date))
    //                             ->where('sales_spg_pasars.id_employee', $this->id_employee)
    //                             ->where('sales_spg_pasars.id_pasar', $this->id_pasar)
    //                             ->where('sales_spg_pasar_details.id_product', $product->id)
    //                             ->select(DB::raw('sum(qty) as qty, sum(qty*price) as value'));

    //         $tbody .= (string)number_format($data->first()->qty * 1);
    //         $value_pf += $data->first()->value * 1;

    //         $tbody .= "</td>";
    //     }

    //     /* ============ */

    //     /* SALES OTHER */

    //     $thead .= "<th>Sales Other</th>";

    //     $tbody .= "<td>";

    //     $data2 = SalesSpgPasar::join('sales_spg_pasar_details', 'sales_spg_pasars.id', 'sales_spg_pasar_details.id_sales')
    //                         ->join('prices', 'prices.id_product', 'sales_spg_pasar_details.id_product')
    //                         ->whereDate('sales_spg_pasars.date', Carbon::parse($this->date))
    //                         ->whereDate('prices.rilis', '<=', Carbon::parse($this->date))
    //                         ->where('sales_spg_pasars.id_employee', $this->id_employee)
    //                         ->where('sales_spg_pasars.id_pasar', $this->id_pasar)
    //                         ->whereNotIn('sales_spg_pasar_details.id_product', $id_products)
    //                         ->select(DB::raw('sum(qty) as qty, sum(qty*price) as value'));

    //     $tbody .= (string)number_format($data2->first()->qty * 1);
    //     $value_other += $data2->first()->value * 1;

    //     $tbody .= "</td>";

    //     /* ============ */

    //     /* VALUE */

    //     $thead .= "<th>Sales PF Value</th>";

    //     $tbody .= "<td>".(string)number_format($value_pf)."</td>";

    //     $thead .= "<th>Sales Other Value</th>";

    //     $tbody .= "<td>".(string)number_format($value_other)."</td>";

    //     $thead .= "<th>Total Value</th>";

    //     $tbody .= "<td>".(string)number_format($value_pf+$value_other)."</td>";

    //     /* RESULT */

    //     $tresult = $thead."</thead>".$tbody."</tr></tbody></table>";

    //     return $tresult;
    // }
}
