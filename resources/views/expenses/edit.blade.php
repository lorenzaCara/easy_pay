<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifica spesa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white sm:rounded-lg shadow-sm">
                <div class="p-6 text-gray-900">

                    {{-- Errori di validazione --}}
                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('expenses.update', $expense->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Titolo -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Titolo
                            </label>
                            <input type="text" name="title" id="title"
                                   value="{{ old('title', $expense->title) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   required>
                        </div>

                        <!-- Importo totale -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">
                                Importo totale
                            </label>
                            <input type="number" step="0.01" name="amount" id="amount"
                                   value="{{ old('amount', $expense->amount_total) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   required>
                        </div>

                        <!-- Data di scadenza -->
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700">
                                Data di scadenza
                            </label>
                            <input type="date" name="due_date" id="due_date"
                                   value="{{ old('due_date', $expense->due_date) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <!-- Partecipanti -->
                        <div>
                            <label for="participants" class="block text-sm font-medium text-gray-700">
                                Partecipanti
                            </label>
                            <select name="participants[]" id="participants" multiple
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ in_array($user->id, $expense->participants->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Tieni premuto Ctrl (o Cmd) per selezionare più utenti</p>
                        </div>

                        <!-- Stato spesa -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Stato
                            </label>
                            <select name="status" id="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="pending" {{ $expense->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $expense->status === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>

                        <!-- Pulsante submit -->
                        <div>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Aggiorna Spesa
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
