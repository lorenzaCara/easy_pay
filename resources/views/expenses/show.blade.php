<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dettagli Spesa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Card principale -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                
                <!-- Titolo e ID -->
                <h3 class="text-lg font-semibold text-gray-900">
                    Spesa {{ $expense->id }} - {{ $expense->title ?? '—' }}
                </h3>

                <!-- Info principali in griglia -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-700 text-sm">
                    <div>
                        <span class="font-medium">Importo totale:</span> € {{ number_format($expense->amount_total, 2, ',', '.') }}
                    </div>
                    <div>
                        <span class="font-medium">Creatore:</span> {{ $expense->payer->name ?? 'Utente non disponibile' }}
                    </div>
                    <div>
                        <span class="font-medium">Scadenza:</span> 
                        {{ $expense->due_date ? \Carbon\Carbon::parse($expense->due_date)->format('d/m/Y') : '—' }}
                    </div>
                </div>

                <!-- Partecipanti -->
                <div>
                    <h4 class="text-md font-semibold text-gray-800 mb-2">Partecipanti</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-100 text-gray-700 text-sm">
                            <thead class="bg-gray-50 text-left text-gray-600">
                                <tr>
                                    <th class="px-4 py-2 border-b">Nome</th>
                                    <th class="px-4 py-2 border-b">Quota</th>
                                    <th class="px-4 py-2 border-b">Stato</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expense->participants as $participant)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-2 border-b">{{ $participant->name }}</td>
                                        <td class="px-4 py-2 border-b">€ {{ number_format($participant->pivot->share_amount, 2, ',', '.') }}</td>
                                        <td class="px-4 py-2 border-b">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                {{ $participant->pivot->status === 'paid' 
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-red-100 text-red-700' }}">
                                                {{ ucfirst($participant->pivot->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Azioni -->
                <div class="flex flex-wrap gap-2 mt-4">
                    <a href="{{ route('expenses.edit', $expense->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Modifica
                    </a>

                    <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                onclick="return confirm('Sei sicuro di voler eliminare questa spesa?')">
                            Elimina
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
