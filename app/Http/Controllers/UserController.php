<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use DB;
use JWTAuth;
use Yajra\Datatables\Datatables;
use App\UserRole;
use App\Filters\RoleFilters;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class UserController extends Controller
{
     use RegistersUsers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    public function index()
    {
       $data['role'] = UserRole::get();
       return view('users.users',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data()
    {
          $user = DB::table('users')
            ->join('user_role', 'users.role_id', '=', 'user_role.id')
            ->select('users.*', 'user_role.level', 'users.role_id');

        return Datatables::of($user)
        ->addColumn('action', function ($user) {
             return "<button onclick='editModal(".json_encode($user).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('user.delete', $user->id)." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $data=$request->all();
        $limit=[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $check = User::whereRaw("TRIM(UPPER(email)) = '". strtoupper($request->input('email'))."'")->count();
            if ($check < 1) {
                User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'role_id' => $request->input('role'),
                    'email_status' => 'unverified',
                    'password' => bcrypt($request->input('password')),
                ]);
                return redirect()->back()
                ->with([
                    'type'   => 'success',
                    'title'  => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah User!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Email sudah ada!'
                ]);
            }
        }
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
        $this->cekAuth = Auth::user();

        $data=$request->all();
        $limit=[
            'name' => 'required|string|max:255',
            // 'email' => 'required|string|email|max:255|unique:users',
            // 'password' => 'required|string|min:6|confirmed',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $check = User::where('id','!=',$id)->whereRaw("TRIM(UPPER(email)) = '". strtoupper($request->input('email'))."'")->count();
            if ($check < 1) {
                User::findOrFail($id)
                ->update([
                    'name' => (isset($request->name) && $request->name !== null) ? $request->name : $this->cekAuth->name,
                    'email' => (isset($request->email) && $request->email !== null) ? $request->email : $this->cekAuth->email,
                    'role_id' => (isset($request->role) && $request->role !== null) ? $request->role : $this->cekAuth->role,
                    'password' => bcrypt((isset($request->password) && $request->password !== null) ? $request->password : $this->cekAuth->password),
                ]);
                return redirect()->back()
                ->with([
                    'type'   => 'success',
                    'title'  => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil merubah User!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Email sudah ada!'
                ]);
            }
        }

    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
         return redirect()->back()
                        ->with([
                            'type'   => 'success',
                            'title'  => 'Sukses!<br/>',
                            'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menghapus User!'
                        ]);
            }
}
