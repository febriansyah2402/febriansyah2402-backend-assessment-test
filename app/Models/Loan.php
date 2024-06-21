<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    const CURRENCY_VND = 'VND';
    const STATUS_DUE = 'due';
    const STATUS_REPAID = 'repaid';
    const CURRENCY_SGD = 'SGD';


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'amount', 'terms', 'outstanding_amount', 'currency_code', 'processed_at', 'status',
    ];

    protected $casts = [
        'processed_at' => 'date',
    ];

    /**
     * A Loan belongs to a User
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A Loan has many Scheduled Repayments
     *
     * @return HasMany
     */
    public function scheduledRepayments()
    {
        return $this->hasMany(ScheduledRepayment::class);
    }

    /**
     * A Loan has many Scheduled receive
     *
     * @return HasMany
     */
    public function receivedRepayments()
    {
        return $this->hasMany(ReceivedRepayment::class);
    }
}
