<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeadResource;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Candidate;
use Illuminate\Support\Carbon;

class LeadController extends Controller
{
    public function create(Request $request)
    {
        $tokenValidated = $this->validateToken(JWTAuth::getToken());

        if ($tokenValidated['success']) {
            $user = $tokenValidated['user'];

            if ($user->role == 'manager') {
                try {
                    $lead = new Candidate;
                    $lead->name = $request->name;
                    $lead->source = $request->source;
                    $lead->owner = $request->owner;
                    $lead->created_at = Carbon::now()->format('Y-m-d H:m:s');
                    $lead->created_by = $user->id;
                    $lead->save();

                    $code = 201;
                    $response = new LeadResource($lead);
                } catch (\Exception $e) {
                    $code = 500;
                    $response = [
                        'meta' => [
                            'success' => false,
                            'errors' => [
                                'The lead could not be saved'
                            ]
                        ]
                    ];
                }
            } else {
                $code = 403;
                $response = [
                    'meta' => [
                        'success' => false,
                        'errors'=> [
                            'You cannot create candidates'
                        ]
                    ]
                ];
            }
        } else {
            $code = $tokenValidated['code'];
            $response = $tokenValidated['response'];
        }

        return response()->json($response, $code);
    }

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
        } else {
            $code = $tokenValidated['code'];
            $response = $tokenValidated['response'];
        }

        return response()->json($response, $code);
    }
}
