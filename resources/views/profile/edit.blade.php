@extends('layouts.dashboard')
@section('title', 'Account Settings')
@section('page-title', 'Account Settings')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="space-y-1">
        <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Account Settings</h2>
        <p class="text-sm text-vl-ink-secondary">Manage your display identity, institutional email, and account security.</p>
    </div>

    {{-- Profile Information --}}
    <x-card title="Profile Information" subtitle="Update your display name and email address." :padding="true">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            <div>
                <label for="name" class="block text-xs font-semibold text-vl-ink-secondary mb-2">Full Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="vl-input">
                @error('name')
                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-vl-ink-secondary mb-2">Email Address</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="vl-input">
                @error('email')
                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                @enderror

                @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <p class="mt-2 text-xs text-vl-gate">
                        Your email address is unverified.
                        <button form="send-verification" class="font-semibold underline ml-1 cursor-pointer">Resend verification email</button>
                    </p>
                    @if(session('status') === 'verification-link-sent')
                        <p class="mt-1 text-xs text-vl-teal">Verification link sent.</p>
                    @endif
                @endif
            </div>

            <div class="pt-2 flex items-center gap-4">
                <button type="submit" class="vl-btn-primary !py-2 !px-5 !text-xs">Save Changes</button>
                @if(session('status') === 'profile-updated')
                    <span x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2500)" class="text-xs font-semibold text-vl-teal">
                        Saved
                    </span>
                @endif
            </div>
        </form>
    </x-card>

    {{-- Change Password --}}
    <x-card title="Change Password" subtitle="Choose a strong password unique to your VisionLab account." :padding="true">
        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label for="current_password" class="block text-xs font-semibold text-vl-ink-secondary mb-2">Current Password</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" placeholder="Enter current password" class="vl-input">
                @error('current_password', 'updatePassword')
                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-vl-ink-secondary mb-2">New Password</label>
                <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Minimum 8 characters" class="vl-input">
                @error('password', 'updatePassword')
                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-vl-ink-secondary mb-2">Confirm New Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Repeat new password" class="vl-input">
                @error('password_confirmation', 'updatePassword')
                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2 flex items-center gap-4">
                <button type="submit" class="vl-btn-primary !py-2 !px-5 !text-xs">Update Password</button>
                @if(session('status') === 'password-updated')
                    <span x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2500)" class="text-xs font-semibold text-vl-teal">
                        Password updated
                    </span>
                @endif
            </div>
        </form>
    </x-card>

    {{-- Danger Zone --}}
    <div class="vl-panel p-6 ring-1 ring-vl-danger/20 space-y-4">
        <div>
            <h3 class="text-sm font-bold text-vl-danger">Danger Zone</h3>
            <p class="text-xs text-vl-ink-secondary mt-1">Permanently delete your account, workspaces, and associated data. This action cannot be reversed.</p>
        </div>

        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="vl-btn-danger !py-2 !px-4 !text-xs"
        >
            Delete My Account
        </button>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-5">
                @csrf
                @method('delete')

                <div class="space-y-1.5">
                    <h3 class="text-base font-bold text-vl-ink">Delete account permanently?</h3>
                    <p class="text-xs leading-relaxed text-vl-ink-secondary">
                        All workspaces, files, and submission history will be permanently deleted. Enter your current password to confirm.
                    </p>
                </div>

                <div>
                    <input id="delete_password" name="password" type="password" placeholder="Current password" class="vl-input">
                    @error('password', 'userDeletion')
                        <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-2.5">
                    <button type="button" x-on:click="$dispatch('close')" class="vl-btn-secondary !py-2 !px-4 !text-xs">Cancel</button>
                    <button type="submit" class="vl-btn-danger !py-2 !px-4 !text-xs">Confirm Deletion</button>
                </div>
            </form>
        </x-modal>
    </div>
</div>
@endsection
