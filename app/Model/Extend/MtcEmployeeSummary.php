<?php

namespace App\Model\Extend;

use Illuminate\Database\Eloquent\Model;
use App\Employee as Employee;
use DB;
use Carbon\Carbon;

class MtcEmployeeSummary extends Employee
{
    public function pfQuery($month, $year, $focus = '')
    {
        $pf = "* IF(
                (SELECT 
                    IF((select count(*) from fokus_areas WHERE product_fokuses.id = fokus_areas.id_pf) = 0, 1, 
                       IF((select count(*) from fokus_areas WHERE product_fokuses.id = fokus_areas.id_pf AND fokus_areas.id_area = sales_mtc_summary.id_area) > 0,1,0)
                    ) as area
                    FROM `product_fokuses`
                    INNER JOIN fokus_products ON product_fokuses.id = fokus_products.id_pf
                    LEFT JOIN fokus_channels on product_fokuses.id = fokus_channels.id_pf
                    WHERE 
                        fokus_products.id_product = sales_mtc_summary.id_product
                        AND fokus_channels.id_channel = sales_mtc_summary.id_channel
                        AND sales_mtc_summary.date BETWEEN product_fokuses.from AND product_fokuses.to
                 ) > 0,
                1, 0)";

        if ($focus != '') {
            $pf = "* IF(
                    (SELECT 
                        id_category$focus
                        FROM `pfs`
                        WHERE 
                            MONTH(date) = ".$month."
                            AND YEAR(date) = ".$year."
                        ORDER BY pfs.id desc
                        LIMIT 1
                     ) = sales_mtc_summary.id_category,
                    1, 0)".$pf;
        }

        return $pf;
    }

    public function pfQueryAlt($periode, $focus = '')
    {
        $newDate = Carbon::parse($periode)->format('Y-m-d');
        $pf = "* IF (
                        (
                            SELECT COUNT(*) FROM product_fokus_mtcs 
                            WHERE product_fokus_mtcs.id_product = sales_mtc_summary.id_product
                            AND product_fokus_mtcs.id_channel = sales_mtc_summary.id_channel
                            AND 
                            (
                                product_fokus_mtcs.id_area = sales_mtc_summary.id_area
                                OR
                                product_fokus_mtcs.id_area is null
                            )
                            AND product_fokus_mtcs.from <= '".$newDate."'
                            AND product_fokus_mtcs.to >= '".$newDate."'                            
                        ) 
                > 0, 1, 0)
              ";

        if ($focus != '') {
            $pf = "* IF(
                    (SELECT 
                        id_category$focus
                        FROM `pfs`
                        WHERE 
                            MONTH(date) = ".Carbon::parse($periode)->month."
                            AND YEAR(date) = ".Carbon::parse($periode)->year."
                        ORDER BY pfs.id desc
                        LIMIT 1
                     ) = sales_mtc_summary.id_subcategory,
                    1, 0)".$pf;
        }

        return $pf;
    }

    /* Achievement MTC */

    public function getTarget($data){
        switch ($this->position->level) {
            case 'spgmtc':
                return 
                    DB::select(
                        "
                        SELECT SUM(target_value) AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND id_store = ".$data['store']."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;    

            case 'mdmtc':
                return 
                    DB::select(
                        "
                        SELECT SUM(target_value) AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;  

            case 'tlmtc':
                return 
                    DB::select(
                        "
                        SELECT SUM(target_value) AS result
                        FROM sales_mtc_summary
                        WHERE sub_area = '".$data['sub_area']."'
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;       
        }
        
    }

    public function getTarget1($data){
        $pf = $this->pfQuery($data['date']->month, $data['date']->year, '1');
        // return $pf;
        switch ($this->position->level) {
            case 'spgmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(target_value $pf) AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND id_store = ".$data['store']."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;    

            case 'mdmtc':
                return 
                    DB::select(
                        "
                        SELECT SUM(target_value) AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;  

            case 'tlmtc':
                return 
                    DB::select(
                        "
                        SELECT SUM(target_value $pf) AS result
                        FROM sales_mtc_summary
                        WHERE sub_area = '".$data['sub_area']."'
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;       
        }
        
    }

    public function getTarget2($data){
        $pf = $this->pfQuery($data['date']->month, $data['date']->year, '2');
        switch ($this->position->level) {
            case 'spgmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(target_value $pf) AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND id_store = ".$data['store']."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;    

            case 'mdmtc':
                return 
                    DB::select(
                        "
                        SELECT SUM(target_value) AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;  

            case 'tlmtc':
                return 
                    DB::select(
                        "
                        SELECT SUM(target_value) AS result
                        FROM sales_mtc_summary
                        WHERE sub_area = '".$data['sub_area']."'
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;       
        }
        
    }

    public function getTarget1Alt($data){
        $pf = $this->pfQueryAlt($data['date'], '1');
        // return $pf;
        switch ($this->position->level) {
            case 'spgmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(target_value $pf) AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND id_store = ".$data['store']."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;    

            case 'mdmtc':
                return 
                    DB::select(
                        "
                        SELECT SUM(target_value $pf) AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;  

            case 'tlmtc':
                return 
                    DB::select(
                        "
                        SELECT SUM(target_value $pf) AS result
                        FROM sales_mtc_summary
                        WHERE sub_area = '".$data['sub_area']."'
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;       
        }
    }

    public function getTarget2Alt($data){
        $pf = $this->pfQueryAlt($data['date'], '2');
        // return $pf;
        switch ($this->position->level) {
            case 'spgmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(target_value $pf) AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND id_store = ".$data['store']."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;    

            case 'mdmtc':
                return 
                    DB::select(
                        "
                        SELECT SUM(target_value $pf) AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;  

            case 'tlmtc':
                return 
                    DB::select(
                        "
                        SELECT SUM(target_value $pf) AS result
                        FROM sales_mtc_summary
                        WHERE sub_area = '".$data['sub_area']."'
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;       
        }
    }

    public function getActual($data){
        switch ($this->position->level) {
            case 'spgmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0))
                        AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND id_store = ".$data['store']."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;

            case 'tlmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0))
                        AS result
                        FROM sales_mtc_summary
                        WHERE
                        sub_area = '".$data['sub_area']."'
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;            

            case 'mdmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0))
                        AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;
        }
        
    }

    public function getActualPrevious($data){
        switch ($this->position->level) {
            case 'spgmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0))
                        AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND id_store = ".$data['store']."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".Carbon::parse($data['date'])->subYear()->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;

            case 'tlmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0))
                        AS result
                        FROM sales_mtc_summary
                        WHERE 
                        sub_area = '".$data['sub_area']."'
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".Carbon::parse($data['date'])->subYear()->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;

            case 'mdmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0))
                        AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".Carbon::parse($data['date'])->subYear()->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;
        }        
    }

    public function getAchievement($data){
        $target = $this->getTarget($data);
        return ($target > 0) ? round(($this->getActual($data)/$target)*100, 2).'%' : '0%';
    }

    public function getGrowth($data){
        $previous = $this->getActualPrevious($data);
        return ($previous > 0) ? round((($this->getActual($data)/$previous)-1)*100, 2).'%' : '0%';
    }

    public function getActualPf($data){
        $pf = $this->pfQuery($data['date']->month, $data['date']->year);
        switch ($this->position->level) {
            case 'spgmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0) $pf)
                        AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND id_store = ".$data['store']."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;

            case 'tlmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0) $pf)
                        AS result
                        FROM sales_mtc_summary
                        WHERE
                        sub_area = '".$data['sub_area']."'
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;            

            case 'mdmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0) $pf)
                        AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;
        }
        
    }

    public function getActualPf1($data){
        // $pf = $this->pfQuery($data['date']->month, $data['date']->year, '1');
        $pf = $this->pfQueryAlt($data['date'], '1');
        switch ($this->position->level) {
            case 'spgmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0) $pf)
                        AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND id_store = ".$data['store']."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;

            case 'tlmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0) $pf)
                        AS result
                        FROM sales_mtc_summary
                        WHERE
                        sub_area = '".$data['sub_area']."'
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;            

            case 'mdmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0) $pf)
                        AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;
        }
        // switch ($this->position->level) {
        //     case 'spgmtc':
        //         $category = Pf::whereDate('from', '<=', $data['date']->format('Y-m-d'))
        //                     ->whereDate('to', '>=', $data['date']->format('Y-m-d'))
        //                     ->first()->id_category1;

        //         $product_ids = ProductFokus::whereHas('Fokus', function ($query) use ($data){
        //                             return $query->where('id_channel', $data['id_channel']);
        //                         })->whereHas('fokusproduct.product.subcategory', function ($query) use ($category){
        //                             return $query->where('id_category', $category);
        //                         });
        //                         // })->where(function ($query) use ($data){
        //                         //     return $query->whereHas('fokusarea', function ($query2) use ($data){
        //                         //         return $query2->where('id_area', $data['id_area']);
        //                         //     })
        //                         // });

        //         return $product_ids->get();   
        //         break;         
        //         return 
        //             DB::select(
        //                 "
        //                 SELECT 
        //                     SUM(total_actual * IF(target_value > 0, 1, 0))
        //                 AS result
        //                 FROM sales_mtc_summary
        //                 WHERE id_employee = ".$this->id."
        //                 AND id_store = ".$data['store']."
        //                 AND MONTH(date) = ".$data['date']->month."
        //                 AND YEAR(date) = ".$data['date']->year."
        //                 LIMIT 1
        //                 "
        //             )[0]->result * 1;
        //     break;
        // }
        
    }

    public function getAchievementPf1($data){
        $target = $this->getTarget1Alt($data);
        return ($target > 0) ? round(($this->getActualPf1($data)/$target)*100, 2).'%' : '0%';
    }

    public function getActualPf2($data){
        // $pf = $this->pfQuery($data['date']->month, $data['date']->year, '2');
        $pf = $this->pfQueryAlt($data['date'], '2');
        switch ($this->position->level) {
            case 'spgmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0) $pf)
                        AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND id_store = ".$data['store']."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;

            case 'tlmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0) $pf)
                        AS result
                        FROM sales_mtc_summary
                        WHERE
                        sub_area = '".$data['sub_area']."'
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;            

            case 'mdmtc':
                return 
                    DB::select(
                        "
                        SELECT 
                            SUM(total_actual * IF(target_value > 0, 1, 0) $pf)
                        AS result
                        FROM sales_mtc_summary
                        WHERE id_employee = ".$this->id."
                        AND MONTH(date) = ".$data['date']->month."
                        AND YEAR(date) = ".$data['date']->year."
                        LIMIT 1
                        "
                    )[0]->result * 1;
            break;
        }
        // switch ($this->position->level) {
        //     case 'spgmtc':
        //         $category = Pf::whereDate('from', '<=', $data['date']->format('Y-m-d'))
        //                     ->whereDate('to', '>=', $data['date']->format('Y-m-d'))
        //                     ->first()->id_category1;

        //         $product_ids = ProductFokus::whereHas('Fokus', function ($query) use ($data){
        //                             return $query->where('id_channel', $data['id_channel']);
        //                         })->whereHas('fokusproduct.product.subcategory', function ($query) use ($category){
        //                             return $query->where('id_category', $category);
        //                         });
        //                         // })->where(function ($query) use ($data){
        //                         //     return $query->whereHas('fokusarea', function ($query2) use ($data){
        //                         //         return $query2->where('id_area', $data['id_area']);
        //                         //     })
        //                         // });

        //         return $product_ids->get();   
        //         break;         
        //         return 
        //             DB::select(
        //                 "
        //                 SELECT 
        //                     SUM(total_actual * IF(target_value > 0, 1, 0))
        //                 AS result
        //                 FROM sales_mtc_summary
        //                 WHERE id_employee = ".$this->id."
        //                 AND id_store = ".$data['store']."
        //                 AND MONTH(date) = ".$data['date']->month."
        //                 AND YEAR(date) = ".$data['date']->year."
        //                 LIMIT 1
        //                 "
        //             )[0]->result * 1;
        //     break;
        // }
        
    }

    public function getAchievementPf2($data){
        $target = $this->getTarget2Alt($data);
        return ($target > 0) ? round(($this->getActualPf2($data)/$target)*100, 2).'%' : '0%';
    }
}
