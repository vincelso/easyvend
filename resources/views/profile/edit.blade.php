@extends('layouts.app')
@section('title', 'My Profile')
@section('subtitle', 'Manage your account information')

@section('content')
<div class="max-w-2xl space-y-5">

    {{-- Profile card --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center gap-4 mb-6 pb-5 border-b border-gray-100">
            <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white font-black text-xl flex items-center justify-center uppercase flex-shrink-0">
                {{ substr(auth()->user()->name, 0, 2) }}
            </div>
            <div>
                <h2 class="font-bold text-gray-900 text-base">{{ auth()->user()->name }}</h2>
                <p class="text-gray-400 text-sm">{{ auth()->user()->email }}</p>
                <span class="mt-1 inline-block text-xs font-semibold px-2 py-0.5 rounded-full
                    {{ auth()->user()->isAdmin() ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
        </div>

        {{-- Update Name & Email --}}
        <h3 class="font-bold text-gray-800 text-sm mb-4">Account Information</h3>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PATCH')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                        Full Name
                    </label>
                    <input type="text" name="name"
                           value="{{ old('name', auth()->user()->name) }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                        Email Address
                    </label>
                    <input type="email" name="email"
                           value="{{ old('email', auth()->user()->email) }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 text-sm mb-4">Change Password</h3>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                        Current Password
                    </label>
                    <input type="password" name="current_password" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                    @error('current_password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                        New Password
                    </label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                        Confirm New Password
                    </label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                        class="px-5 py-2.5 bg-gray-800 text-white text-sm font-semibold rounded-xl hover:bg-gray-900 transition-all">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    {{-- Delete Account --}}
    <div class="bg-white rounded-2xl border border-red-200 shadow-sm p-6">
        <h3 class="font-bold text-red-600 text-sm mb-1">Danger Zone</h3>
        <p class="text-gray-400 text-xs mb-4">Once your account is deleted, all data will be permanently removed.</p>
        <form method="POST" action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.')">
            @csrf @method('DELETE')
            <div class="mb-3">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                    Confirm your password
                </label>
                <input type="password" name="password" required
                       placeholder="Enter your password to confirm"
                       class="w-full border border-red-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-300 transition-all">
                @error('password', 'userDeletion')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit"
                    class="px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-all">
                Delete Account
            </button>
        </form>
    </div>
</div>
@endsection