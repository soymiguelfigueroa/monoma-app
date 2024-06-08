<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Candidate;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;

class LeadsTest extends TestCase
{
    use RefreshDatabase;

    private $apiLeadsPath = '/api/leads';
    
    public function test_get_leads_by_manager_without_cache_ok(): void
    {
        $this->seed();

        $user = User::where('username', 'tester')->first();

        $candidates = Candidate::all();

        $candidatesCollection = [];
        
        foreach ($candidates as $item) {
            $candidatesCollection[] = [
                'id' => $item->id,
                'name' => $item->name,
                'source' => $item->source,
                'owner' => $item->owner,
                'created_at' => $item->created_at,
                'created_by' => $item->created_by
            ];
        }

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("{$this->apiLeadsPath}");

        $response->assertStatus(200);
        $response->assertExactJson([
            'meta' => [
                'success'=> true,
                'errors'=> []
            ],
            'data' => $candidatesCollection
        ]);

        $cache_key = "get_leads_for_user_{$user->id}";

        Redis::del($cache_key);
    }

    public function test_get_leads_by_manager_with_cache_ok(): void
    {
        $this->seed();

        $user = User::where('username', 'tester')->first();

        $candidates = Candidate::all();

        $candidatesCollection = [];
        
        foreach ($candidates as $item) {
            $candidatesCollection[] = [
                'id' => $item->id,
                'name' => $item->name,
                'source' => $item->source,
                'owner' => $item->owner,
                'created_at' => $item->created_at,
                'created_by' => $item->created_by
            ];
        }

        $cache_key = "get_leads_for_user_{$user->id}";

        $leads = Candidate::hydrate($candidatesCollection);

        Redis::set($cache_key, $leads->toJson());

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("{$this->apiLeadsPath}");

        $response->assertStatus(200);
        $response->assertExactJson([
            'meta' => [
                'success'=> true,
                'errors'=> []
            ],
            'data' => $candidatesCollection
        ]);

        Redis::del($cache_key);
    }

    public function test_get_leads_by_agent_without_cache_ok(): void
    {
        $this->seed();

        $user = User::where('username', 'agent_tester')->first();

        $token = JWTAuth::fromUser($user);

        $candidates = $user->candidates;

        $candidatesCollection = [];
        
        foreach ($candidates as $item) {
            $candidatesCollection[] = [
                'id' => $item->id,
                'name' => $item->name,
                'source' => $item->source,
                'owner' => $item->owner,
                'created_at' => $item->created_at,
                'created_by' => $item->created_by
            ];
        }

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("{$this->apiLeadsPath}");

        $response->assertStatus(200);
        $response->assertExactJson([
            'meta' => [
                'success'=> true,
                'errors'=> []
            ],
            'data' => $candidatesCollection
        ]);

        $cache_key = "get_leads_for_user_{$user->id}";

        Redis::del($cache_key);
    }

    public function test_get_leads_by_agent_with_cache_ok(): void
    {
        $this->seed();

        $user = User::where('username', 'agent_tester')->first();

        $token = JWTAuth::fromUser($user);

        $candidates = $user->candidates;

        $candidatesCollection = [];

        foreach ($candidates as $item) {
            $candidatesCollection[] = [
                'id' => $item->id,
                'name' => $item->name,
                'source' => $item->source,
                'owner' => $item->owner,
                'created_at' => $item->created_at,
                'created_by' => $item->created_by
            ];
        }

        $cache_key = "get_leads_for_user_{$user->id}";

        $leads = Candidate::hydrate($candidatesCollection);

        Redis::set($cache_key, $leads->toJson());

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("{$this->apiLeadsPath}");

        $response->assertStatus(200);
        $response->assertExactJson([
            'meta' => [
                'success'=> true,
                'errors'=> []
            ],
            'data' => $candidatesCollection
        ]);

        Redis::del($cache_key);
    }

    public function test_get_leads_by_agent_with_cache_no_leads_found(): void
    {
        $this->seed();

        $user = User::where('username', 'agent_tester')->first();

        $token = JWTAuth::fromUser($user);

        Candidate::truncate();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("{$this->apiLeadsPath}");

        $response->assertStatus(404);
        $response->assertExactJson([
            'meta' => [
                'success' => false,
                'errors' => [
                    'No leads found'
                ]
            ]
        ]);

        $cache_key = "get_leads_for_user_{$user->id}";
        
        Redis::del($cache_key);
    }

    public function test_get_leads_unauthorized(): void
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
        
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("{$this->apiLeadsPath}");

        $response->assertStatus(401);
        $response->assertExactJson([
            'meta' => [
                'success'=> false,
                'errors'=> [
                    'Token expired'
                ]
            ],
        ]);
    }
}
