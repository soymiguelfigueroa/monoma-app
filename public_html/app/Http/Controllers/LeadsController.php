<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Contracts\LeadsRepositoryInterface;
use App\Http\Resources\LeadsCollection;

class LeadsController extends Controller
{
    private $leadsRepository;

    public function __construct(LeadsRepositoryInterface $leadsRepository)
    {
        $this->leadsRepository = $leadsRepository;
    }
    
    public function get(Request $request) 
    {
        $tokenValidated = $this->validateToken(JWTAuth::getToken());

        if ($tokenValidated['success']) {
            $user = $tokenValidated['user'];

            $leads = $this->leadsRepository->getLeads($user);

            if ($leads) {
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
