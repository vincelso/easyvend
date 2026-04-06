<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyVend — Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex items-center justify-center p-4 font-sans antialiased">

    {{-- Navbar (guest) --}}
    <nav class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-14">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xs">EV</span>
                </div>
                <span class="font-bold text-gray-900">Easy<span class="text-indigo-600">Vend</span></span>
            </a>
            <div class="flex items-center gap-1">
                <a href="{{ route('home') }}"
                   class="px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-all
                          {{ request()->routeIs('home') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                    🏠 Home
                </a>
                
                <a href="{{ route('register') }}"
                   class="px-4 py-1.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-all
                          {{ request()->routeIs('register') ? 'ring-2 ring-indigo-300' : '' }}">
                    Register
                </a>
            </div>
        </div>
    </nav>

    <div class="w-full max-w-md mt-14">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-600 rounded-2xl mb-3 shadow-lg">
                <span class="text-white font-bold text-lg">EV</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Welcome back</h1>
            <p class="text-gray-500 text-sm mt-1">Sign in to your EasyVend account</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            {{-- Session Status --}}
            @if (session('status'))
            <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 px-4 py-3 rounded-xl">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                            Email Address
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50"
                               placeholder="you@email.com">
                        @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                            Password
                        </label>
                        <input type="password" name="password" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50"
                               placeholder="Your password">
                        @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                   class="w-4 h-4 accent-indigo-600">
                            <span class="text-sm text-gray-600">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">
                            Forgot password?
                        </a>
                        @endif
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 bg-indigo-600 text-white font-semibold text-sm rounded-xl hover:bg-indigo-700 transition-all shadow-sm mt-2">
                        Sign In
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-500 mt-5">
                No account yet?
                <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">Create one</a>
            </p>
        </div>
    </div>

</body>
</html>