<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Candidate;
use Tymon\JWTAuth\Facades\JWTAuth;

class LeadsTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_get_leads_by_manager_ok(): void
    {
        $this->seed();

        $user = User::where('username', 'tester')->first();

        $candidates = Candidate::all();

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

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("/api/leads");

        $response->assertStatus(200);
        $response->assertExactJson([
            'meta' => [
                'success'=> true,
                'errors'=> []
            ],
            'data' => $candidatesCollection
        ]);
    }

    public function test_get_leads_by_agent_ok(): void
    {
        $this->seed();

        $user = User::where('username', 'agent_tester')->first();

        $token = JWTAuth::fromUser($user);

        $candidates = $user->candidates;

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

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->get("/api/leads");

        $response->assertStatus(200);
        $response->assertExactJson([
            'meta' => [
                'success'=> true,
                'errors'=> []
            ],
            'data' => $candidatesCollection
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
