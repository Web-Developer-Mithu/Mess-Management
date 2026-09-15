<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Mess Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.25em] text-indigo-600">Mess Reports</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Daily, Weekly & Monthly Report</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $from }} to {{ $to }}</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white">Back to Dashboard</a>
        </div>

        <form method="GET" class="mb-6 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-500">Report Type</label>
                    <select name="type" id="report-type"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                        <option value="daily" {{ $type === 'daily' ? 'selected' : '' }}>Date-wise</option>
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
                <button
                    class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-indigo-500">Show
                    Report</button>
            </div>
        </form>

        <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-bold text-slate-500">Total Meals</p>
                <p class="mt-2 text-2xl font-black">{{ number_format($mealCount) }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-bold text-slate-500">Meal Cost</p>
                <p class="mt-2 text-2xl font-black">৳{{ number_format($mealExpense, 2) }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-bold text-slate-500">Meal Rate</p>
                <p class="mt-2 text-2xl font-black">৳{{ number_format($mealRate, 2) }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-bold text-slate-500">Shared Cost</p>
                <p class="mt-2 text-2xl font-black text-orange-700">৳{{ number_format($sharedExpense, 2) }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-bold text-slate-500">Total Deposit</p>
                <p class="mt-2 text-2xl font-black text-emerald-700">৳{{ number_format($totalPayments, 2) }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-200 bg-orange-200 px-5 py-2 text-center">
                <h2 class="text-xl font-black text-slate-900">Meal Cost
                    {{ $type === 'monthly' ? 'Month of ' . \Carbon\Carbon::parse($from)->format('F-Y') : $from . ' to ' . $to }}
                </h2>
                <p class="text-xs font-bold text-slate-700">Member-wise Report</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-max w-full border-collapse text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th
                                class="sticky left-0 z-10 min-w-[190px] border border-slate-300 bg-white px-2 py-2 text-lg font-black text-sky-700">
                                Name</th>
                            @foreach ($dates as $date)
                                <th class="min-w-[34px] border border-slate-300 px-1 py-1 text-center font-bold text-sky-700"
                                    title="{{ $date }}">
                                    {{ \Carbon\Carbon::parse($date)->format('d') }}
                                </th>
                            @endforeach
                            <th
                                class="min-w-[58px] border border-slate-300 bg-sky-50 px-2 py-2 text-center font-bold text-sky-700">
                                Total</th>
                            <th
                                class="min-w-[90px] border border-slate-300 bg-sky-50 px-2 py-2 text-right font-bold text-sky-700">
                                Meal Cost</th>
                            <th
                                class="min-w-[100px] border border-slate-300 bg-sky-50 px-2 py-2 text-right font-bold text-sky-700">
                                Total Deposit</th>
                            <th
                                class="min-w-[120px] border border-slate-300 bg-sky-50 px-2 py-2 text-right font-bold text-sky-700">
                                Net Deposit Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $member)
                            <tr
                                class="hover:bg-orange-50 {{ $member['status'] === 'inactive' ? 'bg-red-50 text-red-700' : '' }}">
                                <td
                                    class="sticky left-0 z-10 border border-slate-300 px-2 py-1 font-bold {{ $member['status'] === 'inactive' ? 'bg-red-50 text-red-700' : 'bg-white text-slate-800' }}">
                                    {{ $member['name'] }}
                                    @if ($member['status'] === 'inactive')
                                        <span
                                            class="ml-1 rounded bg-red-100 px-1.5 py-0.5 text-[10px] font-black uppercase text-red-700">Inactive</span>
                                    @endif
                                </td>
                                @foreach ($dates as $date)
                                    <td
                                        class="border border-slate-300 px-1 py-1 text-center font-semibold {{ ($member['daily_meals'][$date] ?? 0) > 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                        {{ $member['daily_meals'][$date] ?? 0 }}
                                    </td>
                                @endforeach
                                <td
                                    class="border border-slate-300 px-2 py-1 text-center font-bold {{ $member['status'] === 'inactive' ? 'bg-red-100' : 'bg-sky-50' }}">
                                    {{ $member['meals'] }}</td>
                                <td
                                    class="border border-slate-300 px-2 py-1 text-right {{ $member['status'] === 'inactive' ? 'bg-red-100' : 'bg-sky-50' }}">
                                    ৳{{ number_format($member['meal_cost'], 2) }}</td>
                                <td
                                    class="border border-slate-300 px-2 py-1 text-right text-emerald-700 {{ $member['status'] === 'inactive' ? 'bg-red-100' : 'bg-sky-50' }}">
                                    ৳{{ number_format($member['deposit_balance'], 2) }}</td>
                                <td
                                    class="border border-slate-300 px-2 py-1 text-right font-bold {{ $member['status'] === 'inactive' ? 'bg-red-100' : 'bg-sky-50' }}">
                                    ৳{{ number_format($member['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $dates->count() + 5 }}" class="px-5 py-8 text-center text-slate-500">এই
                                    সময়ের কোনো active
                                    member data নেই।</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-100 font-black text-red-600">
                            <td class="sticky left-0 z-10 border border-slate-300 bg-slate-100 px-2 py-1">Total</td>
                            @foreach ($dates as $date)
                                <td class="border border-slate-300 px-1 py-1 text-center">
                                    {{ $members->sum(fn($member) => $member['daily_meals'][$date] ?? 0) }}</td>
                            @endforeach
                            <td class="border border-slate-300 px-2 py-1 text-center">{{ $mealCount }}</td>
                            <td class="border border-slate-300 px-2 py-1 text-right">
                                ৳{{ number_format($mealExpense, 2) }}</td>
                            <td class="border border-slate-300 px-2 py-1 text-right">
                                ৳{{ number_format($members->sum('deposit_balance'), 2) }}</td>
                            <td class="border border-slate-300 px-2 py-1 text-right">
                                ৳{{ number_format($members->sum('balance'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-4 grid gap-0 sm:grid-cols-2">
            <div class="border border-slate-300 bg-orange-200 px-5 py-3 text-center text-xl font-black">Total Meal Cost
            </div>
            <div class="border border-slate-300 bg-sky-200 px-5 py-3 text-center text-2xl font-black">
                ৳{{ number_format($mealExpense, 2) }}</div>
            <div class="border border-slate-300 bg-orange-200 px-5 py-3 text-center text-xl font-black">Total Meal</div>
            <div class="border border-slate-300 bg-sky-200 px-5 py-3 text-center text-2xl font-black text-red-600">
                {{ $mealCount }}</div>
            <div class="border border-slate-300 bg-orange-200 px-5 py-3 text-center text-xl font-black">Per Meal Cost
            </div>
            <div class="border border-slate-300 bg-sky-200 px-5 py-3 text-center text-2xl font-black">
                ৳{{ number_format($mealRate, 2) }}</div>
        </div>

        <div class="mt-4 rounded-2xl border border-indigo-200 bg-indigo-50 p-4 text-sm text-indigo-900">
            <strong>Report Note:</strong> Total Deposit হলো সদস্যের জমা থাকা মোট টাকা। Shared Cost Total Deposit কমায়
            না;
            এটি শুধু Net Deposit Balance থেকে বাদ হয়। Meal Cost meal rate তৈরি করে।
        </div>

        <section class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-orange-200">
            <div class="border-b border-orange-200 bg-orange-100 px-5 py-4">
                <h2 class="text-xl font-black text-orange-950">Shared Cost Report</h2>
                <p class="mt-1 text-sm text-orange-800">Meal Cost-এর বাইরে আলাদা খরচের category ও member-wise
                    allocation</p>
            </div>

            <div class="grid gap-3 p-5 sm:grid-cols-2 lg:grid-cols-4">
                @forelse ($sharedCostByCategory as $category => $categoryTotal)
                    <div class="rounded-xl border border-orange-200 bg-orange-50 p-4">
                        <p class="text-xs font-bold uppercase text-orange-700">{{ $category }}</p>
                        <p class="mt-1 text-xl font-black text-orange-950">৳{{ number_format($categoryTotal, 2) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">এই সময়ের কোনো Shared Cost নেই।</p>
                @endforelse
            </div>

            <div class="overflow-x-auto px-5 pb-5">
                <table class="min-w-[760px] w-full border-collapse text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="border border-slate-200 px-3 py-3">Date</th>
                            <th class="border border-slate-200 px-3 py-3">Type / Category</th>
                            <th class="border border-slate-200 px-3 py-3">Vendor</th>
                            <th class="border border-slate-200 px-3 py-3 text-center">Members</th>
                            <th class="border border-slate-200 px-3 py-3">Member Allocation</th>
                            <th class="border border-slate-200 px-3 py-3 text-right">Total Shared Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sharedCostEntries as $entry)
                            <tr class="hover:bg-orange-50">
                                <td class="border border-slate-200 px-3 py-3">{{ $entry['date'] }}</td>
                                <td class="border border-slate-200 px-3 py-3 font-bold text-orange-800">
                                    {{ $entry['category'] }}</td>
                                <td class="border border-slate-200 px-3 py-3">{{ $entry['vendor'] ?: '—' }}</td>
                                <td class="border border-slate-200 px-3 py-3 text-center">{{ $entry['member_count'] }}
                                </td>
                                <td class="border border-slate-200 px-3 py-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($entry['allocations'] as $memberName => $memberAmount)
                                            <span
                                                class="rounded-full bg-orange-100 px-2 py-1 text-xs font-bold text-orange-900">{{ $memberName }}:
                                                ৳{{ number_format($memberAmount, 2) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="border border-slate-200 px-3 py-3 text-right font-black text-orange-800">
                                    ৳{{ number_format($entry['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-slate-500">কোনো Shared Cost entry
                                    নেই।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-indigo-200">
            <div class="border-b border-indigo-200 bg-indigo-100 px-5 py-4">
                <h2 class="text-xl font-black text-indigo-950">Merged Cost Report</h2>
                <p class="mt-1 text-sm text-indigo-800">Meal Cost এবং Shared Cost একসঙ্গে member-wise হিসাব</p>
            </div>
            <div class="grid gap-3 p-5 sm:grid-cols-3">
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                    <p class="text-xs font-bold text-indigo-700">Combined Total Cost</p>
                    <p class="mt-1 text-xl font-black text-indigo-950">
                        ৳{{ number_format($mealExpense + $sharedExpense, 2) }}</p>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-xs font-bold text-emerald-700">Total Deposit</p>
                    <p class="mt-1 text-xl font-black text-emerald-950">
                        ৳{{ number_format($members->sum('deposit_balance'), 2) }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold text-slate-600">Net Deposit Balance</p>
                    <p class="mt-1 text-xl font-black text-slate-950">
                        ৳{{ number_format($members->sum('balance'), 2) }}</p>
                </div>
            </div>
            <div class="overflow-x-auto px-5 pb-5">
                <table class="min-w-[760px] w-full border-collapse text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="border border-slate-200 px-3 py-3">Member</th>
                            <th class="border border-slate-200 px-3 py-3 text-right">Meal Cost</th>
                            <th class="border border-slate-200 px-3 py-3 text-right">Shared Cost</th>
                            <th class="border border-slate-200 px-3 py-3 text-right">Total Cost</th>
                            <th class="border border-slate-200 px-3 py-3 text-right">Total Deposit</th>
                            <th class="border border-slate-200 px-3 py-3 text-right">Net Deposit Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            <tr
                                class="{{ $member['status'] === 'inactive' ? 'bg-red-50 text-red-700' : 'hover:bg-indigo-50' }}">
                                <td class="border border-slate-200 px-3 py-3 font-bold">
                                    {{ $member['name'] }}
                                    @if ($member['status'] === 'inactive')
                                        <span
                                            class="ml-1 rounded bg-red-100 px-1.5 py-0.5 text-[10px] font-black uppercase">Inactive</span>
                                    @endif
                                </td>
                                <td class="border border-slate-200 px-3 py-3 text-right">
                                    ৳{{ number_format($member['meal_cost'], 2) }}</td>
                                <td class="border border-slate-200 px-3 py-3 text-right text-orange-700">
                                    ৳{{ number_format($member['shared_cost'], 2) }}</td>
                                <td class="border border-slate-200 px-3 py-3 text-right font-black">
                                    ৳{{ number_format($member['meal_cost'] + $member['shared_cost'], 2) }}</td>
                                <td class="border border-slate-200 px-3 py-3 text-right text-emerald-700">
                                    ৳{{ number_format($member['deposit_balance'], 2) }}</td>
                                <td class="border border-slate-200 px-3 py-3 text-right font-black">
                                    ৳{{ number_format($member['balance'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-indigo-100 bg-indigo-50 px-5 py-4 text-sm text-indigo-950">
                <strong>মার্জ রিপোর্ট:</strong> Total Cost = Meal Cost + Shared Cost; Shared Cost meal rate-এ যোগ হয় না,
                কিন্তু Net Deposit Balance থেকে বাদ হয়।
            </div>
        </section>
    </div>
    <script>
        const reportType = document.getElementById('report-type');
        const filters = document.querySelectorAll('[data-filter]');
        reportType.addEventListener('change', () => filters.forEach(filter => filter.classList.toggle('hidden', filter
            .dataset.filter !== reportType.value)));
    </script>
</body>

</html>
