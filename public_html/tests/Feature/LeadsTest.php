<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class LeadsTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_get_leads_by_manager_ok(): void
    {
        $this->seed();

        $user = User::where('username', 'tester')->first();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("/api/leads");

        $response->assertStatus(200);
        $response->assertExactJson([
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
        ]);
    }

    public function test_get_leads_by_agent_ok(): void
    {
        $this->seed();

        $user = User::where('username', 'agent_tester')->first();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("/api/leads");

        $response->assertStatus(200);
        $response->assertExactJson([
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
        ]);
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
        
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("/api/leads");

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
