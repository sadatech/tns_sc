<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use App\Attendance;
use App\AttendanceDetail;
use App\Employee;
use App\AttendanceOutlet;
use Excel;
use Auth;


class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('report.attendance');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data()
    {
        // $attendance = DB::table('attendance_outlets')
        // ->join('attendances', 'attendance_outlets.id_attendance', '=', 'attendances.id')
        // ->join('employees', 'attendances.id_employee', '=', 'employees.id')
        // ->join('outlets', 'attendance_outlets.id_outlet', '=', 'outlets.id')
        // ->select('attendance_outlets.*','checkin');
        $employee = Employee::all();
      
        return Datatables::of($employee)
        ->addColumn('employee', function($employee) {
            return $employee->name;
        })
        ->addColumn('nik', function($employee) {
            return $employee->nik;
        })
        ->addColumn('checkin', function($employee) {
            return $employee->checkin;
        })
        ->addColumn('checkout', function($employee) {
            return $employee->checkout;
        })
        ->addColumn('role', function($employee) {
            return $employee->position->name;
        })
        // ->addColumn('keterangan', function($attendanceOutlet) {
        //     return implode(',',$attendanceOutlet->attendance->keterangan->toArray());
        // })
        // ->addColumn('attendance_detail', function($attendanceOutlet) {
        //     return implode(',',$attendanceOutlet->attendance->keterangan->toArray());
        // })
        ->addColumn('outlet', function($employee) {
            return $employee->name;
        })
        ->addColumn('attendance',function($employee){
            return $attendance = Attendance::where('keterangan','masuk')->where('id_employee',$employee->id)->count();
        })

        ->addColumn('attendance_detail',function($employee){
            $array = [];
            $month = Carbon::parse()->format('m');
            $day = Carbon::now()->format('d');
            $year = Carbon::parse()->format('Y');
            $maxMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            
            $attendance = Attendance::where('id_employee', $employee->id)->get();
            for($i=1; $i <= $maxMonth; $i++){    
                // if($i <= $day){
                $attendance__ = Attendance::where('id_employee', $employee->id)->whereYear('date',$year)->whereMonth('date',$month)->whereDay('date',$i)->get();
                if ($attendance__->count() > 0) {
                    # code...
                    foreach ($attendance__ as $key) {
                        $attDate = date("d",strtotime($key->date));
                        // if($i==$attDate){
                            $attendanceCollection = collect();
                            foreach ($key->attendanceDetail as $attdDetail) {
                                $attdDetail->store = $attdDetail->store;
                                $attdDetail->place = $attdDetail->place;
                                $attdDetail->checkin = $attdDetail->checkin;
                                $attdDetail->checkout = $attdDetail->checkout;
                                $attendanceCollection->push($attdDetail);

                            } 
                            if ($key->keterangan == 'masuk') {
                                    $data = array(
                                    'name' => $employee->name,
                                    'attandaceDetail' => $attendanceCollection,
                                    'attendance' => $key
                                );
                             array_push($array, "<button class='btn btn-sm btn-success btn-square' style='width:80px;height:40px' onclick='detailModal(".json_encode($data).")'>$i</button>");
                             
                            }else if($key->keterangan == 'sakit'){
                                 array_push($array, "<button class='btn btn-sm btn-warning btn-square' style='width:80px;height:40px'>$i</button>");
                                 
                            }else if($key->keterangan == 'alpha'){
                                array_push($array, "<button class='btn btn-sm btn-danger btn-square' style='width:80px;height:40px'>$i</button>");
                                
                            }else if($key->keterangan == 'cuti'){
                                array_push($array, "<button class='btn btn-sm btn-primary btn-square' style='width:80px;height:40px'>$i</button>");
                                
                            }else{
                               array_push($array, "<button class='btn btn-sm btn-secondary btn-square' style='width:80px;height:40px'>$i</button>");
                               
                            }
                        // }else{
                        //     array_push($array, "<button class='btn btn-sm btn-danger btn-square' style='width:80px;height:40px'>$i</button>");
                           
                        // }
                    }
                }else{
                    array_push($array, "<button class='btn btn-sm btn-danger btn-square' style='width:80px;height:40px'>$i</button>");

                } 
            }
            return implode(' ', $array);
        })
        ->rawColumns(['attendance','attendance_detail'])
        ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
