<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'profile_photo_path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Spese create da questo utente (quelle che ha pagato).
     */
    public function expensesPaid()
    {
        return $this->hasMany(Expense::class, 'paid_by');
    }

    /**
     * Spese a cui partecipa (anche se non le ha pagate).
     */
    public function expenses()
    {
        return $this->belongsToMany(Expense::class, 'expense_participants')
            ->using(ExpenseParticipant::class) //cosi laravel utilizza il mio modello pivot personalizzato e non una classe pivot generica
            ->withPivot('share_amount', 'status');
    }

    /**
     * Foto profilo dell'utente
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}
