<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Food Menu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <x-toast />
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.25em] text-emerald-600">Mess Manager</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Weekly Food Menu</h1>
                <p class="mt-1 text-sm text-slate-500">সপ্তাহের প্রতিদিন কী খাবার হবে সেটি লিখে Save করুন।</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white">Dashboard</a>
        </div>

        <form method="POST" action="{{ route('menus.weekly.update') }}"
            class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-7">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                @foreach ($days as $weekday => $day)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <h2 class="mb-3 font-black text-slate-800">{{ $day }}</h2>
                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label for="menu-{{ $weekday }}"
                                    class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">খাবারের
                                    মেনু</label>
                                <textarea id="menu-{{ $weekday }}" name="menus[{{ $weekday }}]" rows="2" maxlength="2000"
                                    placeholder="যেমন: ভাত, ডাল, মাছ, সবজি"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">{{ old('menus.' . $weekday, $menus[$weekday]->menu ?? '') }}</textarea>
                            </div>
                            <div>
                                <label for="market-person-{{ $weekday }}"
                                    class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">কে বাজার
                                    করবে</label>
                                <input id="market-person-{{ $weekday }}" type="text"
                                    name="market_persons[{{ $weekday }}]" maxlength="255"
                                    value="{{ old('market_persons.' . $weekday, $menus[$weekday]->market_person ?? '') }}"
                                    placeholder="যেমন: রহিম"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                <label for="market-condition-{{ $weekday }}"
                                    class="mb-1 mt-3 block text-xs font-bold uppercase tracking-wide text-slate-500">বাজার
                                    না করলে শর্ত</label>
                                <textarea id="market-condition-{{ $weekday }}" name="market_conditions[{{ $weekday }}]" rows="2"
                                    maxlength="2000" placeholder="যেমন: জরিমানা ১০০ টাকা / পরের দিন বাজার"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">{{ old('market_conditions.' . $weekday, $menus[$weekday]->market_condition ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="submit"
                class="mt-6 rounded-xl bg-emerald-600 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-emerald-500">Save
                Weekly Menu</button>
        </form>
    </div>
</body>

</html>
