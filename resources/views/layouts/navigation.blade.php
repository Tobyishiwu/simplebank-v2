{{-- DESKTOP NAVIGATION --}}
<nav class="hidden sm:block bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Left --}}
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}">
                    <x-application-logo class="block h-8 w-auto fill-current text-gray-800" />
                </a>

                <div class="ml-10 flex space-x-8">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    @if(auth()->user()->is_admin)
                        <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.*')">
                            Admin
                        </x-nav-link>
                    @endif
                </div>
            </div>

            {{-- Right --}}
            <div class="flex items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-600 hover:text-gray-800">
                            {{ Auth::user()->name }}
                            <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Log Out
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

        </div>
    </div>
</nav>

{{-- MOBILE BOTTOM NAVIGATION --}}
<nav class="fixed bottom-0 inset-x-0 bg-white border-t sm:hidden z-50">
    <div class="grid grid-cols-4 text-center text-xs">

        <a href="{{ route('dashboard') }}"
           class="py-3 {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
            🏠<br>Home
        </a>

        <a href="{{ route('dashboard') }}#transfer"
           class="py-3 text-gray-500">
            🔁<br>Send
        </a>

        <a href="{{ route('dashboard') }}#withdraw"
           class="py-3 text-gray-500">
            ➖<br>Withdraw
        </a>

        <a href="{{ route('profile.edit') }}"
           class="py-3 {{ request()->routeIs('profile.*') ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
            👤<br>Profile
        </a>
    </div>
</nav>
