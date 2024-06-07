<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LeadsController extends Controller
{
    public function get(Request $request) 
    {
        $token = JWTAuth::getToken();

        try {
            if (!$token) {
                return response()->json([
                    'meta' => [
                        'success' => false,
                        'errors' => [
                            'Token not found'
                        ]
                    ]
                ], 401);
            }
    
            $user = JWTAuth::parseToken($token)->authenticate();

            if (!$user) {
                return response()->json([
                    'meta' => [
                        'success' => false,
                        'errors' => [
                            'User not found'
                        ]
                    ]
                ], 401);
            }
    
            $code = 200;
            $response = [
                'meta' => [
                    'success'=> true,
                    'errors'=> []
                ],
                'data' => [
                    [
                        'id' => "1",
                        'name' => 'Mi candidato',
                        'source' => 'Fotocasa',
                        'owner' => 2,
                        'created_at' => '2020-09-01 16:16:16',
                        'created_by' => 1
                    ],
                    [
                        'id' => "2",
                        'name' => 'Mi candidato 2',
                        'source' => 'Habitaclia',
                        'owner' => 2,
                        'created_at' => '2020-09-01 16:16:16',
                        'created_by' => 1
                    ],
                ]
            ];
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            $code = 401;
            $response = [
                'meta' => [
                    'success' => false,
                    'errors' => [
                        'Token expired'
                    ]
                ]
            ];
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            $code = 401;
            $response = [
                'meta' => [
                    'success' => false,
                    'errors' => [
                        'Invalid token'
                    ]
                ]
            ];
        }

        return response()->json($response, $code);
    }
}
