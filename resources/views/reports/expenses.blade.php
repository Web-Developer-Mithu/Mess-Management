<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Report - Mess Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <x-toast />
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.25em] text-orange-600">Editable Report</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Expense Report</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $from }} to {{ $to }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('expenses.create') }}"
                    class="rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-500">Add
                    Expense</a>
                <a href="{{ route('dashboard') }}"
                    class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white">Dashboard</a>
            </div>
        </div>

        <form method="GET" class="mb-6 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6 lg:items-end">
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-500">Report Type</label>
                    <select name="type" id="expense-report-type"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                        <option value="daily" {{ $type === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ $type === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
                <div data-filter="daily" class="{{ $type === 'daily' ? '' : 'hidden' }}">
                    <label class="mb-1 block text-xs font-bold text-slate-500">Date</label>
                    <input type="date" name="date" value="{{ request('date', $from) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                </div>
                <div data-filter="weekly" class="{{ $type === 'weekly' ? '' : 'hidden' }}">
                    <label class="mb-1 block text-xs font-bold text-slate-500">Week Starting</label>
                    <input type="date" name="week_start" value="{{ request('week_start', $from) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                </div>
                <div data-filter="monthly" class="{{ $type === 'monthly' ? '' : 'hidden' }}">
                    <label class="mb-1 block text-xs font-bold text-slate-500">Month</label>
                    <input type="month" name="month" value="{{ request('month', substr($from, 0, 7)) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-500">Expense Type</label>
                    <select name="expense_type" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                        <option value="all" {{ $expenseType === 'all' ? 'selected' : '' }}>All Expenses</option>
                        <option value="meal" {{ $expenseType === 'meal' ? 'selected' : '' }}>Meal Cost</option>
                        <option value="fixed" {{ $expenseType === 'fixed' ? 'selected' : '' }}>Shared Cost</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-500">Member</label>
                    <select name="member_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                        <option value="">All Members</option>
                        @foreach ($memberOptions as $memberOption)
                            <option value="{{ $memberOption->id }}"
                                {{ (string) $memberId === (string) $memberOption->id ? 'selected' : '' }}>
                                {{ $memberOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button
                    class="rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-500">Show
                    Report</button>
            </div>
        </form>

        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-bold text-slate-500">Total Expense</p>
                <p class="mt-2 text-2xl font-black text-orange-700">৳{{ number_format($totalAmount, 2) }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-bold text-slate-500">Meal Cost</p>
                <p class="mt-2 text-2xl font-black">৳{{ number_format($mealAmount, 2) }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-bold text-slate-500">Shared Cost</p>
                <p class="mt-2 text-2xl font-black text-violet-700">৳{{ number_format($sharedAmount, 2) }}</p>
            </div>
        </div>

        <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-orange-200">
            <div class="border-b border-orange-200 bg-orange-100 px-5 py-4">
                <h2 class="text-xl font-black text-orange-950">All Expense Entries</h2>
                <p class="mt-1 text-sm text-orange-800">প্রতিটি খরচ এখান থেকে Edit বা Delete করা যাবে।</p>
            </div>
            <div class="overflow-x-auto p-5">
                <table class="min-w-[900px] w-full border-collapse text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="border border-slate-200 px-3 py-3">Date</th>
                            <th class="border border-slate-200 px-3 py-3">Category</th>
                            <th class="border border-slate-200 px-3 py-3">Type</th>
                            <th class="border border-slate-200 px-3 py-3">Vendor</th>
                            <th class="border border-slate-200 px-3 py-3">Members</th>
                            <th class="border border-slate-200 px-3 py-3">Note</th>
                            <th class="border border-slate-200 px-3 py-3 text-right">Amount</th>
                            <th class="border border-slate-200 px-3 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr class="hover:bg-orange-50">
                                <td class="border border-slate-200 px-3 py-3">{{ $expense->date }}</td>
                                <td class="border border-slate-200 px-3 py-3 font-bold">{{ $expense->category }}</td>
                                <td class="border border-slate-200 px-3 py-3"><span
                                        class="rounded-full px-2 py-1 text-[10px] font-black uppercase {{ $expense->type === 'fixed' ? 'bg-violet-100 text-violet-700' : 'bg-amber-100 text-amber-700' }}">{{ $expense->type === 'fixed' ? 'Shared Cost' : 'Meal Cost' }}</span>
                                </td>
                                <td class="border border-slate-200 px-3 py-3">{{ $expense->vendor ?: '—' }}</td>
                                <td class="border border-slate-200 px-3 py-3">
                                    {{ collect($expense->member_ids ?? [])->map(fn($id) => $memberOptions->firstWhere('id', (int) $id)?->name ?? 'Member #' . $id)->implode(', ') ?:'All' }}
                                </td>
                                <td class="border border-slate-200 px-3 py-3">{{ $expense->note ?: '—' }}</td>
                                <td class="border border-slate-200 px-3 py-3 text-right font-black">
                                    ৳{{ number_format((float) $expense->amount, 2) }}</td>
                                <td class="border border-slate-200 px-3 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('expenses.edit', ['expense' => $expense, 'return_to' => $returnUrl]) }}"
                                            class="rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">Edit</a>
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                            @csrf @method('DELETE')<input type="hidden" name="return_to"
                                                value="{{ $returnUrl }}"><button type="submit"
                                                class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-10 text-center text-slate-500">এই সময়ের কোনো খরচ
                                    পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <script>
        const reportType = document.getElementById('expense-report-type');
        const filters = document.querySelectorAll('[data-filter]');
        reportType.addEventListener('change', () => filters.forEach(filter => filter.classList.toggle('hidden', filter
            .dataset.filter !== reportType.value)));
    </script>
</body>

</html>
