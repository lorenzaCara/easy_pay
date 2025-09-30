<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Expenses') }}
            </h2>
            @include('expenses.partials.create-button')
        </div>
    </x-slot>

    <div class='py-12'>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-12 mt-6">
    
            <!-- 🔹 Sezione spese non pagate / in corso -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Spese in corso / non pagate</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse ($dueExpenses as $expense)
                        @include('expenses.partials.card', ['expense' => $expense])
                    @empty
                        <p class="text-sm text-gray-500 col-span-2">Nessuna spesa in corso.</p>
                    @endforelse
                </div>

                <!-- Paginazione spese in corso -->
                <div class="mt-4">
                    {{ $dueExpenses->appends(['expired_page' => request('expired_page')])->links() }}
                </div>
            </div>
    
            <!-- 🔹 Sezione spese già pagate / concluse -->
            <div class='bg-gray-100'>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Spese concluse / già pagate</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse ($expiredExpenses as $expense)
                        @include('expenses.partials.card', ['expense' => $expense])
                    @empty
                        <p class="text-sm text-gray-500 col-span-2">Nessuna spesa già pagata.</p>
                    @endforelse
                </div>

                <!-- Paginazione spese concluse -->
                <div class="mt-4">
                    {{ $expiredExpenses->appends(['due_page' => request('due_page')])->links() }}
                </div>
            </div>
    
        </div>
    </div>
</x-app-layout>
