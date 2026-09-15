<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard — Mess Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">
    <div class="flex h-screen overflow-hidden bg-slate-50">
        <!-- Sidebar -->
        <aside
            class="w-72 shrink-0 bg-slate-900 text-slate-200 flex flex-col transition-all duration-300 relative z-20 shadow-2xl">
            <div
                class="absolute inset-0 bg-gradient-to-b from-slate-900 via-slate-900 to-indigo-950/20 pointer-events-none">
            </div>

            <div class="relative z-10 flex flex-col h-full">
                <div class="border-b border-white/10 p-6 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-400">Super Admin</div>
                        <div class="text-xl font-black text-white tracking-tight">Mess Manager</div>
                    </div>
                </div>

                <nav class="flex-1 space-y-1.5 p-4 overflow-y-auto">
                    <a href="{{ route('superadmin.dashboard') }}"
                        class="group flex items-center gap-3 rounded-xl bg-indigo-500/10 border border-indigo-500/20 px-4 py-3.5 font-semibold text-indigo-400 shadow-inner transition-all hover:bg-indigo-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>সকল Mess</span>
                    </a>

                    <a href="{{ route('superadmin.activity.logs') }}"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3.5 font-semibold text-slate-300 transition-all hover:bg-white/5 hover:text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Activity Log</span>
                    </a>

                    <a href="{{ route('superadmin.warnings') }}"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3.5 font-semibold text-slate-300 transition-all hover:bg-white/5 hover:text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z" />
                        </svg>
                        <span>Warning</span>
                    </a>
                </nav>

                <!-- Admin Profile Bottom -->
                <div class="p-4 border-t border-white/10">
                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4 backdrop-blur-sm">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center font-bold text-white text-sm">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="mt-3">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 rounded-xl bg-red-500/10 border border-red-500/20 px-3 py-2 text-xs font-bold text-red-400 hover:bg-red-500/20 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-100 relative">
            <header
                class="min-h-20 flex-shrink-0 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 px-4 py-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-3 sticky top-0 z-10 shadow-sm shadow-slate-200/50">
                <h2 class="text-lg sm:text-xl font-bold text-slate-800">System Overview</h2>
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="px-3 py-1.5 bg-indigo-50 border border-indigo-200 rounded-lg">
                        <span class="text-xs font-bold text-indigo-600">⚡ Super Admin</span>
                    </div>
                    <div class="text-sm font-bold text-slate-700">{{ auth()->user()->name }}</div>
                </div>
            </header>

            <div class="px-6 pt-6 lg:px-8 lg:pt-8">
                <div
                    class="rounded-2xl border border-indigo-200 bg-gradient-to-r from-indigo-50 via-white to-sky-50 px-5 py-4 shadow-sm">
                    <div class="text-sm font-bold text-indigo-700">
                        ⚡ Super Admin হিসেবে <span class="text-slate-900">Md. Mithu Rahman</span> (Smart Soft X InterX)
                        এর ড্যাশবোর্ড দেখছেন
                    </div>
                </div>
            </div>

            <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                @if (session('success'))
                    <div
                        class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-semibold flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div
                        class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 font-semibold flex flex-col gap-2">
                        @foreach ($errors->all() as $err)
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                {{ $err }}
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Header Section -->
                <div
                    class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between bg-white p-6 rounded-3xl shadow-sm border border-slate-200/60 relative overflow-hidden">
                    <div
                        class="absolute right-0 top-0 w-64 h-64 bg-indigo-50 rounded-full blur-3xl -z-10 translate-x-1/2 -translate-y-1/2">
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-indigo-600 mb-1">Management</p>
                        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Active Messes</h1>
                        <p class="text-sm text-slate-500 mt-1">মোট {{ $messes->count() }}টি mess পরিচালনা করুন</p>
                    </div>
                    <a href="{{ route('superadmin.messes.create') }}"
                        class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-500 hover:shadow-indigo-600/50 transition-all hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        নতুন Mess তৈরি করুন
                    </a>
                </div>

                <!-- Stats Bar -->
                <div class="mb-8 grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl">
                            🏢</div>
                        <div>
                            <div class="text-2xl font-black text-slate-900">{{ $messes->count() }}</div>
                            <div class="text-xs font-semibold text-slate-500">মোট Mess</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                            👥</div>
                        <div>
                            <div class="text-2xl font-black text-slate-900">{{ $messes->sum('members_count') }}</div>
                            <div class="text-xs font-semibold text-slate-500">মোট সদস্য</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">
                            👤</div>
                        <div>
                            <div class="text-2xl font-black text-slate-900">{{ $messes->count() }}</div>
                            <div class="text-xs font-semibold text-slate-500">Manager Accounts</div>
                        </div>
                    </div>
                </div>

                <!-- Mess Cards -->
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse($messes as $mess)
                        @php $manager = $mess->users->first(); @endphp
                        <div
                            class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200/60 relative overflow-hidden group hover:border-indigo-300 hover:shadow-md transition-all duration-300">
                            <!-- Decorative blob -->
                            <div
                                class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-50 rounded-full transition-transform group-hover:scale-150 duration-500">
                            </div>

                            <!-- Mess Info -->
                            <div class="flex items-start justify-between mb-5 relative z-10">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xl font-bold text-slate-800 truncate">{{ $mess->name }}</h3>
                                    <p class="text-sm text-slate-500 mt-0.5 truncate">
                                        <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $mess->address ?? 'ঠিকানা দেওয়া হয়নি' }}
                                    </p>
                                </div>
                                @if ($mess->logo)
                                    <img src="{{ asset('storage/' . $mess->logo) }}" alt="{{ $mess->name }} logo"
                                        class="h-12 w-12 rounded-2xl object-cover ring-1 ring-slate-200">
                                @else
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xl flex-shrink-0">
                                        🏢
                                    </div>
                                @endif
                            </div>

                            <!-- Stats -->
                            <div class="flex items-center gap-2 mb-5">
                                <span
                                    class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg flex items-center gap-1">
                                    👥 {{ $mess->members_count }} সদস্য
                                </span>
                                @if ($manager)
                                    <span
                                        class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg flex items-center gap-1">
                                        ✅ Manager আছে
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 bg-red-50 text-red-700 text-xs font-bold rounded-lg flex items-center gap-1">
                                        ⚠️ Manager নেই
                                    </span>
                                @endif
                            </div>

                            <!-- Manager Info -->
                            @if ($manager)
                                <div class="mb-5 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                                        Manager Account</div>
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                            {{ substr($manager->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-bold text-slate-700 truncate">
                                                {{ $manager->name }}</div>
                                            <div class="text-xs text-slate-500 truncate">{{ $manager->email }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('superadmin.messes.edit', $mess) }}"
                                    class="w-full flex items-center justify-center gap-2 rounded-xl bg-amber-50 border border-amber-200 px-4 py-2.5 text-sm font-bold text-amber-700 hover:bg-amber-100 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-8.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 7.5-7.5z" />
                                    </svg>
                                    নাম ও Logo Edit করুন
                                </a>
                                @if ($manager)
                                    <form action="{{ route('superadmin.impersonate', $mess->id) }}" method="POST">
                                        @csrf
                                        <button id="login-mess-{{ $mess->id }}" type="submit"
                                            class="w-full flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-md shadow-indigo-500/20 hover:bg-indigo-500 transition-all">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            ১ ক্লিকে Mess-এ প্রবেশ করুন
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('superadmin.messes.delete', $mess->id) }}" method="POST"
                                    onsubmit="return confirm('সত্যিই কি {{ $mess->name }} এবং সব ডেটা মুছে ফেলতে চান?')">
                                    @csrf
                                    @method('DELETE')
                                    <button id="delete-mess-{{ $mess->id }}" type="submit"
                                        class="w-full flex items-center justify-center gap-2 rounded-xl bg-red-50 border border-red-200 px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-100 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        সম্পূর্ণ Mess Data Delete করুন
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full p-16 text-center rounded-3xl border-2 border-dashed border-slate-300">
                            <div class="text-5xl mb-4">🏢</div>
                            <h3 class="text-lg font-bold text-slate-700">কোনো Mess পাওয়া যায়নি</h3>
                            <p class="text-slate-500 mt-2 mb-6">নতুন একটি Mess তৈরি করে শুরু করুন</p>
                            <a href="{{ route('superadmin.messes.create') }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                নতুন Mess তৈরি করুন
                            </a>
                        </div>
                    @endforelse
                </div>
            </main>
        </div>
    </div>
</body>

</html>
