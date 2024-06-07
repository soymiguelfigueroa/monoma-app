<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Facades\JWTAuth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth', function (Request $request) {
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
});

Route::group(['middleware' => ['api']], function () {
    Route::post('/lead', function (Request $request) {
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
    
            $code = 201;
            $response = [
                'meta' => [
                    'success' => true,
                    'errors' => []
                ],
                'data' => [
                    'id' => '1',
                    'name' => 'Mi candidato',
                    'source' => 'Fotocasa',
                    'owner' => 2,
                    'created_at' => '2020-09-01 16:16:16',
                    'created_by' => 1
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
    });
    Route::get('/lead/{id}', function (Request $request) {
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
                    'id' => "$request->id",
                    'name' => 'Mi candidato',
                    'source' => 'Fotocasa',
                    'owner' => 2,
                    'created_at' => '2020-09-01 16:16:16',
                    'created_by' => 1
                ]
            ];
            
            if ($request->id != 1) {
                $code = 404;
                $response = [
                    'meta' => [
                        'success' => false,
                        'errors' => [
                            'No lead found'
                        ]
                    ]
                ];
            }
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
    });
    
    Route::get('/leads', function (Request $request) {
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
    });
});
