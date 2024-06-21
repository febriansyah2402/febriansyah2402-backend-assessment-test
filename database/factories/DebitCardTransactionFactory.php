<?php

namespace Database\Factories;

use App\Models\DebitCard;
use App\Models\DebitCardTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class DebitCardTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DebitCardTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $debitCard = DebitCard::factory()->create();

        return [
            'amount' => $this->faker->randomNumber(5),
            'currency_code' => $this->faker->randomElement(['USD', 'EUR', 'GBP']),
            'debit_card_id' => $debitCard->id,
            'description' => $this->faker->sentence,
            'transaction_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
