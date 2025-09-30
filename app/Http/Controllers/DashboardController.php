<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;

class DashboardController extends Controller
{
    public function index()
    {
        // Prendi le ultime 3 spese dove l'utente è coinvolto
        $expenses = Expense::with(['participants', 'payer'])
            ->where(function ($query) {
                $query->whereHas('participants', function ($q) {
                    $q->where('users.id', Auth::id());
                })
                ->orWhere('paid_by', Auth::id());
            })
            ->latest()
            ->take(3)
            ->get();

        // Passa la variabile alla view
        return view('dashboard', compact('expenses'));
    }
}
