<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DebitCard;
use App\Models\DebitCardTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DebitCardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }

    public function testCustomerCanSeeAListOfDebitCards()
    {
        // get /debit-cards

        DebitCard::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/debit-cards');

        $response->assertStatus(200)
                ->assertJsonCount(1);
    }

    public function testCustomerCannotSeeAListOfDebitCardsOfOtherCustomers()
    {
        // get /debit-cards

        $otherUser = User::factory()->create();
        DebitCard::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/debit-cards');

        $response->assertStatus(200)
                ->assertJsonCount(0);
    }

    public function testCustomerCanCreateADebitCard()
    {
        // post /debit-cards

        $response = $this->postJson('/api/debit-cards', [
            'card_number' => '1234567890123456',
            'expiration_date' => '12/26',
        ]);
    
        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'card_number', 'expiration_date']);
    
        $this->assertDatabaseHas('debit_cards', [
            'card_number' => '1234567890123456',
            'user_id' => $this->user->id
        ]);
    }

    public function testCustomerCanSeeASingleDebitCardDetails()
    {
        // get api/debit-cards/{debitCard}

        $debitCard = DebitCard::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/debit-cards/{$debitCard->id}");

        $response->assertStatus(200)
                ->assertJson(['id' => $debitCard->id]);
    }

    public function testCustomerCannotSeeASingleDebitCardDetails()
    {
        // get api/debit-cards/{debitCard}

        $otherUser = User::factory()->create();
        $debitCard = DebitCard::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/debit-cards/{$debitCard->id}");

        $response->assertStatus(403);
    }

    public function testCustomerCanActivateADebitCard()
    {
        // put api/debit-cards/{debitCard}

        $debitCard = DebitCard::factory()->create(['user_id' => $this->user->id, 'is_active' => false]);

        $response = $this->putJson("/api/debit-cards/{$debitCard->id}", ['is_active' => true]);

        $response->assertStatus(200)
                ->assertJson(['is_active' => true]);

        $this->assertDatabaseHas('debit_cards', [
            'id' => $debitCard->id,
            'is_active' => true
        ]);
    }

    public function testCustomerCanDeactivateADebitCard()
    {
        // put api/debit-cards/{debitCard}

        $debitCard = DebitCard::factory()->create(['user_id' => $this->user->id, 'is_active' => true]);

        $response = $this->putJson("/api/debit-cards/{$debitCard->id}", ['is_active' => false]);

        $response->assertStatus(200)
                ->assertJson(['is_active' => false]);

        $this->assertDatabaseHas('debit_cards', [
            'id' => $debitCard->id,
            'is_active' => false
        ]);
    }

    public function testCustomerCannotUpdateADebitCardWithWrongValidation()
    {
        // put api/debit-cards/{debitCard}

        $debitCard = DebitCard::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/debit-cards/{$debitCard->id}", ['card_number' => 'invalid']);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['card_number']);
    }

    public function testCustomerCanDeleteADebitCard()
    {
        // delete api/debit-cards/{debitCard}

        $debitCard = DebitCard::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/debit-cards/{$debitCard->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('debit_cards', ['id' => $debitCard->id]);
    }

    public function testCustomerCannotDeleteADebitCardWithTransaction()
    {
        // delete api/debit-cards/{debitCard}

        $debitCard = DebitCard::factory()->create(['user_id' => $this->user->id]);
        DebitCardTransaction::factory()->create(['debit_card_id' => $debitCard->id]);

        $response = $this->deleteJson("/api/debit-cards/{$debitCard->id}");

        $response->assertStatus(400);
    }

    // Extra bonus for extra tests :)
}
