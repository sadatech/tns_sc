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
            $attendance['label'][] = Carbon::parse($day)->format('D, d M Y');
            $attendance['data'][] = (AttendanceOutlet::whereHas('attendance.employee.position', function($q){
                $q->where('level', 'mdgtc');
            })->whereHas('attendance', function($q) use($day){
                $date = Carbon::parse($day)->format('Y-m-d');
                $q->whereDate('date', $date);
            })->first() ?: 0);
        }
        $getWeek = Carbon::parse(Carbon::now()->endOfMonth())->weekOfMonth;
        for ($i=1; $i <= $getWeek; $i++) { 
            $stockist['label'][] = "Week ".$i;
            $stockist['data'][] = StockMdDetail::whereHas('stock', function($q) use ($i){
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
        foreach ($this->getDay(7) as $day) {
            $attendance['label'][] = Carbon::parse($day)->format('D, d M Y');
            $attendance['data'][] = (AttendancePasar::whereHas('attendance.employee.position', function($q){
                $q->where('level', 'spggtc');
            })->whereHas('attendance', function($q) use($day){
                $date = Carbon::parse($day)->format('Y-m-d');
                $q->whereDate('date', $date);
            })->first() ?: 0);
        }
        $getWeek = Carbon::parse(Carbon::now()->endOfMonth())->weekOfMonth;
        for ($i=1; $i <= $getWeek; $i++) { 
            $stockist['label'][] = "Week ".$i;
            $stockist['data'][] = StockMdDetail::whereHas('stock', function($q) use ($i){
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

    public function gtc_dc()
    {
        foreach ($this->getDay(7) as $day) {
            $attendance['label'][] = Carbon::parse($day)->format('D, d M Y');
            $attendance['data'][] = (AttendanceOutlet::whereHas('attendance.employee.position', function($q){
                $q->where('level', 'mdgtc');
            })->whereHas('attendance', function($q) use($day){
                $date = Carbon::parse($day)->format('Y-m-d');
                $q->whereDate('date', $date);
            })->first() ?: 0);
        }
        $getWeek = Carbon::parse(Carbon::now()->endOfMonth())->weekOfMonth;
        for ($i=1; $i <= $getWeek; $i++) { 
            $stockist['label'][] = "Week ".$i;
            $stockist['data'][] = StockMdDetail::whereHas('stock', function($q) use ($i){
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

    public function gtc_motorik()
    {
        foreach ($this->getDay(7) as $day) {
            $attendance['label'][] = Carbon::parse($day)->format('D, d M Y');
            $attendance['data'][] = (AttendanceOutlet::whereHas('attendance.employee.position', function($q){
                $q->where('level', 'mdgtc');
            })->whereHas('attendance', function($q) use($day){
                $date = Carbon::parse($day)->format('Y-m-d');
                $q->whereDate('date', $date);
            })->first() ?: 0);
        }
        $getWeek = Carbon::parse(Carbon::now()->endOfMonth())->weekOfMonth;
        for ($i=1; $i <= $getWeek; $i++) { 
            $stockist['label'][] = "Week ".$i;
            $stockist['data'][] = StockMdDetail::whereHas('stock', function($q) use ($i){
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