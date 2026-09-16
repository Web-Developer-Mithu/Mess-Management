<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isAdminReset ? 'Reset Manager Password' : 'Change Password' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-xl px-4 py-10">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.25em] text-indigo-600">Security</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">
                    {{ $isAdminReset ? 'Reset Manager Password' : 'Change Password' }}</h1>
                @if ($isAdminReset)
                    <p class="mt-1 text-sm text-slate-500">{{ $targetUser->name }} · {{ $targetUser->email }}</p>
                @endif
            </div>
            <a href="{{ $isAdminReset ? route('superadmin.dashboard') : ($targetUser->isSuperAdmin() ? route('superadmin.dashboard') : route('dashboard')) }}"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back</a>
        </div>

        <x-toast />


        <form method="POST"
            action="{{ $isAdminReset ? route('superadmin.users.password.update', $targetUser) : route('account.password.update') }}"
            class="space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            @csrf
            @if ($isAdminReset)
                @method('PUT')
            @endif

            @unless ($isAdminReset)
                <div>
                    <label class="mb-1 block text-sm font-semibold">Current Password</label>
                    <input type="password" name="current_password" required autocomplete="current-password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
            @endunless

            <div>
                <label class="mb-1 block text-sm font-semibold">New Password</label>
                <input type="password" name="password" required minlength="8" autocomplete="new-password"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Confirm New Password</label>
                <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-500">
                {{ $isAdminReset ? 'Reset Manager Password' : 'Update Password' }}
            </button>
        </form>
    </div>
</body>

</html>
