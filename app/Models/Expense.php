<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//solo il model user estende autheticable
class Expense extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'title',
        'amount_total',
        'paid_by',
        'due_date'
    ];

    /**
     * Spese create da questo utente (quelle che ha pagato).
     */
    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Spese a cui partecipa (anche se non le ha pagate).
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'expense_participants')
            ->using(ExpenseParticipant::class) //Per indicare a Laravel che questa non è una Pivot neutra ma ha un modello dedicato con dati extra 
            ->withPivot('share_amount', 'status'); //dati extra
    }
}
