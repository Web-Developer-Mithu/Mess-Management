<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badge Setup</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-2xl px-4 py-10">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-emerald-600">Mess Manager</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Badge Setup</h1>
            </div>
            <a href="{{ route('dashboard') }}"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back</a>
        </div>

        <x-toast />


        <form method="POST" action="{{ route('mess.settings.update') }}" enctype="multipart/form-data"
            class="space-y-5 rounded-2xl bg-white p-6 shadow">
            @csrf
            @method('PUT')

            @if ($mess?->logo)
                <div>
                    <p class="mb-2 text-sm font-semibold">Current Logo</p>
                    <img src="{{ asset('storage/' . $mess->logo) }}" alt="{{ $mess->name }} logo"
                        class="h-20 w-20 rounded-xl object-cover ring-1 ring-slate-200">
                </div>
            @endif

            <div>
                <label class="mb-1 block text-sm font-semibold">Mess Name</label>
                <input type="text" name="name" value="{{ old('name', $mess?->name ?? '') }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Mess Logo</label>
                <input type="file" name="logo" accept="image/jpeg,image/png,image/webp"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <p class="mt-1 text-xs text-slate-500">JPG, PNG, or WebP, max 2MB.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-3 font-semibold text-white">Save
                    Badge</button>
                <a href="{{ route('dashboard') }}"
                    class="rounded-lg border border-slate-300 px-5 py-3 font-semibold text-slate-700">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>
