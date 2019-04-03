<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use App\Product;
use App\Store;
use App\Pasar;
use App\Employee;
use App\TargetGtc;
use App\NewCbd;
use App\Attendance;
use App\AttendancePasar;
use App\AttendanceOutlet;
use App\StockMdHeader;
use App\StockMdDetail;
use App\SalesSpgPasar;
use App\SalesSpgPasarDetail;
use App\SalesDcDetail;
use App\PlanDc;
use App\SamplingDc;
use App\SamplingDcDetail;
use App\AttendanceBlock;
use App\SalesMotoricDetail;
use App\DistributionMotoricDetail;
use App\Model\Extend\TargetKpiMd;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

class DashboardController extends Controller
{
    public function dashboard() {
        $periode = Carbon::now();
        $vdoEmployees = Employee::where('isResign', 0)->whereHas('position', function($query){
            return $query->where('level', 'mdgtc');
        })->pluck('id');
        $target = TargetGtc::whereIn('id_employee', $vdoEmployees)
                        ->whereMonth('rilis', $periode->month)
                        ->whereYear('rilis', $periode->year)
                        ->orderBy('rilis', 'DESC')
                        ->distinct('id_employee')
                        ->get();
        $data['table']['hk_target'] = $target->sum('hk');
        $data['table']['cbd_target'] = $target->sum('cbd');
        $data['table']['cbd_actual'] = NewCbd::whereMonth('date', $periode->month)
                        ->whereYear('date', $periode->year)
                        ->whereIn('id_employee', $vdoEmployees)
                        ->distinct('id_outlet')
                        ->get()->count('id_outlet');
    
        $data['table']['hk_actual'] = Attendance::whereMonth('date', $periode->month)
                        ->whereYear('date', $periode->year)
                        ->whereIn('id_employee', $vdoEmployees)
                        ->groupBy(DB::raw('DATE(date)'),'id_employee')
                        ->get()->count('id');
        $data['table']['ach'] = ($data['table']['cbd_target'] == 0) ? '0 %' : (($data['table']['cbd_actual']/$data['table']['cbd_target']).'%');
        $data['product']  = Product::count();
        $data['store']    = Store::count();
        $data['pasar']    = Pasar::count();
        $data['employee'] = Employee::count();
        return response()->json([
            'success' => true,
            'count' => $data
        ]);
    }

    public function achSmd()
    {
        $periode = Carbon::now();
        $target_kpi = TargetKpiMd::where('isResign', 0)->whereHas('position', function($query){
            return $query->where('level', 'mdgtc');
        });
        return Datatables::of($target_kpi)
        ->addColumn('hk_target', function ($item) use ($periode){
            return is_null($item->getTarget($periode)) ? 0 : $item->getTarget($periode)['hk'];
        })
        ->addColumn('cbd_target', function ($item) use ($periode){
            return is_null($item->getTarget($periode)) ? 0 : $item->getTarget($periode)['cbd'];
        })
        ->addColumn('hk_actual', function ($item) use ($periode){
            return @$item->getHkActual($periode);
        })
        ->addColumn('sum_of_cbd', function ($item) use ($periode){
            return @$item->getCbd($periode);
        })
        ->addColumn('ach', function ($item) use ($periode){
            return is_null($item->getTarget($periode)) ? '0 %'  : ((@$item->getCbd($periode)/($item->getTarget($periode)['cbd'])).'%');
        })
        ->make(true);
    }

    public function achSmdArea()
    {
        $periode = Carbon::now();
        $target_kpi = TargetKpiMd::where('isResign', 0)->whereHas('position', function($query){
            return $query->where('level', 'mdgtc');
        });
        return Datatables::of($target_kpi)
        ->addColumn('hk_target', function ($item) use ($periode){
            return is_null($item->getTarget($periode)) ? 0 : $item->getTarget($periode)['hk'];
        })
        ->addColumn('cbd_target', function ($item) use ($periode){
            return is_null($item->getTarget($periode)) ? 0 : $item->getTarget($periode)['cbd'];
        })
        ->addColumn('hk_actual', function ($item) use ($periode){
            return @$item->getHkActual($periode);
        })
        ->addColumn('sum_of_cbd', function ($item) use ($periode){
            return @$item->getCbd($periode);
        })
        ->addColumn('ach', function ($item) use ($periode){
            return is_null($item->getTarget($periode)) ? '0 %'  : ((@$item->getCbd($periode)/($item->getTarget($periode)['cbd'])).'%');
        })
        ->make(true);
    }

    public function chartAchSmd()
    {
        $periode = Carbon::parse('January 2019');
        $SMDs = TargetKpiMd::where('isResign', 0)->whereHas('position', function($query){
            return $query->where('level', 'mdgtc');
        })->get();
        foreach ($SMDs as $smd) {
            $smd->sum_of_cbd = @$smd->getCbd($periode);
        }


        return response()->json($SMDs);
    }

    public function welcome() {
        return view('welcome');
    }

