@extends('layouts.dashboard')
@section('title', 'Profile Settings')
@section('page-title', 'Account Settings')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <div class="mb-6" style="opacity:0;animation:fadeSlideUp .4s .05s ease forwards">
        <h2 class="text-xl font-bold" style="color:var(--vc-text);">Profile Settings</h2>
        <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">Manage your profile, password and account</p>
    </div>

    <!-- Profile Information -->
    <div class="vc-card p-6" style="opacity:0;animation:fadeSlideUp .4s .15s ease forwards">
        <h2 class="text-sm font-bold mb-1" style="color:var(--vc-text);">Profile Information</h2>
        <p class="text-xs mb-5" style="color:var(--vc-text-secondary);">Update your display name and email address.</p>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            <div>
                <label for="name" class="block text-xs font-semibold mb-2" style="color:var(--vc-text-secondary);">Full Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" placeholder="Your full name" class="vc-input" />
                @error('name')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold mb-2" style="color:var(--vc-text-secondary);">Email Address</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" placeholder="you@example.com" class="vc-input" />
                @error('email')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <p class="text-xs mt-2" style="color:#F59E0B;">
                        Your email is unverified.
                        <button form="send-verification" class="font-semibold hover:underline" style="color:#F59E0B;">Re-send link</button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="text-xs mt-1" style="color:#10B981;">Verification link sent!</p>
                    @endif
                @endif
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit" class="btn-primary py-2 px-5 text-xs">Save Changes</button>
                @if (session('status') === 'profile-updated')
                    <span x-data="{show:true}" x-show="show" x-transition x-init="setTimeout(()=>show=false,2500)" class="text-xs font-semibold" style="color:#10B981;">✓ Saved</span>
                @endif
            </div>
        </form>
    </div>

    <!-- Change Password -->
    <div class="vc-card p-6" style="opacity:0;animation:fadeSlideUp .4s .25s ease forwards">
        <h2 class="text-sm font-bold mb-1" style="color:var(--vc-text);">Change Password</h2>
        <p class="text-xs mb-5" style="color:var(--vc-text-secondary);">Use a strong, unique password to keep your account secure.</p>

        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label for="current_password" class="block text-xs font-semibold mb-2" style="color:var(--vc-text-secondary);">Current Password</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" placeholder="••••••••" class="vc-input" />
                @error('current_password', 'updatePassword')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold mb-2" style="color:var(--vc-text-secondary);">New Password</label>
                <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Min. 8 characters" class="vc-input" />
                @error('password', 'updatePassword')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold mb-2" style="color:var(--vc-text-secondary);">Confirm New Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Repeat new password" class="vc-input" />
                @error('password_confirmation', 'updatePassword')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit" class="btn-primary py-2 px-5 text-xs">Update Password</button>
                @if (session('status') === 'password-updated')
                    <span x-data="{show:true}" x-show="show" x-transition x-init="setTimeout(()=>show=false,2500)" class="text-xs font-semibold" style="color:#10B981;">✓ Password updated</span>
                @endif
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="rounded-2xl border p-6" style="background:rgba(239,68,68,0.03);border-color:rgba(239,68,68,0.2);opacity:0;animation:fadeSlideUp .4s .35s ease forwards">
        <h2 class="text-sm font-bold mb-1" style="color:#EF4444;">Danger Zone</h2>
        <p class="text-xs mb-5" style="color:var(--vc-text-secondary);">Once deleted, all your data is permanently removed and cannot be recovered.</p>

        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="px-4 py-2 rounded-xl text-xs font-bold text-white transition-all"
                style="background:#EF4444;box-shadow:0 0 15px rgba(239,68,68,0.3);">
            Delete My Account
        </button>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6 transition-colors" style="background:var(--vc-surface);">
                @csrf
                @method('delete')
                <div class="flex items-start gap-4 mb-5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);">
                        <svg class="w-5 h-5" style="color:#EF4444;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold mb-1" style="color:var(--vc-text);">Delete account permanently?</h3>
                        <p class="text-xs" style="color:var(--vc-text-secondary);">All your workspaces, code, and data will be permanently deleted. This cannot be undone. Enter your password to confirm.</p>
                    </div>
                </div>
                <input id="delete_password" name="password" type="password" placeholder="Your current password" class="vc-input mb-2" />
                @error('password', 'userDeletion')<p class="mb-4 text-xs text-red-500">{{ $message }}</p>@enderror
                <div class="flex justify-end gap-3 mt-5">
                    <button type="button" x-on:click="$dispatch('close')" class="btn-ghost py-2 px-4 text-xs">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white transition-all" style="background:#EF4444;">Yes, Delete Account</button>
                </div>
            </form>
        </x-modal>
    </div>

</div>
@endsection
