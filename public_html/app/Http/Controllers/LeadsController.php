<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Contracts\LeadsRepositoryInterface;
use App\Http\Resources\LeadsCollection;
use Illuminate\Support\Facades\Redis;
use App\Models\Candidate;

class LeadsController extends Controller
{
    private $leadsRepository;

    public function __construct(LeadsRepositoryInterface $leadsRepository)
    {
        $this->leadsRepository = $leadsRepository;
    }
    
    /**
     * Get all leads.
     */
    public function get(Request $request) 
    {
        $tokenValidated = $this->validateToken(JWTAuth::getToken());

        if ($tokenValidated['success']) {
            $user = $tokenValidated['user'];

            $cache_key = "get_leads_for_user_{$user->id}"; // Setting redis cache key for this case

            /**
             * If key does not exists in redis databases, get data from database
             */
            if (!$leads = Redis::get($cache_key)) {
                $leads = $this->leadsRepository->getLeads($user);
            }

            if ((gettype($leads) == 'object' && $leads->count() > 0) || (gettype($leads) == 'string' && !empty($leads))) {
                /**
                 * If $lead is Object, that means data comes from database. It is saved on redis.
                 * Otherwise, create a Candidate instance with redis data.
                 */
                if (gettype($leads) == 'object') {
                    Redis::set($cache_key, $leads->toJson());
                } elseif (gettype($leads) == 'string') {
                    $object = (array) json_decode($leads);
                    $leads = Candidate::hydrate($object);
                }

                $code = 200;
                $response = new LeadsCollection($leads);
            } else {
                $code = 404;
                $response = [
                    'meta' => [
                        'success' => false,
                        'errors' => [
                            'No leads found'
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
