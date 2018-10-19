<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Company;
use App\User;
use DB;

class DashboardController extends Controller
{
    public function dashboard() {
            return view('dashboard');
    }
    
    public function welcome() {
            return view('welcome');
    }
    
    // public function getCity(Request $request) {
    //     $city = City::where('id_province', $request->get('id'))->get();
    //     return response()->json($city);
    // }
    
    public function create_company(Request $request)
    {
        $data=$request->all();
        $limit=[
            'logo'          => 'max:10000|required|mimes:jpeg,jpg,bmp,png',
            'username'      => 'required|max:20',
            'name'          => 'required',
            'email'         => 'required|email',
            'phone'         => 'required|numeric',
            'fax'           => 'required|numeric',
            'address'       => 'required',
            'postal_code'   => 'required|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $logo=$data['logo'];
            $foto = time()."_".$logo->getClientOriginalName();
            $Path = 'uploads/logoCompany';
            $logo->move($Path, $foto);

            if ($request->input('price') == "one") {
                $price = 2;
                $stock = 3;
            } else if ($request->input('price') == "multi") {
                if ($request->input('stock') == 1) {
                    $price = 1;
                    $stock = 1;
                } else {
                    $price = 1;
                    $stock = 2;
                }
            }
            $insert = Company::create([
                'logo'        => $foto,
                'username'    => $request->input('username'),
                'name'        => $request->input('name'),
                'email'       => $request->input('email'),
                'phone'       => $request->input('phone'),
                'fax'         => $request->input('fax'),
                'address'     => $request->input('address'),
                'postal_code' => $request->input('postal_code'),
                'typePrice'   => $price,
                'typeStock'   => $stock,
                'token'       => md5(base64_encode(str_random(16).date("Y-m-d h:i:sa")))
            ]);
            if ($insert->id) {
                $user = User::find(Auth::user()->id);
                if ($user->save()) {
                    DB::table('positions')->insert([
                        ['name' => 'BA', 'level' => 'level 1'],
                        ['name' => 'SPV', 'level' => 'level 2'],
                        ['name' => 'Atasannya Supervisor', 'level' => 'level 3'],
                        ['name' => 'Manager', 'level' => 'level 4'],
                    ]);
                    return redirect()->route('dashboard');
                } else {
                    return redirect()->route('welcome');
                }
            } else {
                return redirect()->route('welcome');
            }
        }
    }
}