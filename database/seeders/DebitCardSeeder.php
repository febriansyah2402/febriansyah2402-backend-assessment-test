<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DebitCard;
use App\Models\User;

class DebitCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            DebitCard::factory()->count(2)->create(['user_id' => $user->id]);
        }
    }
}
