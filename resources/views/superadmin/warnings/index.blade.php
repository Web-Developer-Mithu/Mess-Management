<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warnings - Super Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.25em] text-amber-600">Super Admin Only</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Warning Management</h1>
                <p class="mt-1 text-sm text-slate-500">প্রতিটি Mess-এর member এবং total balance warning সেট করুন।</p>
            </div>
            <a href="{{ route('superadmin.dashboard') }}"
                class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white">Back to Dashboard</a>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            @forelse ($messes as $mess)
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-black text-slate-900">{{ $mess->name }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $mess->address ?: 'Address not added' }}</p>
                        </div>
                        <span
                            class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">Warning</span>
                    </div>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs font-bold text-slate-500">Member Threshold</p>
                            <p class="mt-1 font-black">
                                {{ $mess->balance_alert_threshold !== null ? '৳' . number_format((float) $mess->balance_alert_threshold, 2) : 'Not set' }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs font-bold text-slate-500">Total Threshold</p>
                            <p class="mt-1 font-black">
                                {{ $mess->total_balance_warning_threshold !== null ? '৳' . number_format((float) $mess->total_balance_warning_threshold, 2) : 'Not set' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('superadmin.messes.edit', $mess) }}"
                        class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-amber-500 px-4 py-3 text-sm font-bold text-white hover:bg-amber-400">Edit
                        Warning Settings</a>
                </div>
            @empty
                <div class="rounded-2xl bg-white p-8 text-center text-slate-500 md:col-span-2">কোনো Mess নেই।</div>
            @endforelse
        </div>
    </div>
</body>

</html>
