<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DebitCardTransaction;
use App\Models\DebitCard;

class DebitCardTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $debitCards = DebitCard::all();

        foreach ($debitCards as $debitCard) {
            DebitCardTransaction::factory()->count(5)->create(['debit_card_id' => $debitCard->id]);
        }
    }
}
