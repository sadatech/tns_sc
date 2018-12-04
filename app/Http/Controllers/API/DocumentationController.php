<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\DocumentationDc;
use Config;
use JWTAuth;
use Image;
use Carbon\Carbon;

class DocumentationController extends Controller
{
	use ApiAuthHelper;

	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request)
	{
		$check = $this->authCheck();
		$code = 200;
		if ($check['success'] == true) {
			
			$user = $check['user'];
			$date 	= Carbon::now()->toDateString();
			$path 	= 'uploads/documentation';

			if ($image1 	= $request->file('photo1')) {
				$photo1 	= time()."_".$image1->getClientOriginalName();
				$image1->move($path, $photo1);
				$image_compress = Image::make($path.'/'.$photo1)->orientate();
				$image_compress->save($path.'/'.$photo1, 50);
			}

			if ($image2 	= $request->file('photo2')) {
				$photo2 	= time()."_".$image2->getClientOriginalName();
				$image2->move($path, $photo2);
				$image_compress = Image::make($path.'/'.$photo2)->orientate();
				$image_compress->save($path.'/'.$photo2, 50);
			}

			if ($image3 	= $request->file('photo3')) {
				$photo3 	= time()."_".$image3->getClientOriginalName();
				$image3->move($path, $photo3);
				$image_compress = Image::make($path.'/'.$photo3)->orientate();
				$image_compress->save($path.'/'.$photo3, 50);
			}

			$insert 	= DocumentationDc::create([
				'id_employee'	=> $user->id,
				'date'			=> $date,
				'place'			=> $request->place,
				'type'			=> $request->type,
				'note'			=> $request->note,
				'photo1'		=> $photo1 ?? null,
				'photo2'		=> $photo2 ?? null,
				'photo3'		=> $photo3 ?? null,
			]);

			if ($insert->id) {
				$res['success'] = true;
				$res['msg'] 	= "Success insert Documentation.";
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Fail insert Documentation.";
			}

		}else{
			$res = $check;
			$code = $res['code'];
			unset($res['code']);
		}
		
		return response()->json($res, $code);
	}

}