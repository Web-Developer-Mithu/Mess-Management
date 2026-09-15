<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-7xl px-4 py-10">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-indigo-600">Super Admin</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Activity Log</h1>
            </div>
            <a href="{{ route('superadmin.dashboard') }}"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back to Dashboard</a>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow">
            <div class="overflow-x-auto">
                <table class="min-w-[860px] text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-bold">Time</th>
                            <th class="px-4 py-3 font-bold">User</th>
                            <th class="px-4 py-3 font-bold">Action</th>
                            <th class="px-4 py-3 font-bold">Model</th>
                            <th class="px-4 py-3 font-bold">Details</th>
                            <th class="px-4 py-3 font-bold">IP</th>
                            <th class="px-4 py-3 font-bold">User Agent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-4 py-3 text-slate-700">{{ $log->created_at->format('d M, Y h:i A') }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $log->user?->name ?? 'System' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="rounded-full px-2 py-1 text-xs font-bold
                                    {{ $log->action === 'created' ? 'bg-emerald-100 text-emerald-700' : ($log->action === 'updated' ? 'bg-sky-100 text-sky-700' : ($log->action === 'deleted' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')) }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ class_basename($log->model_type) }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $log->details }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $log->ip_address ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ Str::limit($log->user_agent ?? '—', 50) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-slate-500" colspan="7">No activity recorded
                                    yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</body>

</html>
