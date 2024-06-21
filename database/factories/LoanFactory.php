<?php

namespace Database\Factories;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Loan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'amount' => $this->faker->numberBetween(1000, 10000),
            'terms' => $this->faker->numberBetween(6, 24),
            'outstanding_amount' => $this->faker->numberBetween(0, 5000),
            'currency_code' => 'USD',
            'processed_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => $this->faker->randomElement(['approved', 'pending', 'rejected']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
