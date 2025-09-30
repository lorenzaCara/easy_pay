<div class="bg-white shadow-sm sm:rounded-lg p-6">
    @if($expenses->isEmpty())
        <div class="text-center text-gray-500">
            <p>Non ci sono spese effettuate.</p>
        </div>
    @else
        <h3 class="text-lg font-semibold mb-4">Ultime spese</h3>
        <ul class="divide-y divide-gray-200">
            @foreach($expenses->take(3) as $expense)
                <li class="py-2 flex justify-between items-center">
                    {{-- Titolo come link --}}
                    <a href="{{ route('expenses.show', $expense->id) }}" class="text-gray-800 hover:text-indigo-600">
                        {{ $expense->title }}
                    </a>
                    <span class="font-medium text-gray-700">
                        € {{ number_format($expense->amount_total, 2, ',', '.') }}
                    </span>
                </li>
            @endforeach
        </ul>
        <div class="mt-4">
            <a href="{{ route('expenses.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                Vedi tutte le spese →
            </a>
        </div>
    @endif
</div>
