<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccessTest extends TestCase
{
    public function test_post_auth_ok(): void
    {
        $response = $this->post('/api/auth', [
            'username' => 'tester',
            'password' => 'PASSWORD'
        ]);

        $response->assertStatus(200);
        $response->assertExactJson([
            'meta' => [
                'success' => true,
                'errors' => []
            ],
            'data' => [
                'token' => 'TOOOOOKEN',
                'minutes_to_expire' => '1440'
            ]
        ]);
    }

    public function test_post_auth_unauthorized(): void
    {
        $response = $this->post('/api/auth', [
            'username' => 'tester',
            'password' => 'PASSWORD1'
        ]);

        $response->assertStatus(401);
        $response->assertExactJson([
            'meta' => [
                'success' => false,
                'errors' => [
                    "Password incorrect for: tester"
                ]
            ]
        ]);
    }
}
