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
    public function index(){
        $periode = Carbon::now();
        // $periode = Carbon::parse('January 2019');
        $vdoEmployees = Employee::where('isResign', 0)->whereHas('position', function($query){
            return $query->where('level', 'mdgtc');
        })->pluck('id');
        $target = TargetGtc::whereIn('id_employee', $vdoEmployees)
                        ->whereMonth('rilis', $periode->month)
                        ->whereYear('rilis', $periode->year)
                        ->orderBy('rilis', 'DESC')
                        ->groupBy('id_employee')
                        ->get();
        $data['hk_target'] = $target->sum('hk');
        $data['cbd_target'] = $target->sum('cbd');
        $data['cbd_actual'] = NewCbd::whereMonth('date', $periode->month)
                        ->whereYear('date', $periode->year)
                        ->whereIn('id_employee', $vdoEmployees)
                        ->groupBy('id_outlet','id_employee')
                        ->get()->count('id_outlet');
    
        $data['hk_actual'] = Attendance::whereMonth('date', $periode->month)
                        ->whereYear('date', $periode->year)
                        ->whereIn('id_employee', $vdoEmployees)
                        ->groupBy(DB::raw('DATE(date)'),'id_employee')
                        ->get()->count('id');
        $data['ach'] = ($data['cbd_target'] == 0) ? '0 %' : (round(($data['cbd_actual']/$data['cbd_target']*100),2).'%');

        return view('dashboard.home', $data);
    }

    public function dashboard() {
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
        // $periode = Carbon::parse('January 2019');
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
            return is_null($item->getTarget($periode)) ? '0 %'  : (round((@$item->getCbd($periode)/($item->getTarget($periode)['cbd'])*100),2).'%');
        })
        ->make(true);
    }

    public function achSmdArea()
    {
        $periode = Carbon::now();
        // $periode = Carbon::parse('January 2019');
        $target_kpi = TargetKpiMd::where('employees.isResign', 0)
        ->join('positions','employees.id_position','positions.id')
        ->where('positions.level', 'mdgtc')
            ->leftjoin('new_cbds','employees.id','new_cbds.id_employee')
            ->where('new_cbds.deleted_at', null)
            ->whereMonth('date', $periode->month)
            ->whereYear('date', $periode->year)
            ->select('employees.id as id', 'employees.name as name', 'employees.email as email', DB::raw("count(distinct(new_cbds.id_outlet)) as cbd"))
            ->groupBy('employees.id')
            ->orderBy('cbd','desc')
            ->limit(10);

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
            return is_null($item->getTarget($periode)) ? '0 %'  : (round((@$item->getCbd($periode)/($item->getTarget($periode)['cbd'])*100),2).'%');
        })
        ->make(true);
    }

    public function chartAchSmd()
    {
        $periode = Carbon::now();
        // $periode = Carbon::parse('January 2019');
        $SMDs = Employee::where('employees.isResign', 0)
        ->join('positions','employees.id_position','positions.id')
        ->where('positions.level', 'mdgtc')
            ->leftjoin('new_cbds','employees.id','new_cbds.id_employee')
            ->where('new_cbds.deleted_at', null)
            ->whereMonth('date', $periode->month)
            ->whereYear('date', $periode->year)
            ->select('employees.id as id', 'employees.name as name', 'employees.email as email', DB::raw("count(distinct(new_cbds.id_outlet)) as sum_of_cbd"))
            ->groupBy('employees.id')
            ->orderBy('sum_of_cbd','desc')
            ->limit(10)->get();

        foreach ($SMDs as $key => $smd) {
            $smd['nama_potong'] = substr($smd->name, 0, 18);
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