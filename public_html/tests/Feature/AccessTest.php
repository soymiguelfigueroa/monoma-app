<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

class AccessTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_post_auth_ok(): void
    {
        $this->seed();
        
        $username = 'tester';
        $password = 'PASSWORD';

        $user = User::where('username', $username)->first();

        $response = $this->post('/api/auth', [
            'username' => $username,
            'password' => $password
        ]);

        $response->assertStatus(200);

        $response_content = json_decode($response->getContent(), true);

        $payload = JWTAuth::decode(new Token($response_content['data']['token']));
        $subject = $payload->get('sub');
        
        $this->assertTrue($subject == $user->id);
    }

    public function test_post_auth_unauthorized(): void
    {
        $this->seed();
        
        $username = 'tester';
        $password = 'PASSWORD';

        $user = User::where('username', $username)->first();
        
        $response = $this->post('/api/auth', [
            'username' => $username,
            'password' => $password . '1'
        ]);

        $response->assertStatus(401);
        $response->assertExactJson([
            'meta' => [
                'success' => false,
                'errors' => [
                    "Password incorrect for: $username"
                ]
            ]
        ]);
    }
}
