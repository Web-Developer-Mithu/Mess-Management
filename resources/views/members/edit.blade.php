<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <x-toast />
    <div class="mx-auto max-w-2xl px-4 py-10">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-emerald-600">Admin Panel</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Edit Member</h1>
            </div>
            <a href="{{ route('dashboard') }}"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back to Dashboard</a>
        </div>

        <form method="POST" action="{{ route('members.update', $member) }}"
            class="space-y-5 rounded-2xl bg-white p-6 shadow">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-1 block text-sm font-semibold">Name</label>
                <input type="text" name="name" value="{{ old('name', $member->name) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $member->phone) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Status</label>
                <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="active" {{ old('status', $member->status) === 'active' ? 'selected' : '' }}>Active
                    </option>
                    <option value="inactive" {{ old('status', $member->status) === 'inactive' ? 'selected' : '' }}>
                        Inactive</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Join Date</label>
                <input type="date" name="join_date" value="{{ old('join_date', $member->join_date) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div class="flex gap-3">
                <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-3 font-semibold text-white">Update
                    Member</button>
                <a href="{{ route('dashboard') }}"
                    class="rounded-lg border border-slate-300 px-5 py-3 font-semibold text-slate-700">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>
