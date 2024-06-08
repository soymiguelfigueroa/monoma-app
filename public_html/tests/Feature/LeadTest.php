<?php

namespace Tests\Feature;

use App\Models\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class LeadTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_post_lead_by_manager(): void
    {
        $this->seed();

        $user = User::where('username', 'tester')->first();

        $token = JWTAuth::fromUser($user);

        $agent = User::where('role', 'agent')->first();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->post('/api/lead', [
            'name' => 'Mi candidato',
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
                'name' => 'Mi candidato',
                'source' => 'Fotocasa',
                'owner' => $agent->id,
                'created_at' => Carbon::now()->format('Y-m-d H:m:s'),
                'created_by' => $user->id
            ]
        ]);
    }

    public function test_post_lead_by_agent(): void
    {
        $this->seed();

        $user = User::where('username', 'agent_tester')->first();

        $token = JWTAuth::fromUser($user);

        $agent = User::where('role', 'agent')->first();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->post('/api/lead', [
            'name' => 'Mi candidato',
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
        
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->post('/api/lead', [
            'name' => 'Mi candidato',
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

    public function test_get_lead_without_cache_ok(): void
    {
        $this->seed();

        $user = User::where('username', 'agent_tester')->first();

        $token = JWTAuth::fromUser($user);

        $userCandidate = $user->candidates()->inRandomOrder()->first();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("/api/lead/$userCandidate->id");

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

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("/api/lead/$userCandidate->id");

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
        
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("/api/lead/1");

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

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("/api/lead/$candidateIdThatNotExists");

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
