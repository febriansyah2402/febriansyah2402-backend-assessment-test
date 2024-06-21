<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DebitCard;
use App\Models\DebitCardTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DebitCardTransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected DebitCard $debitCard;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->debitCard = DebitCard::factory()->create(['user_id' => $this->user->id]);
        Passport::actingAs($this->user);
    }
    
    public function testCustomerCanSeeAListOfDebitCardTransactions()
    {
        // get /debit-card-transactions

        DebitCardTransaction::factory()->create(['debit_card_id' => $this->debitCard->id]);

        $response = $this->getJson('/api/debit-card-transactions');

        $response->assertStatus(200)
                 ->assertJsonCount(1);
    }

    public function testCustomerCannotSeeAListOfDebitCardTransactionsOfOtherCustomerDebitCard()
    {
        // get /debit-card-transactions

        $otherUser = User::factory()->create();
        $otherDebitCard = DebitCard::factory()->create(['user_id' => $otherUser->id]);
        DebitCardTransaction::factory()->create(['debit_card_id' => $otherDebitCard->id]);

        $response = $this->getJson('/api/debit-card-transactions');

        $response->assertStatus(200)
                 ->assertJsonCount(0);
    }

    public function testCustomerCanCreateADebitCardTransaction()
    {
        // post /debit-card-transactions

        $response = $this->postJson('/api/debit-card-transactions', [
            'debit_card_id' => $this->debitCard->id,
            'amount' => 100.50,
            'description' => 'Test Transaction'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'amount', 'description']);

        $this->assertDatabaseHas('debit_card_transactions', [
            'debit_card_id' => $this->debitCard->id,
            'amount' => 100.50
        ]);
    }

    public function testCustomerCannotCreateADebitCardTransactionToOtherCustomerDebitCard()
    {
        // post /debit-card-transactions

        $otherUser = User::factory()->create();
        $otherDebitCard = DebitCard::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->postJson('/api/debit-card-transactions', [
            'debit_card_id' => $otherDebitCard->id,
            'amount' => 100.50,
            'description' => 'Test Transaction'
        ]);

        $response->assertStatus(403);
    }

    public function testCustomerCanSeeADebitCardTransaction()
    {
        // get /debit-card-transactions/{debitCardTransaction}

        $transaction = DebitCardTransaction::factory()->create(['debit_card_id' => $this->debitCard->id]);

        $response = $this->getJson("/api/debit-card-transactions/{$transaction->id}");

        $response->assertStatus(200)
                 ->assertJson(['id' => $transaction->id]);
    }

    public function testCustomerCannotSeeADebitCardTransactionAttachedToOtherCustomerDebitCard()
    {
        // get /debit-card-transactions/{debitCardTransaction}

        $otherUser = User::factory()->create();
        $otherDebitCard = DebitCard::factory()->create(['user_id' => $otherUser->id]);
        $transaction = DebitCardTransaction::factory()->create(['debit_card_id' => $otherDebitCard->id]);

        $response = $this->getJson("/api/debit-card-transactions/{$transaction->id}");

        $response->assertStatus(403);
    }

    // Extra bonus for extra tests :)

    public function testCustomerCannotDeleteADebitCardTransaction()
    {
        // delete /debit-card-transactions/{debitCardTransaction}

        $transaction = DebitCardTransaction::factory()->create(['debit_card_id' => $this->debitCard->id]);

        $response = $this->deleteJson("/api/debit-card-transactions/{$transaction->id}");

        $response->assertStatus(405); // Assuming DELETE is not allowed
    }

    public function testCustomerCannotSeeAListOfTransactionsWhenThereAreNone()
    {
        // get /debit-card-transactions

        $response = $this->getJson('/api/debit-card-transactions');

        $response->assertStatus(200)
                 ->assertJsonCount(0);
    }

    public function testCustomerCannotCreateADebitCardTransactionWithInvalidData()
    {
        // post /debit-card-transactions

        $response = $this->postJson('/api/debit-card-transactions', [
            'debit_card_id' => $this->debitCard->id,
            'amount' => 'invalid_amount',
            'description' => 'Test Transaction'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['amount']);
    }
}
