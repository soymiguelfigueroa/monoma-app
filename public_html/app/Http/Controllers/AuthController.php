<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        $credentials = request(['username', 'password']);
        
        if ($token = auth()->attempt($credentials)) {
            $code = 200;
            $response = [
                'meta' => [
                    'success' => true,
                    'errors' => []
                ],
                'data' => [
                    'token' => $token,
                    'minutes_to_expire' => Config::get('jwt.ttl')
                ]
            ];
        } else {
            $code = 401;
            $response = [
                'meta' => [
                    'success' => false,
                    'errors' => [
                        "Password incorrect for: $request->username"
                    ]
                ]
            ];
        }
    
        return response()->json($response, $code);
    }
}