    public function gtc_smd()
    {
        foreach ($this->getDay(7) as $day) {
            $attendance['label'][] = Carbon::parse($day)->format('d M Y');
            $attendance['data'][] = (AttendanceOutlet::whereHas('attendance.employee.position', function($q){
                $q->where('level', 'mdgtc');
            })->whereHas('attendance', function($q) use($day){
                $date = Carbon::parse($day)->format('Y-m-d');
                $q->whereDate('date', $date);
            })->count() ?: 0);
        }
        $getWeek = Carbon::parse(Carbon::now()->endOfMonth())->weekOfMonth;
        for ($i=1; $i <= $getWeek; $i++) { 
            $stockist['label'][] = "Week ".$i;
            $stockist['data'][] = StockMdDetail::whereHas('stock', function($q) use ($i){
                $q->whereMonth('date', Carbon::now()->month);
                $q->where('week', $i);
            })->count();
        }
        $attendance['label'] = array_values(collect($attendance['label'])->sort()->toArray());
        return response()->json([
            'success' => true,
            'attendance' => $attendance,
            'stockist' => $stockist
        ]);
    }

    public function gtc_spg()
    {
        /*
        SPG Attendance
        */
        foreach ($this->getDay(7) as $day) {
            $attendance['label'][] = Carbon::parse($day)->format('d M Y');
            $attendance['data'][] = (AttendancePasar::whereHas('attendance.employee.position', function($q){
                $q->where('level', 'spggtc');
            })->whereHas('attendance', function($q) use($day){
                $date = Carbon::parse($day)->format('Y-m-d');
                $q->whereDate('date', $date);
            })->count() ?: 0);
        }
        $attendance['label'] = array_values(collect($attendance['label'])->sort()->toArray());
        /*
        SPG Sales
        */
        $getWeek = Carbon::parse(Carbon::now()->endOfMonth())->weekOfMonth;
        for ($i=1; $i <= $getWeek; $i++) {
            $sale['label'][] = "Week ".$i;
            $sale['data'][] = SalesSpgPasarDetail::whereHas('sales', function($q) use($i) {
                $q->whereMonth('date',Carbon::now()->month);
                $q->where('week',$i);
            })->count();
        }
        return response()->json([
            'success' => true,
            'attendance' => $attendance,
            'sales' => $sale
        ]);
    }

    public function gtc_dc()
    {
        /*
        DC Plan
        */
        foreach (array_reverse($this->getDay(Carbon::now()->endOfMonth()->day)) as $day) {
            $plan['label'][] = Carbon::parse($day)->format('d M Y');
            $plan['data'][] = PlanDc::whereDate('date',$day)->count();
        }
        /*
        DC Sales
        */
        $getWeek = Carbon::parse(Carbon::now()->endOfMonth())->weekOfMonth;
        for ($i=1; $i <= $getWeek; $i++) {
            $sale['label'][] = "Week ".$i;
            $sale['data'][] = SalesDcDetail::whereHas('sales', function($q) use($i) {
                $q->whereMonth('date',Carbon::now()->month);
                $q->where('week',$i);
            })->count();
        }
        /*
        DC Sampling
        */
        for ($i=1; $i <= $getWeek; $i++) {
            $sampling['label'][] = "Week ".$i;
            $sampling['data'][] = SamplingDcDetail::whereHas('sampling', function($q) use($i) {
                $q->whereMonth('date',Carbon::now()->month);
                $q->where('week',$i);
            })->count();
        }
        return response()->json([
            'success' => true,
            'plan' => $plan,
            'sales' => $sale,
            'sampling' => $sampling
        ]);
    }

    public function gtc_motorik()
    {
        /*
        Motoris Attendance
        */
        foreach (array_reverse($this->getDay(7)) as $day) {
            $attendance['label'][] = Carbon::parse($day)->format('d M Y');
            $attendance['data'][] = AttendanceBlock::whereHas('attendance.employee.position', function($q){
                $q->where('level', 'motoric');
            })->whereHas('attendance', function($q) use($day){
                $date = Carbon::parse($day)->format('Y-m-d');
                $q->whereDate('date', $date);
            })->count();
        }
        /*
        Motoric Distribution
        */
        foreach (array_reverse($this->getDay(7)) as $day) {
            $distribution['label'][] = Carbon::parse($day)->format('d M Y');
            $distribution['data'][] = DistributionMotoricDetail::whereHas('distribution', function($q) use($day){
                $date = Carbon::parse($day)->format('Y-m-d');
                $q->whereDate('date', $date);
            })->count();
        }
        /*
        Motoris Sales
        */
        $getWeek = Carbon::parse(Carbon::now()->endOfMonth())->weekOfMonth;
        for ($i=1; $i <= $getWeek; $i++) {
            $sale['label'][] = "Week ".$i;
            $sale['data'][] = SalesDcDetail::whereHas('sales', function($q) use($i) {
                $q->whereMonth('date',Carbon::now()->month);
                $q->where('week',$i);
            })->count();
        }
        return response()->json([
            'success' => true,
            'attendance' => $attendance,
            'sales' => $sale,
            'distribution' => $distribution
        ]);
    }

    public function mtc()
    {
        return view('dashboard.mtc');
    }

    public function getDay($day)
    {
        for ($i=0; $i <= $day-1; $i++) {
            $period[] = Carbon::now()->subDays($i);
        }
        return $period;
    }
}