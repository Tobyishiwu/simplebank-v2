<x-app-layout>
    <x-slot name="header">Admin – Transactions</x-slot>

    <div class="p-6">
        <table class="w-full border text-sm">
            <tr class="bg-gray-100">
                <th class="p-2 border">Date</th>
                <th class="p-2 border">User</th>
                <th class="p-2 border">Type</th>
                <th class="p-2 border">Amount</th>
            </tr>
            @foreach($transactions as $tx)
                <tr>
                    <td class="p-2 border">{{ $tx->created_at }}</td>
                    <td class="p-2 border">{{ $tx->account->user->email }}</td>
                    <td class="p-2 border">{{ $tx->type }}</td>
                    <td class="p-2 border">₦{{ number_format($tx->amount,2) }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</x-app-layout>
