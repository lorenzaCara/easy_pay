<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ExpenseParticipant extends Pivot
{
    protected $table = 'expense_participants';

    protected $fillable = [
        'expense_id',
        'user_id',
        'share_amount',
        'status',
    ];

    protected $casts = [
        'share_amount' => 'decimal:2',
    ];
}
