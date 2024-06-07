<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LeadTest extends TestCase
{
    public function test_post_lead_ok(): void
    {
        $user = User::factory()->create();
        dd($user);
        $response = $this->post('/api/lead', [
            'name' => 'Mi candidato',
            'source' => 'Fotocasa',
            'owner' => 2
        ]);

        $response->assertStatus(201);
        $response->assertExactJson([
            'meta' => [
                'success' => true,
                'errors' => []
            ],
            'data' => [
                'id' => '1',
                'name' => 'Mi candidato',
                'source' => 'Fotocasa',
                'owner' => 2,
                'created_at' => '2020-09-01 16:16:16',
                'created_by' => 1
            ]
        ]);
    }

    // public function test_post_lead_unauthorized(): void
    // {
    //     $response = $this->post('/api/lead', [
    //         'name' => 'Mi candidato',
    //         'source' => 'Fotocasa',
    //         'owner' => 2
    //     ]);

    //     $response->assertStatus(401);
    //     $response->assertExactJson([
    //         'meta' => [
    //             'success' => false,
    //             'errors' => [
    //                 'Token expired'
    //             ]
    //         ]
    //     ]);
    // }

    public function test_get_lead_ok(): void
    {
        $response = $this->get("/api/lead/1");

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

    // public function test_get_lead_unauthorized(): void
    // {
    //     $response = $this->get("/api/lead/1");

    //     $response->assertStatus(401);
    //     $response->assertExactJson([
    //         'meta' => [
    //             'success' => false,
    //             'errors' => [
    //                 'Token expired'
    //             ]
    //         ]
    //     ]);
    // }

    public function test_get_lead_not_found(): void
    {
        $response = $this->get("/api/lead/2");

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
