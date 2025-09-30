<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Tutte le spese in cui l'utente è coinvolto (come partecipante o pagatore)
        $userExpenses = Expense::with(['participants', 'payer'])
            ->where(function ($query) {
                $query->whereHas('participants', function ($q) {
                    $q->where('users.id', auth()->id());
                })
                ->orWhere('paid_by', auth()->id());
            })
            ->latest()
            ->get();

        // Dividi tra due e expired
        $dueExpenses = $userExpenses->filter(function ($expense) {
            // Almeno un partecipante in pending = ancora da pagare
            return $expense->participants->contains(function ($p) {
                return $p->pivot->status === 'pending';
            });
        });

        $expiredExpenses = $userExpenses->filter(function ($expense) {
            // Tutti i partecipanti hanno status = paid
            return $expense->participants->every(function ($p) {
                return $p->pivot->status === 'paid';
            });
        });

        return response()->json([
            'due_expenses' => $dueExpenses->values(),
            'expired_expenses' => $expiredExpenses->values(),
            'message' => 'Spese recuperate con successo',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //per controllare che i dati inviati siano validi prima di inviarli
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'participants' => 'required|array',
            'participants.*' => 'exists:users,id',
            'status' => 'required|in:pending,paid',
        ]);

        // crea un nuovo record nella tabella expenses
        $expense = Expense::create([
            'title' => $request->title,
            'amount_total' => $request->amount,
            'paid_by' => Auth::id(),
        ]);

        $participants = $request->participants;
        
        //serve ad includere chi ha creato la spesa nel calcolo... perchè quando faccio il form non mi includo e quindi non mi calcola come partecipante.
        if (!in_array(Auth::id(), $participants)) {
            $participants[] = Auth::id();
        }

        //calcolo dopo aver aggiunto nel calcolo anche utente autenticato
        $share = $request->amount / count($participants);

        foreach($participants as $participantId) {
            $expense->participants()->attach($participantId, [
                'share_amount' => $share,
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'data' => $expense->load('participants'), //carico anche la relazione con i partecipanti
            'message' => 'Spesa creata con successo!'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::with('participants')->findOrFail($id);
        return response()->json([
            'data' => $expense,
            'message' => 'Spesa recuperata con successo'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'participants' => 'required|array',
            'participants.*' => 'exists:users,id',
            'status' => 'required|in:pending,paid',
        ]);

        $expense = Expense::findOrFail($id);

        $participants = $request->participants;

        if (!in_array(Auth::id(), $participants)) {
            $participants[] = Auth::id();
        }

        // Aggiorna l'importo totale della spesa
        $expense->update([
            'title' => $request->title,
            'amount_total' => $request->amount,
        ]);

        // Rimuovi vecchi partecipanti
        $expense->participants()->detach();

        // Calcola la quota per ciascun partecipante
        $share = $request->amount / count($participants);

        foreach ($participants as $participantId) {
            $status = 'pending';

            // Se il participant è l’utente autenticato, prendi lo status dal form
            if ($participantId == Auth::id()) {
                $status = $request->status;
            }

            $expense->participants()->attach($participantId, [
                'share_amount' => $share,
                'status' => $status,
            ]);
        }

        return response()->json([
            'data' => $expense,
            'message' => 'Spesa modificata con successo!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id); // Recupera prima la spesa
        $expense->delete();

        return response()->json([
            'data' => $expense, //opzionale serve per ritornare i dati del record che viene eliminato
            'message' => 'Spesa correttamente eliminata'
        ], 200);
    }
}
