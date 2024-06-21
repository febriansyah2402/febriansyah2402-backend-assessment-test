<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\ReceivedRepayment;
use App\Models\ScheduledRepayment;
use App\Models\User;
use Carbon\Carbon;

class LoanService
{
    /**
     * Create a Loan
     *
     * @param  User  $user
     * @param  int  $amount
     * @param  string  $currencyCode
     * @param  int  $terms
     * @param  string  $processedAt
     *
     * @return Loan
     */
    public function createLoan($user, $amount, $currencyCode, $terms, $processedAt): Loan
    {
        $loan = Loan::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'outstanding_amount' => $amount,
            'currency_code' => $currencyCode,
            'terms' => $terms,
            'processed_at' => $processedAt,
            'status' => Loan::STATUS_DUE,
        ]);

        $repaymentAmount = ceil($amount / $terms);
        $dueDate = Carbon::parse($processedAt)->addMonths(1);

        for ($i = 0; $i < $terms; $i++) {
            ScheduledRepayment::create([
                'loan_id' => $loan->id,
                'amount' => $repaymentAmount,
                'outstanding_amount' => $repaymentAmount, // Sesuaikan dengan amount awal
                'currency_code' => $currencyCode,
                'due_date' => $dueDate->toDateString(),
                'status' => ScheduledRepayment::STATUS_DUE,
            ]);

            $dueDate->addMonths(1);
        }

        return $loan;
    }


    /**
     * Repay Scheduled Repayments for a Loan
     *
     * @param  Loan  $loan
     * @param  int  $amount
     * @param  string  $currencyCode
     * @param  string  $receivedAt
     *
     * @return ReceivedRepayment
     */
    public function repayLoan(Loan $loan, int $amount, string $currencyCode, string $receivedAt): ReceivedRepayment
    {
        $scheduledRepayment = $loan->scheduledRepayments()
            ->where('status', ScheduledRepayment::STATUS_DUE)
            ->orderBy('due_date', 'asc')
            ->first();

        if (!$scheduledRepayment) {
            throw new \Exception('No due scheduled repayment found for this loan.');
        }

        $receivedAmount = min($amount, $scheduledRepayment->outstanding_amount);

        $scheduledRepayment->outstanding_amount -= $receivedAmount;
        if ($scheduledRepayment->outstanding_amount <= 0) {
            $scheduledRepayment->status = ScheduledRepayment::STATUS_REPAID;
        } else {
            $scheduledRepayment->status = ScheduledRepayment::STATUS_PARTIAL;
        }
        $scheduledRepayment->save();

        $loan->outstanding_amount -= $receivedAmount;
        if ($loan->outstanding_amount <= 0) {
            $loan->status = Loan::STATUS_REPAID;
        }
        $loan->save();

        $receivedRepayment = ReceivedRepayment::create([
            'loan_id' => $loan->id,
            'amount' => $receivedAmount,
            'currency_code' => $currencyCode,
            'received_at' => $receivedAt,
        ]);

        return $receivedRepayment;
    }
}
