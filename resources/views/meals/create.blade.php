<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Meal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-2xl px-4 py-10">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-emerald-600">Admin Panel</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Add Meal Entry</h1>
            </div>
            <a href="{{ route('dashboard') }}"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back to Dashboard</a>
        </div>

        <form method="POST" action="{{ route('meals.store') }}" class="space-y-5 rounded-2xl bg-white p-6 shadow">
            @csrf

            <div>
                <label class="mb-1 block text-sm font-semibold">Member</label>
                <select name="member_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="">Select Member</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Date</label>
                <input type="date" name="date" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Meal Count</label>
                <input type="number" name="meal_count" min="0" step="0.01" value="1" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Note</label>
                <textarea name="note" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2"></textarea>
            </div>

            <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-3 font-semibold text-white">Save
                Meal</button>
        </form>
    </div>
</body>

</html>
