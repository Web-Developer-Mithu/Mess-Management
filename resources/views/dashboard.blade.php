<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f766e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" href="{{ asset('icons/mess-manager.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('icons/mess-manager.svg') }}">
    <title>Mess Manager Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .mess-live-scene {
            position: relative;
            min-height: 190px;
            overflow: hidden;
            isolation: isolate;
            background: linear-gradient(160deg, #fff7ed, #ffedd5 55%, #fed7aa);
        }

        .mess-live-scene::after {
            content: '';
            position: absolute;
            inset: auto 0 0;
            height: 34%;
            background: repeating-linear-gradient(90deg, rgba(120, 53, 15, .08) 0 2px, transparent 2px 42px);
            transform: skewY(-2deg);
            z-index: -1;
        }

        .scene-table {
            position: absolute;
            left: 28%;
            right: 8%;
            bottom: 28px;
            height: 18px;
            border-radius: 999px;
            background: #92400e;
            box-shadow: 0 18px 0 -5px #78350f;
        }

        .scene-diner {
            position: absolute;
            bottom: 47px;
            font-size: 42px;
            filter: drop-shadow(0 7px 3px rgba(120, 53, 15, .22));
            animation: diner-breathe 2.6s ease-in-out infinite;
        }

        .scene-diner.one {
            left: 42%;
        }

        .scene-diner.two {
            left: 62%;
            animation-delay: .7s;
        }

        .scene-diner.three {
            right: 8%;
            animation-delay: 1.3s;
        }

        .scene-bowl {
            position: absolute;
            left: 53%;
            bottom: 45px;
            width: 35px;
            height: 15px;
            border-radius: 0 0 20px 20px;
            background: #f8fafc;
            border: 2px solid #cbd5e1;
        }

        .scene-cook {
            position: absolute;
            left: 8%;
            bottom: 34px;
            font-size: 58px;
            filter: drop-shadow(0 8px 3px rgba(120, 53, 15, .25));
            animation: cook-breathe 2.8s ease-in-out infinite;
        }

        .scene-pot {
            position: absolute;
            left: 18%;
            bottom: 43px;
            width: 76px;
            height: 34px;
            border-radius: 0 0 30px 30px;
            background: linear-gradient(#64748b, #1e293b);
            border: 2px solid #94a3b8;
        }

        .scene-pot::before {
            content: '';
            position: absolute;
            left: -7px;
            right: -7px;
            top: -8px;
            height: 12px;
            border-radius: 50%;
            background: #334155;
            border: 2px solid #94a3b8;
        }

        .scene-fire {
            position: absolute;
            left: 24%;
            bottom: 22px;
            width: 38px;
            height: 34px;
            border-radius: 50% 50% 45% 45%;
            background: radial-gradient(circle at 50% 80%, #fef08a 0 15%, #fb923c 35%, #ef4444 65%, transparent 70%);
            animation: fire-flicker .55s infinite alternate;
        }

        .scene-steam {
            position: absolute;
            left: 24%;
            bottom: 83px;
            width: 18px;
            height: 55px;
            border-left: 4px solid rgba(255, 255, 255, .65);
            border-radius: 50%;
            filter: blur(2px);
            animation: scene-steam 2.8s infinite ease-out;
        }

        .scene-spoon {
            position: absolute;
            left: 23%;
            bottom: 72px;
            width: 5px;
            height: 52px;
            border-radius: 5px;
            background: #e2e8f0;
            transform-origin: bottom;
            animation: spoon-stir 1.8s infinite ease-in-out;
        }

        @keyframes diner-breathe {
            50% {
                transform: translateY(-3px) rotate(1deg);
            }
        }

        @keyframes cook-breathe {
            50% {
                transform: translateY(-4px) rotate(2deg);
            }
        }

        @keyframes fire-flicker {
            to {
                transform: scale(.8, 1.15) rotate(4deg);
            }
        }

        @keyframes scene-steam {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }

            35% {
                opacity: .8;
            }

            100% {
                opacity: 0;
                transform: translate(12px, -55px) scale(1.5);
            }
        }

        @keyframes spoon-stir {
            50% {
                transform: rotate(-28deg);
            }
        }

        .warning-pulse {
            animation: warning-pulse 1.8s ease-in-out infinite;
        }

        @keyframes warning-pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, .22);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
            }
        }

        @media (max-width: 640px) {
            .scene-cook {
                left: 3%;
                font-size: 44px;
            }

            .scene-pot {
                left: 13%;
            }

            .scene-table {
                left: 24%;
            }

            .scene-diner {
                font-size: 32px;
            }

            .scene-diner.one {
                left: 38%;
            }

            .scene-diner.two {
                left: 58%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .mess-live-scene * {
                animation: none;
            }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">

    <x-toast />

    @if (session('impersonation_popup'))
        <div id="impersonation-popup"
            class="fixed inset-0 z-[99990] flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
            <div class="relative w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-white/60">
                <div class="bg-gradient-to-br from-amber-500 via-orange-500 to-rose-500 px-6 pb-8 pt-7 text-white">
                    <button type="button" id="close-impersonation-popup" aria-label="Close popup"
                        class="absolute right-4 top-4 rounded-lg p-2 text-white/80 transition hover:bg-white/20 hover:text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 text-3xl shadow-lg">
                        👁</div>
                    <p class="mt-5 text-xs font-black uppercase tracking-[0.22em] text-white/75">Super Admin View Mode
                    </p>
                    <h2 class="mt-1 text-2xl font-black">Mess-এ প্রবেশ করেছেন</h2>
                </div>
                <div class="space-y-3 px-6 py-6 text-slate-700">
                    <p class="text-sm leading-6">আপনি এখন
                        <strong>{{ session('impersonation_popup.mess_name') }}</strong> mess-এর dashboard দেখছেন।</p>
                    <div class="rounded-2xl bg-orange-50 px-4 py-3 text-sm text-orange-900">
                        <strong>Manager:</strong> {{ session('impersonation_popup.manager_name') }}
                    </div>
                    <p class="text-xs text-slate-500">কাজ শেষে উপরের “Return to Super Admin” button ব্যবহার করে ফিরে
                        আসুন।</p>
                    <button type="button" id="continue-impersonation"
                        class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-orange-600">Continue
                        to Dashboard</button>
                </div>
            </div>
        </div>
        <script>
            (() => {
                const popup = document.getElementById('impersonation-popup');
                const close = () => popup?.remove();
                document.getElementById('close-impersonation-popup')?.addEventListener('click', close);
                document.getElementById('continue-impersonation')?.addEventListener('click', close);
                popup?.addEventListener('click', event => {
                    if (event.target === popup) close();
                });
            })();
        </script>
    @endif


    {{-- Impersonation Banner --}}
    @if (session('impersonate_by'))
        <div
            class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-amber-500 to-orange-500 text-white px-6 py-2.5 flex items-center justify-between shadow-lg shadow-amber-500/30">
            <div class="flex items-center gap-3 text-sm font-semibold">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span>Super Admin view mode</span>
            </div>
            <form method="POST" action="{{ route('stop.impersonating') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white text-xs font-bold px-4 py-1.5 rounded-full transition-all border border-white/30 hover:border-white/50">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    Return to Super Admin
                </button>
            </form>
        </div>
    @endif

    <div
        class="flex overflow-hidden bg-slate-50 {{ session('impersonate_by') ? 'h-[calc(100vh-44px)] mt-[44px]' : 'h-screen' }}">
        <!-- Sidebar -->
        <aside
            class="w-72 shrink-0 bg-slate-900 text-slate-200 flex flex-col transition-all duration-300 relative z-20 shadow-2xl">
            <!-- decorative gradient -->
            <div
                class="absolute inset-0 bg-gradient-to-b from-slate-900 via-slate-900 to-emerald-950/20 pointer-events-none">
            </div>

            <div class="relative z-10 flex flex-col h-full">
                <div class="border-b border-white/10 p-6 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-400">Admin
                            Panel</div>
                        <div class="text-xl font-black text-white tracking-tight">Mess Manager</div>
                    </div>
                </div>

                @php
                    $todayDate = now()->toDateString();
                    $weekStart = now()->startOfWeek()->toDateString();
                    $currentMonth = now()->format('Y-m');
                @endphp
                <nav class="flex-1 space-y-1.5 p-4 overflow-y-auto custom-scrollbar">
                    <a href="{{ route('dashboard') }}"
                        class="group flex items-center gap-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 px-4 py-3.5 font-semibold text-emerald-400 shadow-inner transition-all hover:bg-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('mess.settings') }}"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3.5 text-slate-400 transition-all hover:bg-white/5 hover:text-white">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-emerald-400 transition-colors"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V7zm4 4h8m-8 4h5" />
                        </svg>
                        <span>Badge Setup</span>
                    </a>
                    <a href="{{ route('menus.weekly') }}"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3.5 text-slate-400 transition-all hover:bg-white/5 hover:text-white">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-emerald-400 transition-colors"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                        </svg>
                        <span>Weekly Menu</span>
                    </a>
                    <a href="{{ route('members.create') }}"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3.5 text-slate-400 transition-all hover:bg-white/5 hover:text-white">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-emerald-400 transition-colors"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Members</span>
                    </a>
                    <a href="{{ route('meals.create') }}"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3.5 text-slate-400 transition-all hover:bg-white/5 hover:text-white">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-emerald-400 transition-colors"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        <span>Meals</span>
                    </a>
                    <a href="{{ route('expenses.create') }}"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3.5 text-slate-400 transition-all hover:bg-white/5 hover:text-white">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-emerald-400 transition-colors"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>Expenses</span>
                    </a>
                    <a href="{{ route('payments.create') }}"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3.5 text-slate-400 transition-all hover:bg-white/5 hover:text-white">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-emerald-400 transition-colors"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>Payments</span>
                    </a>

                    <div class="rounded-2xl border border-slate-700 bg-slate-800/50 p-2.5">
                        <div class="mb-2 px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                            Reports
                        </div>
                        <div class="space-y-1.5">
                            <a href="{{ route('reports.expenses', ['type' => 'monthly', 'month' => $currentMonth]) }}"
                                class="flex items-center justify-between rounded-xl bg-orange-500/10 px-3 py-2 text-sm font-medium text-orange-200 transition hover:bg-orange-500/20 hover:text-white">
                                <span>Expense Report</span>
                                <span
                                    class="rounded bg-orange-500/20 px-1.5 py-0.5 text-[10px] font-bold text-orange-200">Edit</span>
                            </a>
                            <a href="{{ route('reports.index', ['type' => 'daily', 'date' => $todayDate]) }}"
                                class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-700/70 hover:text-white">
                                <span>Daily Report</span>
                                <span
                                    class="rounded bg-sky-500/10 px-1.5 py-0.5 text-[10px] font-bold text-sky-300">Date</span>
                            </a>
                            <a href="{{ route('reports.index', ['type' => 'weekly', 'week_start' => $weekStart]) }}"
                                class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-700/70 hover:text-white">
                                <span>Weekly Report</span>
                                <span
                                    class="rounded bg-violet-500/10 px-1.5 py-0.5 text-[10px] font-bold text-violet-300">Week</span>
                            </a>
                            <a href="{{ route('reports.index', ['type' => 'monthly', 'month' => $currentMonth]) }}"
                                class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-700/70 hover:text-white">
                                <span>Monthly Report</span>
                                <span
                                    class="rounded bg-emerald-500/10 px-1.5 py-0.5 text-[10px] font-bold text-emerald-300">Month</span>
                            </a>
                        </div>
                    </div>
                </nav>

                <div class="p-4 border-t border-white/10">
                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4 backdrop-blur-sm">
                        <div class="text-[10px] uppercase tracking-[0.25em] text-slate-400">Current Month
                        </div>
                        <div class="mt-1 text-lg font-bold text-white">
                            {{ date('F Y', strtotime($selectedMonth)) }}
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-100 relative">

            <!-- Top Navigation -->
            <header
                class="h-20 flex-shrink-0 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 px-8 flex items-center justify-between sticky top-0 z-10 shadow-sm shadow-slate-200/50">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-bold text-slate-800">Overview</h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                        <div
                            class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold shadow-inner">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="hidden md:block">
                            <div class="text-sm font-bold text-slate-700">
                                {{ auth()->user()->name ?? 'Admin' }}</div>
                            <div class="text-xs text-slate-500">
                                {{ auth()->user()->email ?? 'admin@mess.com' }}</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="ml-2">
                            @csrf
                            <button type="submit"
                                class="p-2 text-slate-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50"
                                title="Logout">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                        <a href="{{ route('account.password.edit') }}"
                            class="rounded-lg p-2 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600"
                            title="Change password">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-3 lg:p-4 custom-scrollbar">

                <!-- Page Header -->
                <div
                    class="mb-4 flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between bg-white p-4 rounded-2xl shadow-sm border border-slate-200/60 relative overflow-hidden">
                    <div
                        class="absolute right-0 top-0 w-64 h-64 bg-emerald-50 rounded-full blur-3xl -z-10 translate-x-1/2 -translate-y-1/2">
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-600 mb-1">
                            Summary</p>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                            {{ auth()->user()->mess?->name ?? 'Mess Dashboard' }}</h1>
                    </div>

                    <form method="GET" class="flex items-center gap-3 relative z-10">
                        <label class="text-sm font-semibold text-slate-500">Select Month</label>
                        <input type="month" name="month" value="{{ $selectedMonth }}"
                            class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 transition-all outline-none">
                        <button type="submit"
                            class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-emerald-500/30 hover:bg-emerald-500 hover:shadow-emerald-500/50 transition-all">Apply</button>
                    </form>
                </div>

                @if ($menuOverviewVisible)
                    <section class="mb-4 grid gap-3 md:grid-cols-2">
                        <div
                            class="relative overflow-hidden rounded-2xl border border-emerald-200 bg-emerald-50 p-3 shadow-sm">
                            <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-emerald-100"></div>
                            <div class="relative flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-700">আজকের
                                        মেনু
                                    </p>
                                    <h2 class="mt-1 text-base font-black text-slate-900">
                                        {{ $menuTodayDate->translatedFormat('l, d M') }}</h2>
                                    <p class="mt-2 whitespace-pre-line text-xs font-semibold leading-5 text-slate-700">
                                        {{ $menuToday?->menu ?: 'মেনু সেট করা হয়নি।' }}</p>
                                    @if ($menuToday?->market_person || $menuToday?->market_condition)
                                        <div class="mt-2 border-t border-emerald-200 pt-2 text-xs text-slate-700">
                                            <p><strong>বাজার:</strong>
                                                {{ $menuToday?->market_person ?: 'নির্ধারিত নয়' }}</p>
                                            @if ($menuToday?->market_condition)
                                                <p class="mt-1"><strong>শর্ত:</strong>
                                                    {{ $menuToday->market_condition }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <span class="rounded-xl bg-white px-3 py-2 text-xl shadow-sm">🍽</span>
                            </div>
                        </div>
                        <div
                            class="relative overflow-hidden rounded-2xl border border-sky-200 bg-sky-50 p-3 shadow-sm">
                            <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-sky-100"></div>
                            <div class="relative flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.2em] text-sky-700">আগামীকালের
                                        মেনু
                                    </p>
                                    <h2 class="mt-1 text-base font-black text-slate-900">
                                        {{ $menuTomorrowDate->translatedFormat('l, d M') }}</h2>
                                    <p class="mt-2 whitespace-pre-line text-xs font-semibold leading-5 text-slate-700">
                                        {{ $menuTomorrow?->menu ?: 'মেনু সেট করা হয়নি।' }}</p>
                                    @if ($menuTomorrow?->market_person || $menuTomorrow?->market_condition)
                                        <div class="mt-2 border-t border-sky-200 pt-2 text-xs text-slate-700">
                                            <p><strong>বাজার:</strong>
                                                {{ $menuTomorrow?->market_person ?: 'নির্ধারিত নয়' }}</p>
                                            @if ($menuTomorrow?->market_condition)
                                                <p class="mt-1"><strong>শর্ত:</strong>
                                                    {{ $menuTomorrow->market_condition }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <span class="rounded-xl bg-white px-3 py-2 text-xl shadow-sm">🍴</span>
                            </div>
                        </div>
                        <div class="md:col-span-2 -mt-1">
                            <a href="{{ route('menus.weekly') }}"
                                class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-700">Edit
                                Weekly Menu</a>
                        </div>
                    </section>
                @endif

                <div class="mb-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <div
                        class="rounded-2xl bg-white p-3 shadow-sm border border-slate-200/60 relative overflow-hidden group hover:border-orange-300 transition-all">
                        <div
                            class="absolute right-0 top-0 w-24 h-24 bg-orange-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110">
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-lg">
                                ৳
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-500">Total Expense</div>
                                <div class="mt-1 text-xl font-black text-slate-900 tracking-tight">
                                    {{ number_format($totalExpenses, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-2xl bg-white p-3 shadow-sm border border-slate-200/60 relative overflow-hidden group hover:border-sky-300 transition-all">
                        <div
                            class="absolute right-0 top-0 w-24 h-24 bg-sky-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110">
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-lg">
                                🍽️
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-500">Total Meals</div>
                                <div class="mt-1 text-xl font-black text-slate-900 tracking-tight">
                                    {{ $totalMeals }}</div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-2xl bg-white p-3 shadow-sm border border-slate-200/60 relative overflow-hidden group hover:border-emerald-300 transition-all">
                        <div
                            class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110">
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg">
                                💳
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-500">Total Payment</div>
                                <div class="mt-1 text-xl font-black text-slate-900 tracking-tight">
                                    {{ number_format($totalPayments, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-2xl bg-white p-3 shadow-sm border border-slate-200/60 relative overflow-hidden group hover:border-indigo-300 transition-all">
                        <div
                            class="absolute right-0 top-0 w-24 h-24 bg-indigo-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110">
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg">
                                📊
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-500">Meal Rate</div>
                                <div class="mt-1 text-xl font-black text-slate-900 tracking-tight">
                                    ৳{{ number_format($mealRate, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Month-over-Month Meal Cost Comparison & Analysis --}}
                <div
                    class="mb-4 rounded-2xl border border-indigo-100 bg-gradient-to-br from-white via-indigo-50/30 to-sky-50/40 p-4 shadow-sm ring-1 ring-slate-200/70 relative overflow-hidden">
                    <div class="pointer-events-none absolute -right-12 -top-12 h-40 w-40 rounded-full bg-indigo-200/20 blur-2xl"
                        aria-hidden="true"></div>
                    <div class="pointer-events-none absolute -left-12 -bottom-12 h-40 w-40 rounded-full bg-emerald-200/20 blur-2xl"
                        aria-hidden="true"></div>

                    <div
                        class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-indigo-100/80 pb-5">
                        <div class="flex items-start gap-3.5">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white font-black text-xl shadow-md shadow-indigo-600/20">
                                📈
                            </div>
                            <div>
                                <div
                                    class="inline-flex items-center gap-1.5 rounded-full bg-indigo-100 px-2.5 py-0.5 text-[11px] font-extrabold uppercase tracking-wide text-indigo-800">
                                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-600"></span>
                                    মাসিক তুলনামূলক এনালাইসিস
                                </div>
                                <h3 class="mt-1 text-lg font-black text-slate-900 tracking-tight">
                                    মিল খরচের তুলনা: {{ $mealComparison['currentMonthName'] }} বনাম
                                    {{ $mealComparison['prevMonthName'] }}
                                </h3>
                            </div>
                        </div>

                        @if ($mealComparison['hasPrevData'])
                            @if ($mealComparison['mealExpenseDiff'] < 0)
                                <div
                                    class="inline-flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-800 shadow-sm">
                                    <span
                                        class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-white text-xs font-black">↓</span>
                                    <div>
                                        <span class="block text-xs font-semibold text-emerald-600">আগের মাসের
                                            চেয়ে</span>
                                        <span
                                            class="text-emerald-900 font-black">৳{{ number_format(abs($mealComparison['mealExpenseDiff']), 2) }}
                                            কম ({{ abs($mealComparison['mealExpensePct']) }}% সাশ্রয়)</span>
                                    </div>
                                </div>
                            @elseif ($mealComparison['mealExpenseDiff'] > 0)
                                <div
                                    class="inline-flex items-center gap-2 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-bold text-rose-800 shadow-sm">
                                    <span
                                        class="flex h-6 w-6 items-center justify-center rounded-full bg-rose-500 text-white text-xs font-black">↑</span>
                                    <div>
                                        <span class="block text-xs font-semibold text-rose-600">আগের মাসের চেয়ে</span>
                                        <span
                                            class="text-rose-900 font-black">৳{{ number_format($mealComparison['mealExpenseDiff'], 2) }}
                                            বেশি (+{{ abs($mealComparison['mealExpensePct']) }}%)</span>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700">
                                    <span>⚖️</span>
                                    <span>পূর্ববর্তী মাসের সমান খরচ</span>
                                </div>
                            @endif
                        @else
                            <div
                                class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-bold text-slate-500">
                                <span>ℹ️</span>
                                <span>পূর্ববর্তী মাসের ডেটা পাওয়া যায়নি</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 grid gap-4 sm:grid-cols-3">
                        {{-- 1. Total Meal Expense Comparison --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">মিল খরচ
                                    (Grocery/Bazaar)</span>
                                <span
                                    class="rounded-md bg-orange-100 px-2 py-0.5 text-[10px] font-bold text-orange-700">খাত</span>
                            </div>
                            <div class="mt-2 flex items-baseline justify-between gap-2">
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold">চলতি মাস</p>
                                    <p class="text-xl font-black text-slate-900">
                                        ৳{{ number_format($mealComparison['currentMealExpense'], 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-400 font-semibold">পূর্বের মাস</p>
                                    <p class="text-sm font-bold text-slate-600">
                                        ৳{{ number_format($mealComparison['prevMealExpense'], 2) }}</p>
                                </div>
                            </div>
                            <div
                                class="mt-3 border-t border-slate-100 pt-2 flex items-center justify-between text-xs font-semibold">
                                <span class="text-slate-500">পার্থক্য:</span>
                                @if ($mealComparison['hasPrevData'])
                                    <span
                                        class="{{ $mealComparison['mealExpenseDiff'] <= 0 ? 'text-emerald-700 font-black' : 'text-rose-700 font-black' }}">
                                        {{ $mealComparison['mealExpenseDiff'] > 0 ? '+' : '' }}৳{{ number_format($mealComparison['mealExpenseDiff'], 2) }}
                                        ({{ $mealComparison['mealExpenseDiff'] > 0 ? 'বেশি' : ($mealComparison['mealExpenseDiff'] < 0 ? 'কম' : 'সমান') }})
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </div>
                        </div>

                        {{-- 2. Meal Rate Comparison --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">মিল রেট (Per
                                    Meal Rate)</span>
                                <span
                                    class="rounded-md bg-indigo-100 px-2 py-0.5 text-[10px] font-bold text-indigo-700">হার</span>
                            </div>
                            <div class="mt-2 flex items-baseline justify-between gap-2">
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold">চলতি মাস</p>
                                    <p class="text-xl font-black text-slate-900">
                                        ৳{{ number_format($mealComparison['currentMealRate'], 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-400 font-semibold">পূর্বের মাস</p>
                                    <p class="text-sm font-bold text-slate-600">
                                        ৳{{ number_format($mealComparison['prevMealRate'], 2) }}</p>
                                </div>
                            </div>
                            <div
                                class="mt-3 border-t border-slate-100 pt-2 flex items-center justify-between text-xs font-semibold">
                                <span class="text-slate-500">পার্থক্য:</span>
                                @if ($mealComparison['hasPrevData'])
                                    <span
                                        class="{{ $mealComparison['mealRateDiff'] <= 0 ? 'text-emerald-700 font-black' : 'text-rose-700 font-black' }}">
                                        {{ $mealComparison['mealRateDiff'] > 0 ? '+' : '' }}৳{{ number_format($mealComparison['mealRateDiff'], 2) }}
                                        ({{ $mealComparison['mealRateDiff'] > 0 ? 'বেশি' : ($mealComparison['mealRateDiff'] < 0 ? 'সাশ্রয়ী' : 'সমান') }})
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </div>
                        </div>

                        {{-- 3. Total Meals Comparison --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">মোট মিল সংখ্যা
                                    (Total Meals)</span>
                                <span
                                    class="rounded-md bg-sky-100 px-2 py-0.5 text-[10px] font-bold text-sky-700">সংখ্যা</span>
                            </div>
                            <div class="mt-2 flex items-baseline justify-between gap-2">
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold">চলতি মাস</p>
                                    <p class="text-xl font-black text-slate-900">
                                        {{ number_format($mealComparison['currentTotalMeals'], 1) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-400 font-semibold">পূর্বের মাস</p>
                                    <p class="text-sm font-bold text-slate-600">
                                        {{ number_format($mealComparison['prevTotalMeals'], 1) }}</p>
                                </div>
                            </div>
                            <div
                                class="mt-3 border-t border-slate-100 pt-2 flex items-center justify-between text-xs font-semibold">
                                <span class="text-slate-500">পার্থক্য:</span>
                                @if ($mealComparison['hasPrevData'])
                                    <span class="text-slate-700 font-black">
                                        {{ $mealComparison['totalMealsDiff'] > 0 ? '+' : '' }}{{ number_format($mealComparison['totalMealsDiff'], 1) }}
                                        টি
                                        ({{ $mealComparison['totalMealsDiff'] > 0 ? 'বেশি' : ($mealComparison['totalMealsDiff'] < 0 ? 'কম' : 'সমান') }})
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Smart Insight Bar --}}
                    <div
                        class="mt-4 rounded-xl border border-indigo-200/60 bg-white/80 p-3.5 text-xs text-slate-700 flex items-center gap-2">
                        <span class="text-base shrink-0">💡</span>
                        <div class="leading-relaxed">
                            @if ($mealComparison['hasPrevData'])
                                @if ($mealComparison['mealExpenseDiff'] < 0)
                                    পূর্ববর্তী মাস <strong>({{ $mealComparison['prevMonthName'] }})</strong>-এর তুলনায়
                                    চলতি মাসে মিল খরচ
                                    <strong>৳{{ number_format(abs($mealComparison['mealExpenseDiff']), 2) }}
                                        ({{ abs($mealComparison['mealExpensePct']) }}%) সাশ্রয়</strong> হয়েছে এবং মিল
                                    রেট <strong>৳{{ number_format(abs($mealComparison['mealRateDiff']), 2) }}</strong>
                                    কমেছে।
                                @elseif ($mealComparison['mealExpenseDiff'] > 0)
                                    পূর্ববর্তী মাস <strong>({{ $mealComparison['prevMonthName'] }})</strong>-এর তুলনায়
                                    চলতি মাসে মিল খরচ
                                    <strong>৳{{ number_format($mealComparison['mealExpenseDiff'], 2) }}
                                        ({{ abs($mealComparison['mealExpensePct']) }}%) বৃদ্ধি</strong> পেয়েছে। মিল
                                    সংখ্যা <strong>{{ abs($mealComparison['totalMealsDiff']) }} টি
                                        {{ $mealComparison['totalMealsDiff'] > 0 ? 'বেশি' : 'কম' }}</strong> হয়েছে।
                                @else
                                    পূর্ববর্তী মাস <strong>({{ $mealComparison['prevMonthName'] }})</strong> এবং চলতি
                                    মাসে মিলের মোট খরচ সমান রয়েছে।
                                @endif
                            @else
                                পূর্ববর্তী মাস <strong>({{ $mealComparison['prevMonthName'] }})</strong>-এর কোনো মিল বা
                                খরচের ডেটা পাওয়া যায়নি। পরবর্তী মাসগুলোতে সিস্টেম স্বয়ংক্রিয়ভাবে বিস্তারিত তুলনা
                                প্রদর্শন করবে।
                            @endif
                        </div>
                    </div>
                </div>

                @if (
                    $totalBalanceWarningThreshold !== null &&
                        $totalBalanceWarningMessage &&
                        $totalNetBalance < (float) $totalBalanceWarningThreshold)
                    <div
                        class="warning-pulse mb-8 rounded-3xl border border-red-300 bg-red-50 p-5 text-red-950 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 text-xl">
                                !</div>
                            <div>
                                <h2 class="font-black">Warning: Total Mess Balance</h2>
                                <p class="mt-1 text-sm font-semibold">{{ $totalBalanceWarningMessage }}</p>
                                <p class="mt-2 text-xs font-bold">Net Deposit Balance:
                                    ৳{{ number_format($totalNetBalance, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <section class="mess-live-scene mb-8 rounded-3xl border border-orange-200 shadow-sm"
                    aria-label="Live mess cooking scene">
                    <div class="absolute left-5 top-4 z-10">
                        <p class="text-[10px] font-black uppercase tracking-[0.25em] text-orange-700">Live Mess</p>
                        <p class="mt-1 text-sm font-bold text-orange-950">
                            {{ auth()->user()->mess?->dining_scene_message ?: 'রান্না চলছে, সবাই টেবিলে' }}</p>
                    </div>
                    <div class="scene-cook" aria-hidden="true">🧑‍🍳</div>
                    <div class="scene-pot" aria-hidden="true"></div>
                    <div class="scene-fire" aria-hidden="true"></div>
                    <div class="scene-steam" aria-hidden="true"></div>
                    <div class="scene-spoon" aria-hidden="true"></div>
                    <div class="scene-table" aria-hidden="true"></div>
                    <div class="scene-bowl" aria-hidden="true"></div>
                    <div class="scene-diner one" aria-hidden="true">🧑🏻‍🦱</div>
                    <div class="scene-diner two" aria-hidden="true">👩🏽‍🦱</div>
                    <div class="scene-diner three" aria-hidden="true">🧔🏾</div>
                </section>

                <div class="mb-8 grid grid-cols-1 gap-3 sm:flex sm:flex-wrap sm:gap-4">
                    <a href="{{ route('members.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-slate-900/20 hover:bg-slate-800 hover:shadow-slate-900/40 transition-all hover:-translate-y-0.5 sm:px-6">
                        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Member
                    </a>
                    <a href="{{ route('meals.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-600/30 hover:bg-emerald-500 hover:shadow-emerald-600/50 transition-all hover:-translate-y-0.5 sm:px-6">
                        <svg class="w-5 h-5 text-emerald-200" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Meal
                    </a>
                    <a href="{{ route('meals.bulk.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-teal-600 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-teal-600/30 hover:bg-teal-500 transition-all hover:-translate-y-0.5 sm:px-6">
                        <svg class="w-5 h-5 text-teal-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5V4H2v16h5m10-8H7m10 4H7m3-8H7" />
                        </svg>
                        সবার Meal
                    </a>
                    <a href="{{ route('expenses.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-500 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/30 hover:bg-orange-400 hover:shadow-orange-500/50 transition-all hover:-translate-y-0.5 sm:px-6">
                        <svg class="w-5 h-5 text-orange-200" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Expense
                    </a>
                    <a href="{{ route('payments.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-sky-600/30 hover:bg-sky-500 hover:shadow-sky-600/50 transition-all hover:-translate-y-0.5 sm:px-6">
                        <svg class="w-5 h-5 text-sky-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Payment
                    </a>
                    <a href="{{ route('reports.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-500 transition-all hover:-translate-y-0.5 sm:px-6">
                        <svg class="w-5 h-5 text-indigo-200" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h8l4 4v10a2 2 0 01-2 2z" />
                        </svg>
                        সব Report
                    </a>
                </div>

                <div class="overflow-hidden rounded-3xl border border-slate-200/60 bg-white shadow-sm">
                    <div
                        class="border-b border-slate-200/60 bg-white px-4 py-4 sm:px-6 sm:py-5 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-lg font-black text-slate-800">Member Ledger</h2>
                        <div class="px-3 py-1 bg-slate-100 text-slate-500 text-xs font-bold rounded-lg">
                            {{ count($rows) }} Members</div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse text-sm">
                            <thead class="bg-slate-50 text-left text-slate-500 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Member
                                    </th>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">
                                        Meals
                                    </th>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">
                                        Meal
                                        Cost</th>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">
                                        Shared Cost</th>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">
                                        Opening</th>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">
                                        Total Deposit
                                    </th>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">
                                        Net Deposit Balance
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($rows as $row)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs">
                                                        {{ substr($row['name'], 0, 1) }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <span
                                                            class="block font-bold text-slate-700">{{ $row['name'] }}</span>
                                                        @if (
                                                            $balanceAlertComment &&
                                                                $mess?->balance_alert_threshold !== null &&
                                                                $row['balance'] < (float) $mess->balance_alert_threshold)
                                                            <span
                                                                class="mt-1 inline-flex max-w-[220px] items-center rounded-full bg-amber-100 px-2 py-1 text-[10px] font-bold leading-tight text-amber-800"
                                                                title="{{ $balanceAlertComment }}">
                                                                ! {{ $balanceAlertComment }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('members.edit', $row['id']) }}"
                                                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[11px] font-bold text-slate-600 hover:border-indigo-300 hover:text-indigo-600 transition-all">
                                                        Edit
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-medium text-slate-600">
                                            {{ $row['meals'] }}</td>
                                        <td class="px-6 py-4 text-right font-medium text-slate-600">
                                            ৳{{ number_format($row['meal_cost'], 2) }}</td>
                                        <td class="px-6 py-4 text-right font-medium text-orange-700">
                                            ৳{{ number_format($row['shared_cost'], 2) }}</td>
                                        <td class="px-6 py-4 text-right font-medium text-slate-600">
                                            ৳{{ number_format($row['opening_balance'], 2) }}</td>
                                        <td class="px-6 py-4 text-right font-medium text-slate-600">
                                            ৳{{ number_format($row['deposit_balance'], 2) }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $row['balance'] >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                                ৳{{ number_format($row['balance'], 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 border-t border-slate-200">
                                <tr>
                                    <td class="px-6 py-4 font-black text-slate-800">Total</td>
                                    <td class="px-6 py-4 text-center font-black text-slate-800">
                                        {{ $totalMeals }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-slate-800">
                                        ৳{{ number_format($mealExpenses, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-black text-orange-700">
                                        ৳{{ number_format($sharedExpenses, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-black text-slate-800">
                                        ৳{{ number_format(collect($rows)->sum('deposit_balance'), 2) }}</td>
                                    <td class="px-6 py-4 text-right font-black text-slate-800">
                                        ৳{{ number_format(collect($rows)->sum('balance'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                    <p class="font-bold text-slate-800">এই মাসের Expense Report</p>
                    <p class="mt-1">Meal Cost ৳{{ number_format($mealExpenses, 2) }} meal rate-এর মাধ্যমে হিসাব
                        হয়েছে।</p>
                    <p class="mt-1">Total Deposit-এ Shared Cost ৳{{ number_format($sharedExpenses, 2) }} কমেনি;
                        এটি শুধু Net Deposit Balance থেকে বাদ হয়েছে।</p>
                    <p class="mt-1">Shared Cost ৳{{ number_format($sharedExpenses, 2) }} meal rate-এ যোগ হয়নি;
                        নির্দিষ্ট সদস্যদের balance থেকে আলাদা করে বাদ হয়েছে।</p>
                    <p class="mt-1 font-semibold text-slate-700">সর্বমোট খরচ: ৳{{ number_format($totalExpenses, 2) }}
                    </p>
                </div>

                @if ($inactiveMembers->isNotEmpty())
                    <div class="mt-8 rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div
                            class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                            <div>
                                <h2 class="font-black text-slate-800">Inactive Members</h2>
                                <p class="mt-1 text-xs text-slate-500">এরা report ও নতুন meal হিসাবের বাইরে আছে। আবার
                                    চালু করতে Edit করুন।</p>
                            </div>
                            <span
                                class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ $inactiveMembers->count() }}
                                জন</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach ($inactiveMembers as $inactiveMember)
                                <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                                    <div>
                                        <p class="font-semibold text-slate-700">{{ $inactiveMember->name }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ $inactiveMember->phone ?: 'Phone not added' }}</p>
                                    </div>
                                    <a href="{{ route('members.edit', $inactiveMember) }}"
                                        class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100">
                                        Active করতে Edit করুন
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <x-developer-card />
            </main>
        </div>
    </div>
</body>

</html>
