<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-gray-900">Transaction Receipt</h2>
    </x-slot>

    <div class="max-w-md mx-auto px-4 py-6 space-y-6">

        {{-- Receipt Card --}}
        <div class="bg-white rounded-xl shadow p-5 space-y-4">

            <div class="text-center">
                <p class="text-sm text-gray-500">Transaction Type</p>
                <p class="text-lg font-semibold capitalize">
                    {{ str_replace('_', ' ', $transaction->type) }}
                </p>
            </div>

            <div class="border-t pt-4 space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Amount</span>
                    <span class="font-semibold">
                        ₦{{ number_format($transaction->amount, 2) }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">Balance After</span>
                    <span>
                        ₦{{ number_format($transaction->balance_after, 2) }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">Date</span>
                    <span>
                        {{ $transaction->created_at->format('M d, Y H:i') }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">Reference</span>
                    <span class="text-xs font-mono">
                        TX-{{ str_pad($transaction->id, 10, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">Account</span>
                    <span>{{ $account->account_number }}</span>
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs text-gray-600">
            This receipt is system-generated and cannot be modified.
        </div>

        {{-- Actions --}}
        <div class="space-y-2">
            <a href="{{ route('dashboard') }}"
               class="block w-full text-center bg-gray-900 text-white py-3 rounded-lg">
                Back to Home
            </a>

            <button onclick="window.print()"
                    class="block w-full text-center bg-gray-100 text-gray-700 py-3 rounded-lg">
                Print / Save
            </button>
        </div>

    </div>
</x-app-layout>
