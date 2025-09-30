<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            @include('expenses.partials.create-button')
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Hello, {{ Auth::user()->name }}! You're logged in!
                </div>
            </div>

            {{-- Anteprima spese --}}
            @include('expenses.partials.preview', ['expenses' => $expenses])
        </div>
    </div>
</x-app-layout>
