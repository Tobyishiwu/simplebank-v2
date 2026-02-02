<x-app-layout>
    <x-slot name="header">Admin – Accounts</x-slot>

    <div class="p-6">
        <table class="w-full border text-sm">
            <tr class="bg-gray-100">
                <th class="p-2 border">Account #</th>
                <th class="p-2 border">Owner</th>
                <th class="p-2 border">Balance</th>
            </tr>
            @foreach($accounts as $account)
                <tr>
                    <td class="p-2 border">{{ $account->account_number }}</td>
                    <td class="p-2 border">{{ $account->user->email }}</td>
                    <td class="p-2 border">₦{{ number_format($account->balance,2) }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</x-app-layout>
