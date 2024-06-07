<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LeadsController extends Controller
{
    public function get(Request $request) 
    {
        $tokenValidated = $this->validateToken(JWTAuth::getToken());

        if ($tokenValidated['success']) {
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
        } else {
            $code = $tokenValidated['code'];
            $response = $tokenValidated['response'];
        }

        return response()->json($response, $code);
    }
}
