<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mess Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    <x-toast />
    <div class="mx-auto max-w-6xl px-6 py-16">
        <header class="mb-12 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-600">Mess Manager</p>
                <h1 class="mt-2 text-4xl font-bold tracking-tight sm:text-5xl">Smart mess operations for your daily
                    routine</h1>
            </div>
            <a href="{{ route('login') }}"
                class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/30 transition hover:bg-emerald-500">Get
                Started</a>
        </header>

        <main class="grid gap-6 md:grid-cols-3">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-4 inline-flex rounded-xl bg-emerald-100 p-3 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M9 7h6m-6 4h6m-6 4h6M5 21h14a2 2 0 002-2V7l-5-5H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold">Meal Management</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Track daily meals, manage monthly meal sheets, and keep
                    your mess records accurate and transparent.</p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-4 inline-flex rounded-xl bg-orange-100 p-3 text-orange-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 8c-1.657 0-3 1.343-3 3v1h6v-1c0-1.657-1.343-3-3-3zm-7 3c0-4.418 3.582-8 8-8s8 3.582 8 8v1H5v-1zm-1 2h18v4H4v-4z" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold">Cost Tracking</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Monitor expenses, calculate fair monthly costs, and
                    maintain a clean balance for each member.</p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-4 inline-flex rounded-xl bg-sky-100 p-3 text-sky-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75M12 12a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold">Member Management</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Manage members, payments, and mess participation with a
                    simple dashboard built for speed.</p>
            </div>
        </main>

        <section class="mt-12 rounded-3xl bg-slate-900 p-8 text-white shadow-xl shadow-slate-300/25">
            <div class="grid gap-8 md:grid-cols-2 md:items-center">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-300">Why Mess Manager?</p>
                    <h3 class="mt-3 text-3xl font-bold">Built for hostel and shared mess administration</h3>
                </div>
                <ul class="space-y-3 text-sm text-slate-200">
                    <li>• Daily meal tracking and expense control</li>
                    <li>• Monthly summary and settlement reports</li>
                    <li>• Fast dashboard for mess managers and members</li>
                </ul>
            </div>
        </section>

        <x-developer-card />
    </div>
</body>

</html>
