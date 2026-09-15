<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Mess</title>
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

                <nav class="flex-1 space-y-1.5 p-4 overflow-y-auto custom-scrollbar">
                    <a href="{{ route('superadmin.dashboard') }}"
                        class="group flex items-center gap-3 rounded-xl hover:bg-white/5 px-4 py-3.5 font-semibold text-slate-400 transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>All Messes</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-100 relative">
            <header
                class="h-20 flex-shrink-0 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 px-8 flex items-center justify-between sticky top-0 z-10 shadow-sm shadow-slate-200/50">
                <h2 class="text-xl font-bold text-slate-800">Create New Mess</h2>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 lg:p-8 custom-scrollbar">
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

                <div class="max-w-3xl mx-auto bg-white p-8 rounded-3xl shadow-sm border border-slate-200/60">
                    <form action="{{ route('superadmin.messes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-8 pb-6 border-b border-slate-100">
                            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <span
                                    class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">1</span>
                                Mess Details
                            </h3>
                            <div class="space-y-4 pl-10">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Mess Name</label>
                                    <input type="text" name="mess_name" required value="{{ old('mess_name') }}"
                                        class="w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 transition-all outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Address
                                        (Optional)</label>
                                    <input type="text" name="mess_address" value="{{ old('mess_address') }}"
                                        class="w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 transition-all outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Mess Logo
                                        (Optional)</label>
                                    <input type="file" name="logo" accept="image/jpeg,image/png,image/webp"
                                        class="w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 transition-all outline-none">
                                    <p class="mt-1 text-xs text-slate-500">JPG, PNG বা WebP, সর্বোচ্চ 2MB</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <span
                                    class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">2</span>
                                Manager Account Details
                            </h3>
                            <div class="space-y-4 pl-10">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Manager Name</label>
                                    <input type="text" name="manager_name" required value="{{ old('manager_name') }}"
                                        class="w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 transition-all outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Manager Email (Used for
                                        Login)</label>
                                    <input type="email" name="manager_email" required
                                        value="{{ old('manager_email') }}"
                                        class="w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 transition-all outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                                    <input type="password" name="manager_password" required
                                        class="w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 transition-all outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                            <a href="{{ route('superadmin.dashboard') }}"
                                class="px-6 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition-all">Cancel</a>
                            <button type="submit"
                                class="px-8 py-3 rounded-xl bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-500 transition-all">Create
                                Mess & Manager</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>

</html>
