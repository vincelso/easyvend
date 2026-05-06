<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="EasyVend" class="h-10 w-auto">
                    </a>
                </div>

                <!-- Desktop Nav Links -->
                <div class="hidden space-x-1 sm:ms-8 sm:flex sm:items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                        Transactions
                    </x-nav-link>

                    <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                        Sales
                    </x-nav-link>

                    @if(auth()->user()->isAdmin())
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                            Products
                        </x-nav-link>
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            Users
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">

                {{-- Notification Bell (Admin only) --}}
                @if(auth()->user()->isAdmin())
                <div class="relative" x-data="{ bellOpen: false }">
                    <button @click="bellOpen = !bellOpen"
                            class="relative p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($totalAlerts > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                            {{ $totalAlerts > 9 ? '9+' : $totalAlerts }}
                        </span>
                        @endif
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="bellOpen"
                        @click.outside="bellOpen = false"
                        x-transition
                        class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-200 z-50 overflow-hidden">

                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <span class="font-bold text-gray-900 text-sm">Notifications</span>
                            @if($totalAlerts > 0)
                            <span class="text-xs bg-red-100 text-red-600 font-semibold px-2 py-0.5 rounded-full">{{ $totalAlerts }} alerts</span>
                            @else
                            <span class="text-xs text-gray-400">All clear</span>
                            @endif
                        </div>

                        <div class="max-h-80 overflow-y-auto">
                            @if($totalAlerts === 0)
                            <div class="px-4 py-6 text-center text-gray-400 text-sm">
                                ✅ No alerts at the moment
                            </div>
                            @endif

                            {{-- Expired --}}
                            @if($alertExpired->count() > 0)
                            <div class="px-4 py-2 bg-red-50 border-b border-red-100">
                                <p class="text-xs font-bold text-red-700 uppercase tracking-wide mb-1">🔴 Expired ({{ $alertExpired->count() }})</p>
                                @foreach($alertExpired as $p)
                                <a href="{{ route('products.index', ['filter' => 'expired']) }}"
                                class="flex items-center justify-between py-1 hover:opacity-75 transition-all">
                                    <span class="text-xs text-red-800 font-medium truncate">{{ $p->name }}</span>
                                    <span class="text-xs text-red-500 flex-shrink-0 ml-2">{{ $p->expiry_date->format('M d') }}</span>
                                </a>
                                @endforeach
                            </div>
                            @endif

                            {{-- Expiring Soon --}}
                            @if($alertExpiringSoon->count() > 0)
                            <div class="px-4 py-2 bg-orange-50 border-b border-orange-100">
                                <p class="text-xs font-bold text-orange-700 uppercase tracking-wide mb-1">⏰ Expiring Soon ({{ $alertExpiringSoon->count() }})</p>
                                @foreach($alertExpiringSoon as $p)
                                <a href="{{ route('products.index', ['filter' => 'expiring']) }}"
                                class="flex items-center justify-between py-1 hover:opacity-75 transition-all">
                                    <span class="text-xs text-orange-800 font-medium truncate">{{ $p->name }}</span>
                                    <span class="text-xs text-orange-500 flex-shrink-0 ml-2">{{ $p->expiry_date->format('M d') }}</span>
                                </a>
                                @endforeach
                            </div>
                            @endif

                            {{-- Out of Stock --}}
                            @if($alertOutOfStock->count() > 0)
                            <div class="px-4 py-2 bg-red-50 border-b border-red-100">
                                <p class="text-xs font-bold text-red-600 uppercase tracking-wide mb-1">🚫 Out of Stock ({{ $alertOutOfStock->count() }})</p>
                                @foreach($alertOutOfStock as $p)
                                <a href="{{ route('products.index', ['filter' => 'out']) }}"
                                class="flex items-center justify-between py-1 hover:opacity-75 transition-all">
                                    <span class="text-xs text-red-800 font-medium truncate">{{ $p->name }}</span>
                                    <span class="text-xs text-red-500 flex-shrink-0 ml-2">0 left</span>
                                </a>
                                @endforeach
                            </div>
                            @endif

                            {{-- Low Stock --}}
                            @if($alertLowStock->count() > 0)
                            <div class="px-4 py-2 bg-amber-50">
                                <p class="text-xs font-bold text-amber-700 uppercase tracking-wide mb-1">⚠ Low Stock ({{ $alertLowStock->count() }})</p>
                                @foreach($alertLowStock as $p)
                                <a href="{{ route('products.index', ['filter' => 'low']) }}"
                                class="flex items-center justify-between py-1 hover:opacity-75 transition-all">
                                    <span class="text-xs text-amber-800 font-medium truncate">{{ $p->name }}</span>
                                    <span class="text-xs text-amber-600 flex-shrink-0 ml-2">{{ $p->stock }} left</span>
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="px-4 py-3 border-t border-gray-100">
                            <a href="{{ route('products.index') }}"
                            class="text-xs text-indigo-600 font-semibold hover:underline">
                                View all products →
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <span class="text-xs text-gray-400 font-medium uppercase tracking-wide">
                    {{ auth()->user()->isAdmin() ? 'Admin' : 'Cashier' }}
                </span>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                Transactions
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                Sales
            </x-responsive-nav-link>
            @if(auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                    Products
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    Users
                </x-responsive-nav-link>
            @endif
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>