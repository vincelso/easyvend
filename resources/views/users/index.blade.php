@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
    <p class="text-gray-500 text-sm mt-0.5">{{ $users->count() }} registered user(s) in the system.</p>
</div>

{{-- Admin-only notice --}}
<div class="mb-5 flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm font-medium">
    🔐 This page is restricted to <strong class="mx-1">Administrators</strong> only.
</div>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Transactions</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Joined</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center uppercase flex-shrink-0">
                                {{ substr($user->name, 0, 2) }}
                            </div>
                            <span class="font-semibold text-gray-800">
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                <span class="text-xs text-gray-400 font-normal">(you)</span>
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $user->email }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $user->role === 'admin' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 font-semibold text-gray-700">{{ $user->transactions_count }}</td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3">
                        @if($user->id !== auth()->id())
                        <div class="flex items-center gap-2">
                            {{-- Role Toggle --}}
                            <form method="POST" action="{{ route('users.updateRole', $user) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="role"
                                       value="{{ $user->role === 'admin' ? 'cashier' : 'admin' }}">
                                <button type="submit"
                                        class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-lg hover:bg-indigo-100 transition-all">
                                    Make {{ $user->role === 'admin' ? 'Cashier' : 'Admin' }}
                                </button>
                            </form>
                            {{-- Delete --}}
                            <form method="POST" action="{{ route('users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete {{ $user->name }}? All their transactions will also be removed.')">
                                @csrf @method('DELETE')
                                <button class="px-2.5 py-1 bg-red-50 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-100 transition-all">
                                    Delete
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="text-xs text-gray-300 italic">Cannot modify self</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection