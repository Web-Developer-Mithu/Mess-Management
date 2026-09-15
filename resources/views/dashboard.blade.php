<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

                <nav class="flex-1 space-y-1.5 p-4 overflow-y-auto custom-scrollbar">
                    <a href="{{ route('dashboard') }}"
                        class="group flex items-center gap-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 px-4 py-3.5 font-semibold text-emerald-400 shadow-inner transition-all hover:bg-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
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
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 lg:p-8 custom-scrollbar">

                <!-- Page Header -->
                <div
                    class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between bg-white p-6 rounded-3xl shadow-sm border border-slate-200/60 relative overflow-hidden">
                    <div
                        class="absolute right-0 top-0 w-64 h-64 bg-emerald-50 rounded-full blur-3xl -z-10 translate-x-1/2 -translate-y-1/2">
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-600 mb-1">
                            Summary</p>
                        <h1 class="text-3xl font-black text-slate-900 tracking-tight">
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

                <div class="mb-8 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    <div
                        class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200/60 relative overflow-hidden group hover:border-orange-300 transition-all">
                        <div
                            class="absolute right-0 top-0 w-24 h-24 bg-orange-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110">
                        </div>
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xl">
                                ৳
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-500">Total Expense</div>
                                <div class="mt-1 text-2xl font-black text-slate-900 tracking-tight">
                                    {{ number_format($totalExpenses, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200/60 relative overflow-hidden group hover:border-sky-300 transition-all">
                        <div
                            class="absolute right-0 top-0 w-24 h-24 bg-sky-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110">
                        </div>
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-xl">
                                🍽️
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-500">Total Meals</div>
                                <div class="mt-1 text-2xl font-black text-slate-900 tracking-tight">
                                    {{ $totalMeals }}</div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200/60 relative overflow-hidden group hover:border-emerald-300 transition-all">
                        <div
                            class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110">
                        </div>
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xl">
                                💳
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-500">Total Payment</div>
                                <div class="mt-1 text-2xl font-black text-slate-900 tracking-tight">
                                    {{ number_format($totalPayments, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200/60 relative overflow-hidden group hover:border-indigo-300 transition-all">
                        <div
                            class="absolute right-0 top-0 w-24 h-24 bg-indigo-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110">
                        </div>
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xl">
                                📊
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-500">Meal Rate</div>
                                <div class="mt-1 text-2xl font-black text-slate-900 tracking-tight">
                                    ৳{{ number_format($mealRate, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($balanceAlertMembers->isNotEmpty() && $balanceAlertComment)
                    <div class="warning-pulse mb-8 rounded-3xl border border-amber-300 bg-amber-50 p-5 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-xl">
                                !</div>
                            <div class="min-w-0">
                                <h2 class="font-black text-amber-950">Warning: Member Balance Alert</h2>
                                <p class="mt-1 text-sm font-semibold text-amber-900">{{ $balanceAlertComment }}</p>
                                <p class="mt-2 text-xs text-amber-800">
                                    নিচের {{ $balanceAlertMembers->count() }} জন সদস্যের balance
                                    ৳{{ number_format((float) auth()->user()->mess->balance_alert_threshold, 2) }}-এর
                                    নিচে:
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($balanceAlertMembers as $alertMember)
                                        <span
                                            class="rounded-full bg-white px-3 py-1 text-xs font-bold text-amber-900 ring-1 ring-amber-200">
                                            {{ $alertMember['name'] }}:
                                            ৳{{ number_format($alertMember['balance'], 2) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

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
                                                    <span class="font-bold text-slate-700">{{ $row['name'] }}</span>
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
            </main>
        </div>
    </div>
</body>

</html>
