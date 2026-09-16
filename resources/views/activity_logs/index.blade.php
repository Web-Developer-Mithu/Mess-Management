<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <x-toast />
    <div class="mx-auto max-w-7xl px-4 py-10">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-indigo-600">Super Admin</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Activity Log</h1>
            </div>
            <a href="{{ route('superadmin.dashboard') }}"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back to Dashboard</a>
        </div>

        <form method="GET" class="mb-6 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="font-black text-slate-900">Audit Filters</h2>
                    <p class="mt-1 text-xs text-slate-500">কোন Mess, user বা member কখন কী করেছে খুঁজুন</p>
                </div>
                <a href="{{ route('superadmin.activity.logs') }}"
                    class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Clear filters</a>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <select name="mess_id" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                    <option value="">All Messes</option>
                    @foreach ($messes as $mess)
                        <option value="{{ $mess->id }}" @selected(request('mess_id') == $mess->id)>{{ $mess->name }}</option>
                    @endforeach
                </select>
                <select name="user_id" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                    <option value="">All Users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}
                            ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                <select name="action" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                    <option value="">All Actions</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst($action) }}</option>
                    @endforeach
                </select>
                <input type="text" name="model" value="{{ request('model') }}" placeholder="Model: Member, Meal..."
                    class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                <input type="date" name="from" value="{{ request('from') }}"
                    class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm" title="From date">
                <input type="date" name="to" value="{{ request('to') }}"
                    class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm" title="To date">
                <input type="search" name="search" value="{{ request('search') }}"
                    placeholder="Search member, IP, details..."
                    class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm lg:col-span-2">
                <button
                    class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-indigo-500">Filter
                    Activity</button>
            </div>
        </form>

        <div class="mb-3 flex items-center justify-between text-sm text-slate-500">
            <span>{{ $logs->total() }} activity records found</span>
            <span>Showing page {{ $logs->currentPage() }}</span>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow">
            <div class="overflow-x-auto">
                <table class="min-w-[1500px] text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-bold">Time</th>
                            <th class="px-4 py-3 font-bold">User / Mess</th>
                            <th class="px-4 py-3 font-bold">Action</th>
                            <th class="px-4 py-3 font-bold">Model</th>
                            <th class="px-4 py-3 font-bold">Details</th>
                            <th class="px-4 py-3 font-bold">Before</th>
                            <th class="px-4 py-3 font-bold">After</th>
                            <th class="px-4 py-3 font-bold">IP Address</th>
                            <th class="px-4 py-3 font-bold">Location</th>
                            <th class="px-4 py-3 font-bold">Device</th>
                            <th class="px-4 py-3 font-bold">Platform</th>
                            <th class="px-4 py-3 font-bold">Browser</th>
                            <th class="px-4 py-3 font-bold">Raw User Agent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $log->created_at->timezone('Asia/Dhaka')->format('d M, Y h:i:s A') }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-800">
                                    <div>{{ $log->user?->name ?? 'System' }}</div>
                                    <div class="text-xs font-normal text-slate-500">{{ $log->user?->email ?? '—' }}
                                    </div>
                                    <div class="text-xs font-normal text-indigo-600">
                                        {{ $log->user?->mess?->name ?? 'Global / Super Admin' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-2">
                                        <span
                                            class="rounded-full px-2 py-1 text-xs font-bold
                                        {{ $log->action === 'created' ? 'bg-emerald-100 text-emerald-700' : ($log->action === 'updated' ? 'bg-sky-100 text-sky-700' : ($log->action === 'deleted' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')) }}">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                        @if ($log->model_type === \App\Models\Expense::class && $log->action === 'deleted')
                                            <form method="POST"
                                                action="{{ route('superadmin.activity.logs.restore', $log) }}">
                                                @csrf
                                                <button type="submit"
                                                    class="rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700 hover:bg-emerald-100">
                                                    Restore Expense
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ class_basename($log->model_type) }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $log->details }}</td>
                                <td class="max-w-sm px-4 py-3 align-top text-xs text-slate-600">
                                    @if ($log->old_values)
                                        <pre class="max-h-32 overflow-auto whitespace-pre-wrap rounded-lg bg-red-50 p-2 text-red-800">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="max-w-sm px-4 py-3 align-top text-xs text-slate-600">
                                    @if ($log->new_values)
                                        <pre class="max-h-32 overflow-auto whitespace-pre-wrap rounded-lg bg-emerald-50 p-2 text-emerald-800">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $log->ip_address ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $log->location ?? 'Unknown' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $log->device_type ?? 'Unknown' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $log->platform ?? 'Unknown' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $log->browser ?? 'Unknown' }}</td>
                                <td class="max-w-xs px-4 py-3 text-xs text-slate-500" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent ?? '—', 80) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-slate-500" colspan="14">No activity recorded
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
