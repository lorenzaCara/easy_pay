<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->id();

        // 🔹 Spese in corso (almeno un partecipante con stato pending)
        $dueExpenses = Expense::with(['participants', 'payer'])
            ->where(function ($query) use ($userId) {
                $query->whereHas('participants', function ($q) use ($userId) {
                    $q->where('users.id', $userId)
                    ->where('expense_participants.status', 'pending');
                })
                ->orWhere('paid_by', $userId);
            })
            ->latest()
            ->paginate(4, ['*'], 'due_page'); // nome pagina unico

        // 🔹 Spese concluse (tutti i partecipanti hanno pagato)
        $expiredExpenses = Expense::with(['participants', 'payer'])
            ->where(function ($query) use ($userId) {
                $query->whereHas('participants', function ($q) use ($userId) {
                    $q->where('users.id', $userId);
                })
                ->orWhere('paid_by', $userId);
            })
            ->whereDoesntHave('participants', function ($q) {
                $q->where('expense_participants.status', 'pending');
            })
            ->latest()
            ->paginate(4, ['*'], 'expired_page'); // nome pagina unico

        return view('expenses.index', compact('dueExpenses', 'expiredExpenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Prendi tutti gli utenti tranne l'autenticato
        $users = User::where('id', '!=', auth()->id())->get();

        return view('expenses.create', compact('users'));
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
            'due_date' => 'nullable|date'
        ]);

        // crea un nuovo record nella tabella expenses
        $expense = Expense::create([
            'title' => $request->title,
            'amount_total' => $request->amount,
            'paid_by' => Auth::id(),
            'due_date' => $request->due_date
        ]);

        $participants = $request->participants;
        
        //serve ad includere chi ha creato la spesa nel calcolo... perchè quando faccio il form non mi includo e quindi non mi calcola come partecipante.
        if (!in_array(Auth::id(), $participants)) {
            $participants[] = Auth::id();
        }

        //calcolo dopo aver aggiunto nel calcolo anche utente autenticato
        $share = $request->amount / count($participants);

        /* foreach($participants as $participantId) {
            $expense->participants()->attach($participantId, [
                'share_amount' => $share,
                'status' => 'pending',
            ]);
        } */

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

        return redirect()->route('expenses.show', $expense->id)->with('success', 'Spesa creata con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::with('participants')->findOrFail($id);
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $expense = Expense::with('participants')->findOrFail($id);
        $users = User::where('id', '!=', auth()->id())->get(); // Mostra tutti gli utenti tranne chi ha creato la spesa

        return view('expenses.edit', compact('expense', 'users'));
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
            'due_date' => 'nullable|date'
        ]);

        $expense = Expense::with('participants')->findOrFail($id);

        $participants = $request->participants;

        if (!in_array(Auth::id(), $participants)) {
            $participants[] = Auth::id();
        }

        // Aggiorna l'importo totale della spesa
        $expense->update([
            'title' => $request->title,
            'amount_total' => $request->amount,
            'due_date' => $request->due_date
        ]);

        // 🔹 Salva stati già esistenti prima del detach
        $existingStatuses = $expense->participants->pluck('pivot.status', 'id');

        // Rimuovi vecchi partecipanti
        $expense->participants()->detach();

        // Calcola la quota per ciascun partecipante
        $share = $request->amount / count($participants);

        foreach ($participants as $participantId) {
            // Recupera lo stato precedente, se esiste
            $status = $existingStatuses[$participantId] ?? 'pending';

            // Se il participant è l’utente autenticato, aggiorna dallo stato del form
            if ($participantId == Auth::id()) {
                $status = $request->status;
            }

            $expense->participants()->attach($participantId, [
                'share_amount' => $share,
                'status' => $status,
            ]);
        }

        return redirect()->route('expenses.show', $expense->id)
            ->with('success', 'Spesa aggiornata e partecipanti ricalcolati!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Expense::destroy($id);
        return redirect()->route('expenses.index')->with('success', 'Spesa rimossa con successo!');
    }
}
