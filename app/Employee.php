<?php

namespace App;

use App\Components\traits\DropDownHelper;
use App\Filters\QueryFilters;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use DB;
use Carbon\Carbon;
use App\Pasar;

class Employee extends Model implements AuthenticatableContract, JWTSubject
{
    use Authenticatable;
    use DropDownHelper;
    
    protected $fillable = [
        'name', 'nik', 'id_position', 'ktp', 'phone', 'email', 'rekening', 'bank', 'status', 'joinAt', 'id_agency', 'gender', 'education', 'birthdate', 'foto_ktp', 'foto_tabungan', 'isResign', 'password', 'id_timezone'
    ];

    protected $hidden = [
        'password'
    ];

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
    
    public function resigns()
    {
        return $this->hasMany('App\Resign', 'id_employee');
    }

    public function planEmployee()
    {
        return $this->hasMany('App\PlanEmployee', 'id_employee');
    }

    public function attendanceDetail()
    {
        return $this->hasMany('App\AttendanceDetail', 'id_employee');
    }

    public function attendanceOutlet()
    {
        return $this->hasMany('App\AttendanceOutlet', 'id_employee');
    }

    public function attendance()
    {
        return $this->hasMany('App\Attendance', 'id_employee');
    }

    public function rejoins()
    {
        return $this->hasMany('App\Rejoin', 'id_employee');
    }

    public function sellin()
    {
        return $this->hasMany('App\SellIn', 'id_employee');
    }

    public function headeriin()
    {
        return $this->hasMany('App\HeaderIn', 'id_employee');
    }

    public function employeeStore()
    {
        return $this->hasMany('App\EmployeeStore', 'id_employee');
    }

    public function employeePasar()
    {
        return $this->hasMany('App\EmployeePasar', 'id_employee');
    }

    public function employeeSubArea()
    {
        return $this->hasMany('App\EmployeeSubArea', 'id_employee');
    }

    public function position()
    {
        return $this->belongsTo('App\Position', 'id_position');
    }

    public function agency()
    {
        return $this->belongsTo('App\Agency', 'id_agency');
    }

    public function timezone()
    {
        return $this->belongsTo('App\Timezone', 'id_timezone');
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

    public function getActualPf1($data){
        $pf = $this->pfQuery($data['date']->month, $data['date']->year, '1');
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
        $target = $this->getTarget1($data);
        return ($target > 0) ? round(($this->getActualPf1($data)/$target)*100, 2).'%' : '0%';
    }

    public function getActualPf2($data){
        $pf = $this->pfQuery($data['date']->month, $data['date']->year, '2');
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
        $target = $this->getTarget2($data);
        return ($target > 0) ? round(($this->getActualPf2($data)/$target)*100, 2).'%' : '0%';
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }

    public function getAreaByPasar(){
        return implode(', ', array_unique(Pasar::with('subarea.area')->whereIn('id', $this->employeePasar->pluck('id_pasar')->toArray())->get()->pluck('subarea.area.name')->toArray()));
    }

    public function toArray(){
        $array = parent::toArray();
        $array['foto_profil_url'] = !empty($this->foto_profil) ? asset($this->foto_profil) : '';
        return $array;
    }
}

