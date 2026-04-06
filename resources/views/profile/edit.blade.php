@extends('layouts.app')
@section('title', 'Profile')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Profile</h1>
    <p class="text-gray-500 text-sm mt-0.5">Manage your account settings</p>
</div>

<div class="space-y-6 max-w-2xl">
    <div class="p-6 bg-white shadow-sm rounded-2xl border border-gray-200">
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="p-6 bg-white shadow-sm rounded-2xl border border-gray-200">
        @include('profile.partials.update-password-form')
    </div>

    <div class="p-6 bg-white shadow-sm rounded-2xl border border-gray-200">
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection