<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeadResource;
use App\Contracts\LeadRepositoryInterface;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Candidate;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests\CreateLeadRequest;

class LeadController extends Controller
{
    private $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }
    
    /**
     * Lead storing. Only managers can store leads
     */
    public function create(CreateLeadRequest $request)
    {
        $tokenValidated = $this->validateToken(JWTAuth::getToken());

        if ($tokenValidated['success']) {
            $user = $tokenValidated['user'];

            if ($user->role == 'manager') {
                try {
                    $lead = $this->leadRepository->createLead(new Candidate, $request, $user);

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

    /**
     * Get a specific lead
     */
    public function get(Request $request)
    {
        $tokenValidated = $this->validateToken(JWTAuth::getToken());

        if ($tokenValidated['success']) {
            $user = $tokenValidated['user'];

            $cache_key = "get_lead_{$request->id}_for_user_{$user->id}"; // Setting redis cache key for this case

            /**
             * If key does not exists in redis databases, get data from database
             */
            if (!$lead = Redis::get($cache_key)) {
                $lead = $this->leadRepository->getLeadById($request->id, $user);
            }

            if ($lead) {
                /**
                 * If $lead is Object, that means data comes from database. It is saved on redis.
                 * Otherwise, create a Candidate instance with redis data.
                 */
                if (gettype($lead) == 'object') {
                    Redis::set($cache_key, $lead->toJson());
                } elseif (gettype($lead) == 'string') {
                    $lead = new Candidate(json_decode($lead, true));
                }
                
                $code = 200;
                $response = new LeadResource($lead);
            } else {
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
