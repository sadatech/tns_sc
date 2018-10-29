<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Employee;
use App\EmployeeStore;
use JWTFactory;
use JWTAuth;
use Config;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function user(Request $request)
	{
		$limit=[
			'nik' => 'required|alpha_dash',
			'password' => 'max:25|required',
		];
		$validator = Validator($request->all(), $limit);
		if ($validator->fails()){
			$res['success'] = false;
			if ($validator->messages()->get('nik')) {
				$message = "NIK tidak boleh mengandung spesial karakter dan spasi!";
			} else {
				$message = "Terjadi Kesalahan.";
			}
			$res['msg'] = $message;
			$code = 200;
		} else {
			$user = Employee::where(['nik' => $request->input('nik')])->first();
			if ($user) {
				if (!$user->isResign) {
					if (Hash::check($request->input('password'), $user->password)) {
						$credentials = $request->only('nik', 'password');
						try {
							Config::set('auth.providers.users.model', \App\Employee::class);
							if (!$token = JWTAuth::attempt($credentials)) {
								$res['success'] = false;
								$res['msg'] = 'Invalid username/password or account not found.';
								$code = 200;
							} else {
								$res['success'] = true;
								$res['msg'] = 'Login success.';
								$res['user'] = array(
									'id' => $user->id,
									'name' => $user->name,
									'nik' => $user->nik,
									'phone' => $user->phone,
									'email' => $user->email,
									'ktp' => $user->ktp,
									'rekening' => $user->rekening,
									'status' => $user->status,
									'joinAt' => $user->joinAt,
									'gender' => $user->gender,
									'education' => $user->education,
									'birthdate' => $user->birthdate,
									'level' => $user->position,
									'position' => $user->position->name,
									'agency' => $user->agency->name,
									'token' => $token
								);
								$code = 200;
							}
						} catch (JWTException $e) {
							$res['success'] = false;
							$res['msg'] = 'Unable to generate token.';
							$code = 200;
						}
					} else {
						$res['success'] = false;
						$res['msg'] = 'Invalid username/password or account not found.';
						$code = 200;
					}
				} else {
					$res['success'] = false;
					$res['msg'] = 'This user has been resigned.';
					$code = 200;
				}
			} else {
				$res['success'] = false;
				$res['msg'] = 'Invalid username/password or account not found.';
				$code = 200;
			}
		}
		return response()->json($res, $code);
	}

	public function login(Request $request)
	{
		$credentials = $request->only('nik', 'password');
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
            // verify the credentials and create a token for the user
			if (! $token = JWTAuth::attempt($credentials)) {
				return response()->json(['error' => 'invalid_credentials'], 401);
			}else{
				$user =  Auth::user();
				return response()->json(
					[
						'status' 	=> true,
						'name' 		=> $user->name,
						'email' 	=> $user->email,
						'photo' 	=> $user->foto_profil,
						'level' 	=> $user->position->level,
						'level_name'=> $user->position->name,
						'token' 	=> $token
					]
				);
			}
		} catch (JWTException $e) {
            // something went wrong
			return response()->json(['error' => 'could_not_create_token'], 500);
		}
		// return response()->json($token);
	}

	public function getUser()
	{
		try {
			// if (! $user = JWTAuth::toUser(JWTAuth::getToken())) {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}

		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

			return response()->json(['token_expired'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

			return response()->json(['token_invalid'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

			return response()->json(['token_absent'], $e->getStatusCode());

		}
		return response()->json($user);
	}
}