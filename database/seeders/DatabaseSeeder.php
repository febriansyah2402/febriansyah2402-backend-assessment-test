<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UsersSeeder;
use Database\Seeders\DebitCardSeeder;
use Database\Seeders\ScheduledRepaymentSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();
        \App\Models\DebitCard::factory(20)->create();
        $this->call([
            UsersSeeder::class,
            DebitCardSeeder::class,
            DebitCardTransactionsSeeder::class,
        ]);
    }
}
