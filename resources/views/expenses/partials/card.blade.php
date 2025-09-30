<div class="mb-6 rounded-lg bg-white shadow-sm p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">{{ $expense->title }}</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('expenses.edit', $expense->id) }}"
               class="text-sm text-indigo-600 hover:underline">
                Edit
            </a>
            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="text-sm text-red-600 hover:underline">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Info principali -->
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm text-gray-700 mb-6">
        <div>
            <dt class="font-medium text-gray-900">Importo totale</dt>
            <dd>€ {{ number_format($expense->amount_total, 2, ',', '.') }}</dd>
        </div>
        <div>
            <dt class="font-medium text-gray-900">Pagato da</dt>
            <dd>{{ $expense->payer->name }}</dd>
        </div>
        <div>
            <dt class="font-medium text-gray-900">Creato il</dt>
            <dd>{{ $expense->created_at->format('d/m/Y H:i') }}</dd>
        </div>
        @if($expense->due_date)
            <div>
                <dt class="font-medium text-gray-900">Scadenza</dt>
                <dd>{{ \Carbon\Carbon::parse($expense->due_date)->format('d/m/Y') }}</dd>
            </div>
        @endif
    </dl>

    <!-- Partecipanti -->
    <h3 class="text-md font-semibold text-gray-900 mb-3">Partecipanti</h3>
    <ul class="space-y-2">
        @foreach ($expense->participants as $participant)
            <li class="flex items-center justify-between py-2 border-b last:border-0">
                <div>
                    <p class="font-medium text-gray-800">{{ $participant->name }}</p>
                    <p class="text-sm text-gray-500">
                        Quota: € {{ number_format($participant->pivot->share_amount, 2, ',', '.') }}
                    </p>
                </div>
                <span class="px-2 py-1 rounded-full text-sm font-medium
                    {{ $participant->pivot->status === 'paid' 
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'}}">
                    {{ ucfirst($participant->pivot->status) }}
                </span>
            </li>
        @endforeach
    </ul>
</div>
