<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense List</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <x-toast />

    <div class="mx-auto max-w-7xl px-4 py-10">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-emerald-600">Expense Manager</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Expense List</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('expenses.create') }}"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-500">Add
                    Expense</a>
                <a href="{{ route('dashboard') }}"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back to Dashboard</a>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-bold">Date</th>
                            <th class="px-4 py-3 font-bold">Category</th>
                            <th class="px-4 py-3 font-bold">Type</th>
                            <th class="px-4 py-3 font-bold">Amount</th>
                            <th class="px-4 py-3 font-bold">Vendor</th>
                            <th class="px-4 py-3 font-bold">Members</th>
                            <th class="px-4 py-3 font-bold">Note</th>
                            <th class="px-4 py-3 font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($expenses as $expense)
                            <tr>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ Carbon\Carbon::parse($expense->date)->format('d M, Y') }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $expense->category }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="rounded-full px-2 py-1 text-xs font-bold {{ $expense->type === 'fixed' ? 'bg-violet-100 text-violet-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $expense->type === 'fixed' ? 'Shared Cost' : 'Meal Cost' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-bold text-emerald-700">
                                    ৳{{ number_format((float) $expense->amount, 2) }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $expense->vendor ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    @if (!empty($expense->member_ids))
                                        {{ collect($expense->member_ids)->map(fn($id) => \App\Models\Member::find($id)?->name ?? 'Member #' . $id)->implode(', ') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $expense->note ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('expenses.edit', $expense) }}"
                                            class="rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">Edit</a>
                                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-slate-500">No expenses added yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $expenses->links() }}
        </div>
    </div>
</body>

</html>
