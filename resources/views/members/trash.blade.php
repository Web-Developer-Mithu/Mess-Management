<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trash</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <x-toast />
    <div class="mx-auto max-w-5xl px-4 py-10">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-amber-600">Trash</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Deleted Members</h1>
            </div>
            <a href="{{ route('dashboard') }}"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back to Dashboard</a>
        </div>

        @if ($trashedMembers->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <p class="text-lg font-bold text-slate-600">No deleted members in trash.</p>
            </div>
        @else
            <div class="overflow-hidden rounded-2xl bg-white shadow">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-bold">Name</th>
                            <th class="px-4 py-3 font-bold">Phone</th>
                            <th class="px-4 py-3 font-bold">Status</th>
                            <th class="px-4 py-3 font-bold">Deleted At</th>
                            <th class="px-4 py-3 font-bold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($trashedMembers as $member)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $member->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $member->phone ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="rounded-full px-2 py-1 text-xs font-bold {{ $member->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                        {{ ucfirst($member->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $member->deleted_at->format('d M, Y h:i A') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('members.restore', $member->id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-500">
                                            Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>

</html>
