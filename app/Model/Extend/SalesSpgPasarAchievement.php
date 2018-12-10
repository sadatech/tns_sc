<?php

namespace App\Model\Extend;

use Illuminate\Database\Eloquent\Model;
use App\SalesSpgPasar as SalesSpgPasar;
use Carbon\Carbon;
use DB;
use App\ProductFokusSpg;

class SalesSpgPasarAchievement extends SalesSpgPasar
{
    protected $appends = ['periode', 'nama_spg', 'area','hk', 'sum_of_jumlah_v', 'sum_of_jumlah', 'sum_of_pf_value_v','sum_of_pf_value', 'sum_of_total_value_v','sum_of_total_value', 'eff_kontak', 'act_value_v','act_value', 'sales_per_kontak'];

    public function getPeriodeAttribute(){
        return Carbon::parse($this->date)->format('F Y');
    }

    public function getAreaAttribute(){
        return $this->employee->getAreaByPasar();
    }

    public function getNamaSpgAttribute(){
        return $this->employee->name;
    }

       public function getHkAttribute(){
        return SalesSpgPasar::whereMonth('date', Carbon::parse($this->date)->month)
                            ->whereYear('date', Carbon::parse($this->date)->year)
                            ->groupBy('date')
                            ->get()
                            ->count() * 1;
    }

    public function getSumOfJumlahVAttribute(){
        return SalesSpgPasar::whereMonth('date', Carbon::parse($this->date)->month)
                            ->whereYear('date', Carbon::parse($this->date)->year)
                            ->where('id_pasar', $this->id_pasar)
                            ->count() * 1;
    }

    public function getSumOfJumlahAttribute(){
        return number_format($this->sum_of_jumlah_v);
    }

    public function getSumOfPfValueVAttribute(){
        $id_products = ProductFokusSpg::whereDate('from', '<=', Carbon::parse($this->date))
                                 ->whereDate('to', '>=', Carbon::parse($this->date))
                                 ->where('id_employee', $this->id_employee)
                                 ->pluck('id_product');

        $data = SalesSpgPasar::join('sales_spg_pasar_details', 'sales_spg_pasars.id', 'sales_spg_pasar_details.id_sales')
                                // ->join('prices', 'prices.id_product', 'sales_spg_pasar_details.id_product')
                                ->join('prices', function($join){
                                    return $join->on('prices.id_product', 'sales_spg_pasar_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_spg_pasar_details.id_product AND deleted_at is null LIMIT 1)"));
                                })
                                ->whereDate('sales_spg_pasars.date', Carbon::parse($this->date))
                                // ->whereDate('prices.rilis', '<=', Carbon::parse($this->date))
                                ->where('sales_spg_pasars.id_employee', $this->id_employee)
                                ->where('sales_spg_pasars.id_pasar', $this->id_pasar)
                                ->whereIn('sales_spg_pasar_details.id_product', $id_products)
                                ->select(DB::raw('sum(qty*price) as value'));

        return @($data->first()->value) * 1;
    }

    public function getSumOfPfValueAttribute(){
        return number_format($this->sum_of_pf_value_v);
    }

    public function getSumOfTotalValueVAttribute(){
        $data = SalesSpgPasar::join('sales_spg_pasar_details', 'sales_spg_pasars.id', 'sales_spg_pasar_details.id_sales')
                                // ->join('prices', 'prices.id_product', 'sales_spg_pasar_details.id_product')
                                ->join('prices', function($join){
                                    return $join->on('prices.id_product', 'sales_spg_pasar_details.id_product')->where('prices.rilis', DB::raw("(SELECT MAX(rilis) FROM prices WHERE id_product = sales_spg_pasar_details.id_product AND deleted_at is null LIMIT 1)"));
                                })
                                ->whereDate('sales_spg_pasars.date', Carbon::parse($this->date))
                                // ->whereDate('prices.rilis', '<=', Carbon::parse($this->date))
                                ->where('sales_spg_pasars.id_employee', $this->id_employee)
                                ->where('sales_spg_pasars.id_pasar', $this->id_pasar)
                                ->select(DB::raw('sum(qty*price) as value'));

        return @($data->first()->value) * 1;
    }

    public function getSumOfTotalValueAttribute(){
        return number_format($this->sum_of_total_value_v);
    }

    public function getEffKontakAttribute(){
        return ($this->hk > 0) ? ($this->sum_of_jumlah_v / $this->hk) : 0;
    }

    public function getActValueVAttribute(){
        return ($this->hk > 0) ? ($this->sum_of_total_value_v / $this->hk) : 0;
    }

    public function getActValueAttribute(){
        return number_format(($this->hk > 0) ? ($this->sum_of_total_value_v / $this->hk) : 0);
    }

    public function getSalesPerKontakAttribute(){
        return number_format(($this->eff_kontak > 0) ? ($this->act_value_v / $this->eff_kontak) : 0);
    }
}
