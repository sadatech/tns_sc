<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Product;
use App\Store;
use App\Pasar;
use App\Employee;
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
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

class DashboardController extends Controller
{
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