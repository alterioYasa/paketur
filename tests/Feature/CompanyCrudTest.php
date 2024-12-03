<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $superAdmin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'Super Admin',
        ]);

        $this->superAdminToken = JWTAuth::fromUser($superAdmin);
    }

    public function test_store()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->superAdminToken,
        ])->postJson('/api/v1/companies', [
            'name' => 'Test Company',
            'email' => 'testcompany@example.com',
            'phone_number' => '081234567890',
        ]);

        $response->assertStatus(201)->assertJson([
            'status' => 'true',
            'message' => 'Data has been saved',
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
            'email' => 'testcompany@example.com',
            'phone_number' => '081234567890'
        ]);
    }

    public function test_show()
    {
        $company = Company::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->superAdminToken,
        ])->getJson('/api/v1/companies/' . $company->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'email' => $company->email,
                    'phone_number' => $company->phone_number,
                ]
            ]);
    }
}
