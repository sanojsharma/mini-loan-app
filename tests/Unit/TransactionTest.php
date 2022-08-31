<?php

namespace Tests\Unit;

use Tests\TestCase;
use Laravel\Passport\Passport;
use App\Models\User;
use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Foundation\Testing\RefreshDatabase;


class TransactionTest extends TestCase
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
        $this->seed(); #Seed Admin user
    }

    public function test_transaction_without_auth()
    {

        $user =  User::factory()->create();
        $loan = Loan::factory()->create(
            [
                'user_id' => $user->id
            ]
        );
        $repayment = LoanRepayment::factory()->create(
            [
                'user_id' => $user->id,
                'loan_id' =>  $loan->id
            ]
        );

        $payload = ['repayment_id' =>$repayment->id,'amount'=>3333];
        $response = $this->json('POST','api/v1/transaction/create', $payload);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }



    public function test_create_transaction_with_auth()
    {
        $user =  User::factory()->create();
        $loan = Loan::factory()->create(
            [
                'user_id' => $user->id
            ]
        );
        $repayment = LoanRepayment::factory()->create(
            [
                'user_id' => $user->id,
                'loan_id' =>  $loan->id
            ]
        );
        Passport::actingAs(
            $user,
        );
        $payload = ['repayment_id' => $repayment->id,'amount'=> $repayment->loan_amount]; # This data if for demo purpose.
        $response = $this->json('POST','api/v1/transaction/create', $payload);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }
}
