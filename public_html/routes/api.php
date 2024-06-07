<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth', function (Request $request) {
    $response_code = 200;
    $response = [
        'meta' => [
            'success' => true,
            'errors' => []
        ],
        'data' => [
            'token' => 'TOOOOOKEN',
            'minutes_to_expire' => '1440'
        ]
    ];
    
    if ($request->username != 'tester' || $request->password != 'PASSWORD') {
        $response_code = 401;
        $response = [
            'meta' => [
                'success' => false,
                'errors' => [
                    "Password incorrect for: $request->username"
                ]
            ]
        ];
    }

    return response()->json($response, $response_code);
});

Route::post('/lead', function (Request $request) {
    return response()->json([
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
    ], 201);
});
Route::get('/lead/{id}', function (Request $request) {
    $response_code = 200;
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
        $response_code = 404;
        $response = [
            'meta' => [
                'success' => false,
                'errors' => [
                    'No lead found'
                ]
            ]
        ];
    }
    
    return response()->json($response, $response_code);
});

Route::get('/leads', function (Request $request) {
    return response()->json([
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
    ], 200);
});
