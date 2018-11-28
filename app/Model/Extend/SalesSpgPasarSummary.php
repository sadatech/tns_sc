<?php

namespace App\Model\Extend;

use Illuminate\Database\Eloquent\Model;
use App\SalesSpgPasar as SalesSpgPasar;
use Carbon\Carbon;
use DB;
use App\ProductFokusSpg;
use App\Product;

class SalesSpgPasarSummary extends SalesSpgPasar
{
    protected $appends = ['area', 'nama_spg', 'tanggal', 'nama_pasar', 'nama_stokies', 'jumlah_beli', 'detail'];

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
        return 'Under Construction';
    }

    public function getJumlahBeliAttribute(){
        return number_format(SalesSpgPasar::whereDate('date', Carbon::parse($this->date))
                            ->where('id_employee', $this->id_employee)
                            ->where('id_pasar', $this->id_pasar)
                            ->count() * 1);
    }  

    public function getDetailAttribute(){
    	$id_products = ProductFokusSpg::whereDate('from', '<=', Carbon::parse($this->date))
                                 ->whereDate('to', '>=', Carbon::parse($this->date))
                                 ->where('id_employee', $this->id_employee)
                                 ->pluck('id_product');

        $thead = "<table><thead>";
        $tbody = "<tbody><tr>";

        $products = Product::whereIn('id', $id_products)->get();

        $value_pf = 0;
        $value_other = 0;

        /* SALES FOCUS */

        foreach ($products as $product) {
            $thead .= "<th>Sales ".$product->name."</th>";
            $tbody .= "<td>";

            $data = SalesSpgPasar::join('sales_spg_pasar_details', 'sales_spg_pasars.id', 'sales_spg_pasar_details.id_sales')
                                ->join('prices', 'prices.id_product', 'sales_spg_pasar_details.id_product')
                                ->whereDate('sales_spg_pasars.date', Carbon::parse($this->date))
                                ->whereDate('prices.rilis', '<=', Carbon::parse($this->date))
                                ->where('sales_spg_pasars.id_employee', $this->id_employee)
                                ->where('sales_spg_pasars.id_pasar', $this->id_pasar)
                                ->where('sales_spg_pasar_details.id_product', $product->id)
                                ->select(DB::raw('sum(qty) as qty, sum(qty*price) as value'));

            $tbody .= (string)number_format($data->first()->qty * 1);
            $value_pf += $data->first()->value * 1;

            $tbody .= "</td>";
        }

        /* ============ */

        /* SALES OTHER */

        $thead .= "<th>Sales Other</th>";

        $tbody .= "<td>";

        $data2 = SalesSpgPasar::join('sales_spg_pasar_details', 'sales_spg_pasars.id', 'sales_spg_pasar_details.id_sales')
                            ->join('prices', 'prices.id_product', 'sales_spg_pasar_details.id_product')
                            ->whereDate('sales_spg_pasars.date', Carbon::parse($this->date))
                            ->whereDate('prices.rilis', '<=', Carbon::parse($this->date))
                            ->where('sales_spg_pasars.id_employee', $this->id_employee)
                            ->where('sales_spg_pasars.id_pasar', $this->id_pasar)
                            ->whereNotIn('sales_spg_pasar_details.id_product', $id_products)
                            ->select(DB::raw('sum(qty) as qty, sum(qty*price) as value'));

        $tbody .= (string)number_format($data2->first()->qty * 1);
        $value_other += $data2->first()->value * 1;

        $tbody .= "</td>";

        /* ============ */

        /* VALUE */

        $thead .= "<th>Sales PF Value</th>";

        $tbody .= "<td>".(string)number_format($value_pf)."</td>";

        $thead .= "<th>Sales Other Value</th>";

        $tbody .= "<td>".(string)number_format($value_other)."</td>";

        $thead .= "<th>Total Value</th>";

        $tbody .= "<td>".(string)number_format($value_pf+$value_other)."</td>";

        /* RESULT */

        $tresult = $thead."</thead>".$tbody."</tr></tbody></table>";

        return $tresult;
    } 
}
