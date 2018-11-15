<?php

namespace App\Components\traits;
use JWTAuth;

/**
 * 
 */

trait ApiAuthHelper
{

	public static function authCheck(){
		$res['success'] = false;
		try {
			$res['code'] = 200;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				}else{
					$res['success'] = true;
					$res['user'] 	= $user;
				}
			}else{
				$res['msg'] = "User not found.";
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['msg'] = "Token Expired.";
			$res['code'] = $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['msg'] = "Token Invalid.";
			$res['code'] = $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['msg'] = "Token Absent.";
			$res['code'] = $e->getStatusCode();
		}
		return $res;
	}
	
}