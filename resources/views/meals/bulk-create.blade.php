<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Meal for Everyone</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-2xl px-4 py-10">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-teal-600">Admin Panel</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Add Meal for Everyone</h1>
                <p class="mt-2 text-sm text-slate-500">শুধু active সদস্যদের জন্য নির্দিষ্ট তারিখে meal যোগ হবে।</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back to Dashboard</a>
        </div>

        <x-toast />


        <form method="POST" action="{{ route('meals.bulk.store') }}" class="space-y-5 rounded-2xl bg-white p-6 shadow">
            @csrf

            <div class="rounded-xl border border-teal-200 bg-teal-50 p-4 text-sm text-teal-900">
                নির্দিষ্ট তারিখে প্রত্যেক active সদস্যের পাশে আলাদা meal count দিন। একই তারিখে আগের entry থাকলে সেটি
                update হবে।
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Date</label>
                <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between gap-3">
                    <label class="text-sm font-semibold">Member-wise Meal Count</label>
                    <span class="text-xs text-slate-500">শুধু active members</span>
                </div>
                <div class="space-y-3">
                    @forelse ($members as $member)
                        <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="min-w-0 flex-1">
                                <div class="truncate font-semibold text-slate-800">{{ $member->name }}</div>
                                <div class="text-xs text-slate-500">{{ $member->phone ?: 'Phone not added' }}</div>
                            </div>
                            <input type="number" name="meal_counts[{{ $member->id }}]" min="0" step="0.01"
                                value="{{ old('meal_counts.' . $member->id, 0) }}" required
                                class="w-28 rounded-lg border border-slate-300 px-3 py-2 text-right">
                        </div>
                    @empty
                        <p class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">কোনো active
                            member নেই।</p>
                    @endforelse
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Note</label>
                <textarea name="note" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('note') }}</textarea>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="rounded-lg bg-teal-600 px-5 py-3 font-semibold text-white">Save for
                    Everyone</button>
                <a href="{{ route('dashboard') }}"
                    class="rounded-lg border border-slate-300 px-5 py-3 font-semibold text-slate-700">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>
