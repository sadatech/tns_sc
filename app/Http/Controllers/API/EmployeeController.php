<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Config;
use Auth;
use JWTAuth;

use App\Employee;

class EmployeeController extends Controller
{

	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	private function _validateEmployeeAuth()
	{
		$this->employe_auth = JWTAuth::parseToken()->authenticate();

		if (Employee::where("id", $this->employe_auth->id)->count() > 0)
		{
			return true;	
		}

		return false;
	}

	public function _validateBodyTypeData(Request $req)
	{
		if (strlen($req->getContent()) > 0)
		{
			$reqs = new \stdClass;

			foreach (json_decode($req->getContent()) as $name => $value)
			{
				$reqs->$name = $value;
			}

			return $reqs;
		}
		else
		{
			return $req;
		}
	}

	public function editPassword(Request $req)
	{
		if ($this->_validateEmployeeAuth())
		{

			if(Validator($req->all(), ["password"=>'required'])->fails())
			{
				return response()->json(["message"=>"Operation Failed", "reason"=>"Password Field Cannot Be Empty"], 500);
			}

			DB::transaction(function() use ($req){

				//
				Employee::where("id", $this->employe_auth->id)
				->update([
					"password" => bcrypt($req->password)
				]);

			});

			return response()->json(["message"=>"Operation Success"], 200);
		}
		else
		{
			return response()->json(["message"=>"Operation Failed", "reason"=>"Employee Not Found"], 500);
		}
	}

	public function editProfile(Request $req)
	{
		if ($this->_validateEmployeeAuth())
		{
			$req = $this->_validateBodyTypeData($req);

			DB::transaction(function() use ($req){

				//
				Employee::where("id", $this->employe_auth->id)
				->update([
					"name"           => (isset($req->name) && $req->name !== null) ? $req->name : $this->employe_auth->name,
					"nik"            => (isset($req->nik) && $req->nik !== null) ? $req->nik : $this->employe_auth->nik,
					"ktp"            => (isset($req->ktp) && $req->ktp !== null) ? $req->ktp : $this->employe_auth->ktp,
					"phone"          => (isset($req->phone) && $req->phone !== null) ? $req->phone : $this->employe_auth->phone,
					"email"          => (isset($req->email) && $req->email !== null) ? $req->email : $this->employe_auth->email,
					"rekening"       => (isset($req->rekening) && $req->rekening !== null) ? $req->rekening : $this->employe_auth->rekening,
					"bank"           => (isset($req->bank) && $req->bank !== null) ? $req->bank : $this->employe_auth->bank,
				]);

			});

			return response()->json(["message"=>"Operation Success"], 200);
		}
		else
		{
			return response()->json(["message"=>"Operation Failed", "reason"=>"Employee Not Found"], 500);
		}
	}

	public function editProfilePhoto(Request $req, $photo_type = "profile")
	{
		if ($this->_validateEmployeeAuth())
		{
			$req = $this->_validateBodyTypeData($req);

			if ($req->hasFile('photo'))
			{
				$photo = $req->file('photo');

				$path = $photo->store("employee/photo_" . $photo_type);

				Employee::where("id", $this->employe_auth->id)
				->update([
					"foto_" . str_replace("profile", "profil", $photo_type) => $path
				]);

				return response()->json(["message"=>"Operation Success"], 200);
			}
			else
			{
				return response()->json(["message"=>"Operation Failed", "reason"=>"Photo Not Upladed"], 500);
			}

		}
		else
		{
			return response()->json(["message"=>"Operation Failed", "reason"=>"Employee Not Found"], 500);
		}
	}

}
