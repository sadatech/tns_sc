<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Company;
use App\Province;
use App\City;

class CompanyController extends Controller
{
    public function baca()
    {
        $data['province']       = Province::all();
        $data['company']        = Company::get();
        return view('company.profile', $data);
    }

    public function getCity(Request $request)
     {
        $city = City::where('id_province', $request->get('id'))->get();
        return response()->json($city);
    }

    public function update(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'logo'          => 'max:10000|required|mimes:jpeg,jpg,bmp,png',
            'province'      => 'required|numeric',
            'city'          => 'required|numeric',
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
            $company = Company::find($id);
                if ($request->file('logo')) {
			    	$foto = $request->file('logo');
			    	$logo = time()."_".rand(1,99999).".".$foto->getClientOriginalExtension();
			    	$logo_path = 'uploads/logoCompany';
			    	$foto->move($logo_path, $logo);
			    } else {
			    	$logo = "Failed change Logo";
			    }
                if($request->file('logo')){
                    $company->logo = $logo;
                }
                $company->username      = $request->input('username');
                $company->name          = $request->input('name');
                $company->email         = $request->input('email');
                $company->phone         = $request->input('phone');
                $company->fax           = $request->input('fax');
                $company->address       = $request->input('address');
                $company->postal_code   = $request->input('postal_code');
                $company->id_province   = $request->input('province');
                $company->id_city       = $request->input('city');
                $company->save();
                return redirect()->back()
                ->with([
                    'type' => 'success',
                    'title' => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah company!'
                ]);
        }
    }
}
