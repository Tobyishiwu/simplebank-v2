<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold">Confirm Transfer</h2>
    </x-slot>

    <div class="max-w-md mx-auto px-4 py-6 space-y-4">

        <div class="bg-white rounded-xl p-4 shadow">
            <p class="text-sm text-gray-500">Recipient Account</p>
            <p class="font-semibold">{{ $toAccount->account_number }}</p>

            <p class="mt-3 text-sm text-gray-500">Amount</p>
            <p class="text-xl font-bold">₦{{ number_format($amount, 2) }}</p>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800">
            Please confirm the details carefully. Transfers cannot be reversed.
        </div>

        <form method="POST" action="{{ route('account.transfer.execute') }}">
            @csrf
            <input type="hidden" name="account_number" value="{{ $toAccount->account_number }}">
            <input type="hidden" name="amount" value="{{ $amount }}">

            <button class="w-full bg-gray-900 text-white py-3 rounded-lg">
                Confirm & Send
            </button>
        </form>

        <a href="{{ route('dashboard') }}"
           class="block text-center text-sm text-gray-500 mt-2">
            Cancel
        </a>

    </div>
</x-app-layout>
