<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeadsTest extends TestCase
{
    public function test_get_leads_ok(): void
    {
        $response = $this->get("/api/leads");

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

    // public function test_get_leads_unauthorized(): void
    // {
    //     $response = $this->get("/api/leads");

    //     $response->assertStatus(401);
    //     $response->assertExactJson([
    //         'meta' => [
    //             'success'=> false,
    //             'errors'=> [
    //                 'Token expired'
    //             ]
    //         ],
    //     ]);
    // }
}
