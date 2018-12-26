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
use Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use Box\Spout\Writer\Style\Color;
use File;
use Excel;
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
        $employees = DB::table('employees')
        ->leftjoin('attendances','employees.id','=','attendances.id_employee')
        ->leftjoin('attendance_outlets','attendances.id','=','attendance_outlets.id_attendance')
        ->leftjoin('positions', 'employees.id_position', '=', 'positions.id')
        ->where('positions.level', 'spgmtc')->orWhere('positions.level','mdmtc')
        ->select('employees.*','attendance_outlets.checkin','attendance_outlets.checkout','positions.level');
        $employee = Employee::all();
      
        return Datatables::of($employees)
        ->addColumn('employee', function($employees) {
            return $employees->name;
        })
        ->addColumn('nik', function($employees) {
            return $employees->nik;
        })
        ->addColumn('checkin', function($employees) {
            return $employees->checkin;
        })
        ->addColumn('checkout', function($employees) {
            return $employees->checkout;
        })
        ->addColumn('role', function($employees) {
            return $employees->level;
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
        ->addColumn('attendance_detail',function($employees){
            $array = [];
            $month = Carbon::parse()->format('m');
            $day = Carbon::now()->format('d');
            $year = Carbon::parse()->format('Y');
            $maxMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            
            $attendance = Attendance::where('id_employee', $employees->id)->get();
            for($i=1; $i <= $maxMonth; $i++){    
                // if($i <= $day){
               if ($attendance->count() > 0) {
                    $attendance__ = Attendance::where('id_employee', $employees->id)->whereYear('date',$year)->whereMonth('date',$month)->whereDay('date',$i)->get();
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
                                    'name' => $employees->name,
                                    'attandaceDetail' => $attendanceCollection,
                                    'attendance' => $key
                                );
                             array_push($array, "<button class='btn btn-sm btn-success btn-square' style='width:80px;height:40px' onclick='detailModal(".json_encode($data).")'>$i<br>Masuk</button>");
                             
                            }else if($key->keterangan == 'sakit'){
                                 array_push($array, "<button class='btn btn-sm btn-warning btn-square' style='width:80px;height:40px'>$i<br>Sakit</button>");
                                 
                            }else if($key->keterangan == 'alpha'){
                                array_push($array, "<button class='btn btn-sm btn-danger btn-square' style='width:80px;height:40px'>$i<br>Alpha</button>");
                                
                            }else if($key->keterangan == 'cuti'){
                                array_push($array, "<button class='btn btn-sm btn-primary btn-square' style='width:80px;height:40px'>$i<br>Cuti</button>");
                                
                            }else{
                               array_push($array, "<button class='btn btn-sm btn-secondary btn-square' style='width:80px;height:40px'>$i<br>Alpha</button>");
                               
                            }
                        // }else{
                        //     array_push($array, "<button class='btn btn-sm btn-danger btn-square' style='width:80px;height:40px'>$i</button>");
                           
                        // }
                    }
                }else{
                    array_push($array, "<button class='btn btn-sm btn-danger btn-square' style='width:80px;height:40px'>$i<br>Alpha</button>");
                } 
               }else{
                    return "<button class='btn btn-sm btn-secondary btn-square' style='width:200px;height:40px'><i class='fa fa-warning
'></i> Belum ada absen </button>";
                } 
            }
            return implode(' ', $array);
        })
        ->rawColumns(['attendance','attendance_detail'])
        ->make(true);
    }
    public function exportXLS()
    {
     Excel::create('AttendanceMTC-Report_'.Carbon::now(), function($excel){
        $excel->sheet('Attendance Report MTC', function($sheet){
            $sheet->cells('A1:G1', function($cells) {
                $cells->setFontWeight('bold');
                $cells->setAlignment('center');
                $cells->setBackground('#74fd84');
            });
            $sheet->row(1, ['Employee','Keterangan','Store','Place','Checkin','Checkout','Date']);
            $employee = Employee::all()->pluck('id');
            $array = [];
            $month = Carbon::parse()->format('m');
            $day = Carbon::now()->format('d');
            $year = Carbon::parse()->format('Y');
            $maxMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            
            $attendance = Attendance::whereIn('id_employee', $employee)->get();
              for($i=1; $i <= $maxMonth; $i++){    
                // if($i <= $day){
                $attendance__ = Attendance::whereIn('id_employee', $employee)->whereYear('date',$year)->whereMonth('date',$month)->whereDay('date',$i)->get();
                if ($attendance__->count() > 0) {
                    # code...
                    foreach ($attendance__ as $key) {
                        $attDate = date("d",strtotime($key->date));
                        // if($i==$attDate){
                            $attendanceCollection = collect();
                            foreach ($key->attendanceDetail as $attdDetail) {
                               $sheet->appendRow([
                                    $attdDetail->attendance->employee->name, 
                                    $attdDetail->attendance->keterangan, 
                                    $attdDetail->store->name1, 
                                    $attdDetail->place->name, 
                                    $attdDetail->checkin, 
                                    $attdDetail->checkout, 
                                    $attdDetail->attendance->date, 
                                ]);
                                $attendanceCollection->push($attdDetail);
                                } 
                        }
                }
            }
        });
        // $excel->sheet('Timezone List', function($sheet) {
        //     $sheet->cells('A1:G1', function($cells) {
        //         $cells->setFontWeight('bold');
        //         $cells->setAlignment('center');
        //     });
        //     $sheet->row(1, ['ID TIMEZONE', 'NAME', 'TIMEZONE']);
        //     $oke=Timezone::orderBy('created_at','DESC')->get();
        //     foreach ($oke as $ok) {
        //         $sheet->appendRow([
        //             $ok->id, 
        //             $ok->name,
        //             $ok->timezone,
        //         ]);
        //     }
        // });
    })->export('xlsx');
     return ;
    }
}
