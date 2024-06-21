<?php

namespace Database\Factories;

use App\Models\DebitCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DebitCardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DebitCard::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number' => $this->faker->creditCardNumber,
            'type' => $this->faker->creditCardType,
            'expiration_date' => $this->faker->dateTimeBetween('+1 month', '+3 years'),
            'disabled_at' => $this->faker->boolean(20) ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the debit card is active.
     *
     * @return $this
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'disabled_at' => null,
            ];
        });
    }

    /**
     * Indicate that the debit card is expired.
     *
     * @return $this
     */
    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'disabled_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            ];
        });
    }
}
