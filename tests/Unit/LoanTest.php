<?php

namespace Tests\Unit;

use Tests\TestCase;
use Laravel\Passport\Passport;
use App\Models\User;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');
        $this->seed(); # Seed Admin User
    }

    public function test_loan_without_auth()
    {
        $payload = ['loan_amount' => 10000, 'loan_term' => 3];
        $response = $this->json('POST', 'api/v1/loans/create', $payload);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_create_loan_with_auth()
    {
        Passport::actingAs(
            User::factory()->create(),
        );
        $payload = ['loan_amount' => 10000, 'loan_term' => 3];
        $response = $this->json('POST', 'api/v1/loans/create', $payload);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }

    public function test_approve_loan_without_admin_permission()
    {
        $user =  User::factory()->create();
        $loan = Loan::factory()->create(
            [
                'user_id' => $user->id
            ]
        );
        Passport::actingAs(
            $user,
        );

        $payload = ['user_id' => $user->id, 'loan_id' => $loan->id];
        $response = $this->json('POST', 'api/v1/admin/approve', $payload);
        $response->assertStatus(403);
        $response->assertJson(['message' => 'Invalid scope(s) provided.']);
    }

    public function test_approve_loan_with_admin_permission()
    {
        $user =  User::factory()->create();
        Passport::actingAs(
            $user,
            ['approve-loan'] # Admin Permission policy
        );
        $user_customer =  User::factory()->create();
        $loan = Loan::factory()->create(['user_id' => $user_customer->id]);
        $payload = ['user_id' => $user_customer->id, 'loan_id' => $loan->id];
        $response = $this->json('POST', 'api/v1/admin/approve', $payload);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }

    public function test_get_loan_details_with_auth()
    {
        $user = User::factory()->create();
        Passport::actingAs(
            $user,
        );
        $loan =  Loan::factory(Loan::class)->create([
            'user_id' => $user->id,
        ]);
        $response = $this->json('POST', 'api/v1/loans');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }
}
