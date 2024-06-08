<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Tymon\JWTAuth\Facades\JWTAuth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * validate if a token is valid for the incoming request
     */
    protected function validateToken($token)
    {
        try {
            if (!$token) {
                $response = [
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
            } else {
                $user = JWTAuth::parseToken($token)->authenticate();
    
                if (!$user) {
                    $response = [
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
                } else {
                    $response = [
                        'success'=> true,
                        'user' => $user
                    ];
                }
            }

            return $response;
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
        }
    }
}
