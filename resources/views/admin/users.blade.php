<x-app-layout>
    <x-slot name="header">Admin – Users</x-slot>

    <div class="p-6">
        <table class="w-full border text-sm">
            <tr class="bg-gray-100">
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Name</th>
                <th class="p-2 border">Email</th>
            </tr>
            @foreach($users as $user)
                <tr>
                    <td class="p-2 border">{{ $user->id }}</td>
                    <td class="p-2 border">{{ $user->name }}</td>
                    <td class="p-2 border">{{ $user->email }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</x-app-layout>
