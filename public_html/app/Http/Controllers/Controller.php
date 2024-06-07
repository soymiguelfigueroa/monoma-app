<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Tymon\JWTAuth\Facades\JWTAuth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function validateToken($token)
    {
        try {
            if (!$token) {
                return [
                    'success' => false,
                    'code' => 401,
                    'response' => [
                        'meta' => [
                            'success' => false,
                            'errors' => [
                                'Token not found'
                            ]
                        ]
                    ]
                ];
            }
    
            $user = JWTAuth::parseToken($token)->authenticate();
    
            if (!$user) {
                return [
                    'success' => false,
                    'code' => 401,
                    'response' => [
                        'meta' => [
                            'success' => false,
                            'errors' => [
                                'User not found'
                            ]
                        ]
                    ]
                ];
            }
    
            return [
                'success'=> true,
                'user' => $user
            ];
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return [
                'success' => false,
                'code' => 401,
                'response' => [
                    'meta' => [
                        'success' => false,
                        'errors' => [
                            'Token expired'
                        ]
                    ]
                ]
            ];
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return [
                'success' => false,
                'code' => 401,
                'response' => [
                    'meta' => [
                        'success' => false,
                        'errors' => [
                            'Invalid token'
                        ]
                    ]
                ]
            ];
        }
    }
}
