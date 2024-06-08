<?php

namespace Tests\Feature;

use App\Models\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    private $apiLeadPath = '/api/lead';
    private $testLeadName = 'Mi candidato';
    
    public function test_post_lead_by_manager(): void
    {
        $this->seed();

        $user = User::where('username', 'tester')->first();

        $token = JWTAuth::fromUser($user);

        $agent = User::where('role', 'agent')->first();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->post($this->apiLeadPath, [
            'name' => $this->testLeadName,
            'source' => 'Fotocasa',
            'owner' => $agent->id
        ]);

        $candidate = Candidate::latest()->first();

        $response->assertStatus(201);
        $response->assertExactJson([
            'meta' => [
                'success' => true,
                'errors' => []
            ],
            'data' => [
                'id' => $candidate->id,
                'name' => $this->testLeadName,
                'source' => 'Fotocasa',
                'owner' => $agent->id,
                'created_at' => Carbon::now()->format('Y-m-d H:m:s'),
                'created_by' => $user->id
            ]
        ]);
    }

    public function test_post_lead_by_manager_with_errors_in_data(): void
    {
        $this->seed();

        $user = User::where('username', 'tester')->first();

        $token = JWTAuth::fromUser($user);

        $agent = User::where('role', 'agent')->first();

        $data = [
            'name' => 123456,
            'source' => 'Fotocasa',
            'owner' => $agent->id
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->post($this->apiLeadPath, $data);

        $response->assertStatus(422);
        $response->assertExactJson([
                    'meta' => [
                        'success' => false,
                        'errors' => [
                            'name' => [
                                'The name field must be a string.'
                            ]
                        ]
                    ]
                ]);
    }

    public function test_post_lead_by_manager_with_exception(): void
    {
        $this->seed();

        $user = User::where('username', 'tester')->first();

        $token = JWTAuth::fromUser($user);

        $agent = User::where('role', 'agent')->first();

        DB::statement('drop table candidato');

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->post($this->apiLeadPath, [
            'name' => $this->testLeadName,
            'source' => 'Fotocasa',
            'owner' => $agent->id
        ]);

        $response->assertStatus(500);
        $response->assertExactJson([
            'meta' => [
                'success' => false,
                'errors' => [
                    'The lead could not be saved'
                ]
            ]
        ]);
    }

    public function test_post_lead_by_agent(): void
    {
        $this->seed();

        $user = User::where('username', 'agent_tester')->first();

        $token = JWTAuth::fromUser($user);

        $agent = User::where('role', 'agent')->first();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->post($this->apiLeadPath, [
            'name' => $this->testLeadName,
            'source' => 'Fotocasa',
            'owner' => $agent->id
        ]);

        $response->assertStatus(403);
        $response->assertExactJson([
            'meta' => [
                'success' => false,
                'errors' => [
                    'You cannot create candidates'
                ]
            ]
        ]);
    }

    public function test_post_lead_unauthorized(): void
    {
        $this->seed();

        /**
         * {
         *      "iss": "Online JWT Builder",
         *      "iat": 1717771658,
         *      "exp": 1591541371,
         *      "aud": "www.example.com",
         *      "sub": "1",
         *      "GivenName": "Johnny",
         *      "Surname": "Rocket",
         *      "Email": "jrocket@example.com",
         *      "Role": "manager"
         *  }
         */
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJPbmxpbmUgSldUIEJ1aWxkZXIiLCJpYXQiOjE3MTc3NzE2NTgsImV4cCI6MTU5MTU0MTM3MSwiYXVkIjoid3d3LmV4YW1wbGUuY29tIiwic3ViIjoiMSIsIkdpdmVuTmFtZSI6IkpvaG5ueSIsIlN1cm5hbWUiOiJSb2NrZXQiLCJFbWFpbCI6Impyb2NrZXRAZXhhbXBsZS5jb20iLCJSb2xlIjoibWFuYWdlciJ9.WVqYV9zLZglkj7Y40f7oZJ5hIl2lAAc8DchyJgmU5dc';
        
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->post($this->apiLeadPath, [
            'name' => $this->testLeadName,
            'source' => 'Fotocasa',
            'owner' => 2
        ]);

        $response->assertStatus(401);
        $response->assertExactJson([
            'meta' => [
                'success' => false,
                'errors' => [
                    'Token expired'
                ]
            ]
        ]);
    }

    public function test_post_lead_without_token(): void
    {
        $this->seed();
        
        $token = '';
        
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->post($this->apiLeadPath, [
            'name' => $this->testLeadName,
            'source' => 'Fotocasa',
            'owner' => 2
        ]);

        $response->assertStatus(401);
        $response->assertExactJson([
            'meta' => [
                'success' => false,
                'errors' => [
                    'Token not found'
                ]
            ]
        ]);
    }

    public function test_post_lead_with_token_not_found(): void
    {
        $this->seed();

        $user = User::where('username', 'agent_tester')->first();

        $token = JWTAuth::fromUser($user);

        DB::table('usuario')->where('username', '=', 'agent_tester')->delete();
        
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->post($this->apiLeadPath, [
            'name' => $this->testLeadName,
            'source' => 'Fotocasa',
            'owner' => 2
        ]);

        $response->assertStatus(401);
        $response->assertExactJson([
            'meta' => [
                'success' => false,
                'errors' => [
                    'User not found'
                ]
            ]
        ]);
    }

    public function test_get_lead_without_cache_ok(): void
    {
        $this->seed();

        $user = User::where('username', 'agent_tester')->first();

        $token = JWTAuth::fromUser($user);

        $userCandidate = $user->candidates()->inRandomOrder()->first();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("{$this->apiLeadPath}/$userCandidate->id");

        $response->assertStatus(200);
        $response->assertExactJson([
            'meta' => [
                'success'=> true,
                'errors'=> []
            ],
            'data' => [
                'id' => $userCandidate->id,
                'name' => $userCandidate->name,
                'source' => $userCandidate->source,
                'owner' => $userCandidate->owner,
                'created_at' => $userCandidate->created_at,
                'created_by' => $userCandidate->created_by
            ]
        ]);

        $cache_key = "get_lead_{$userCandidate->id}_for_user_{$user->id}";

        Redis::del($cache_key);
    }

    public function test_get_lead_with_cache_ok(): void
    {
        $this->seed();

        $user = User::where('username', 'agent_tester')->first();

        $token = JWTAuth::fromUser($user);

        $userCandidate = $user->candidates()->inRandomOrder()->first();

        $cache_key = "get_lead_{$userCandidate->id}_for_user_{$user->id}";

        Redis::set($cache_key, $userCandidate->toJson());

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("{$this->apiLeadPath}/$userCandidate->id");

        $response->assertStatus(200);
        $response->assertExactJson([
            'meta' => [
                'success'=> true,
                'errors'=> []
            ],
            'data' => [
                'id' => $userCandidate->id,
                'name' => $userCandidate->name,
                'source' => $userCandidate->source,
                'owner' => $userCandidate->owner,
                'created_at' => $userCandidate->created_at,
                'created_by' => $userCandidate->created_by
            ]
        ]);

        Redis::del($cache_key);
    }

    public function test_get_lead_unauthorized(): void
    {
        $this->seed();

        /**
         * {
         *      "iss": "Online JWT Builder",
         *      "iat": 1717771658,
         *      "exp": 1591541371,
         *      "aud": "www.example.com",
         *      "sub": "1",
         *      "GivenName": "Johnny",
         *      "Surname": "Rocket",
         *      "Email": "jrocket@example.com",
         *      "Role": "manager"
         *  }
         */
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJPbmxpbmUgSldUIEJ1aWxkZXIiLCJpYXQiOjE3MTc3NzE2NTgsImV4cCI6MTU5MTU0MTM3MSwiYXVkIjoid3d3LmV4YW1wbGUuY29tIiwic3ViIjoiMSIsIkdpdmVuTmFtZSI6IkpvaG5ueSIsIlN1cm5hbWUiOiJSb2NrZXQiLCJFbWFpbCI6Impyb2NrZXRAZXhhbXBsZS5jb20iLCJSb2xlIjoibWFuYWdlciJ9.WVqYV9zLZglkj7Y40f7oZJ5hIl2lAAc8DchyJgmU5dc';
        
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("{$this->apiLeadPath}/1");

        $response->assertStatus(401);
        $response->assertExactJson([
            'meta' => [
                'success' => false,
                'errors' => [
                    'Token expired'
                ]
            ]
        ]);
    }

    public function test_get_lead_not_found(): void
    {
        $this->seed();

        $user = User::where('username', 'tester')->first();

        $token = JWTAuth::fromUser($user);

        $candidateIdThatNotExists = Candidate::orderBy('id', 'desc')->first()->id + 1;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("{$this->apiLeadPath}/$candidateIdThatNotExists");

        $response->assertStatus(404);
        $response->assertExactJson([
            'meta' => [
                'success' => false,
                'errors' => [
                    'No lead found'
                ]
            ]
        ]);
    }
}
