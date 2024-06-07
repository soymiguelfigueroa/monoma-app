<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Carbon;

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

        $response->assertStatus(201);
        $response->assertExactJson([
            'meta' => [
                'success' => true,
                'errors' => []
            ],
            'data' => [
                'id' => 1,
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

    public function test_get_lead_ok(): void
    {
        $this->seed();

        $user = User::where('username', 'tester')->first();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("/api/lead/1");

        $response->assertStatus(200);
        $response->assertExactJson([
            'meta' => [
                'success'=> true,
                'errors'=> []
            ],
            'data' => [
                'id' => "1",
                'name' => 'Mi candidato',
                'source' => 'Fotocasa',
                'owner' => 2,
                'created_at' => '2020-09-01 16:16:16',
                'created_by' => 1
            ]
        ]);
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

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("/api/lead/2");

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
