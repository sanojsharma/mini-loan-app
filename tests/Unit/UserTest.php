<?php

namespace Tests\Unit;

use Tests\TestCase;
use Database\Seeders\AdminUserSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */

    /* Login tests */

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');
        $this->seed();
    }

    public function test_login_without_email_pass()
    {
        $response = $this->json('POST', '/api/v1/login');
        $response->assertStatus(401);
        $response->assertJson(['success' => 'false']);
    }

    public function test_login_without_pass()
    {
        $payload = ['email' => 'admin@admin.com'];
        $response = $this->json('POST', '/api/v1/login', $payload);
        $response->assertStatus(401);
        $response->assertJson(['success' => 'false']);
    }

    public function test_login_with_email_pass()
    {
        $payload = ['email' => 'admin@admin.com', 'password' => 'admin@123'];
        $this->json('POST', '/api/v1/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'user',
                'token',
            ]);
    }

    /* Login tests ENDS */

    public function test_register_with_name_email()
    {
        $payload = ['email' => 'sam1@gmail.com', 'name' => 'sam4'];
        $response = $this->json('POST', '/api/v1/register', $payload);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'user',
            'token',
        ]);
    }

    public function test_register_with_duplicate_email()
    {
        $payload = ['email' => 'admin@admin.com', 'name' => 'sam4'];
        $response = $this->json('POST', '/api/v1/register', $payload);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'errors',
        ]);
    }
}
