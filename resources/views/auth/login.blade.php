<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mess Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes float-smoke {
            0% {
                transform: translateY(0) scale(1) rotate(0deg);
                opacity: 0;
            }

            50% {
                opacity: 0.6;
            }

            100% {
                transform: translateY(-150px) scale(1.5) rotate(45deg);
                opacity: 0;
            }
        }

        .smoke-particle {
            position: absolute;
            background: radial-gradient(circle, rgba(255, 100, 50, 0.2) 0%, rgba(200, 50, 20, 0) 70%);
            border-radius: 50%;
            filter: blur(15px);
            animation: float-smoke 6s infinite ease-in;
            z-index: 0;
            pointer-events: none;
        }

        .smoke-1 {
            width: 100px;
            height: 100px;
            bottom: -20px;
            left: 20%;
            animation-delay: 0s;
            animation-duration: 7s;
        }

        .smoke-2 {
            width: 150px;
            height: 150px;
            bottom: -50px;
            left: 50%;
            animation-delay: 2s;
            animation-duration: 8s;
        }

        .smoke-3 {
            width: 120px;
            height: 120px;
            bottom: -30px;
            left: 70%;
            animation-delay: 4s;
            animation-duration: 6s;
        }

        .fire-glow {
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.4), inset 0 0 20px rgba(249, 115, 22, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .input-fire:focus {
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
            border-color: rgba(239, 68, 68, 0.8);
        }

        .login-cooking-scene {
            position: absolute;
            inset: auto 0 0;
            height: 240px;
            overflow: hidden;
            pointer-events: none;
            opacity: .9;
        }

        .login-pot {
            position: absolute;
            left: 50%;
            bottom: 28px;
            width: 180px;
            height: 72px;
            transform: translateX(-50%);
            border-radius: 0 0 45px 45px;
            background: linear-gradient(#374151, #111827 70%);
            border: 3px solid #4b5563;
            box-shadow: 0 18px 22px rgba(0, 0, 0, .5), inset 0 8px 12px rgba(255, 255, 255, .08);
        }

        .login-pot::before {
            content: '';
            position: absolute;
            top: -12px;
            left: -12px;
            right: -12px;
            height: 18px;
            border-radius: 50%;
            background: #1f2937;
            border: 3px solid #6b7280;
            box-shadow: inset 0 3px 5px rgba(255, 255, 255, .12);
        }

        .login-flame {
            position: absolute;
            left: 50%;
            bottom: 3px;
            width: 72px;
            height: 62px;
            transform: translateX(-50%);
            background: radial-gradient(ellipse at bottom, #fef08a 0 12%, #fb923c 35%, #ef4444 62%, transparent 70%);
            filter: blur(2px);
            animation: flame-dance .65s ease-in-out infinite alternate;
        }

        .login-steam {
            position: absolute;
            left: 50%;
            bottom: 104px;
            width: 28px;
            height: 90px;
            border-left: 6px solid rgba(255, 255, 255, .2);
            border-radius: 50%;
            filter: blur(5px);
            animation: steam-rise 3s ease-in-out infinite;
        }

        .login-spoon {
            position: absolute;
            left: calc(50% + 28px);
            bottom: 93px;
            width: 8px;
            height: 92px;
            border-radius: 8px;
            background: #d1d5db;
            transform-origin: bottom;
            animation: stir-pot 2.4s ease-in-out infinite;
        }

        .login-cook {
            position: absolute;
            left: calc(50% - 230px);
            bottom: 27px;
            font-size: 76px;
            filter: drop-shadow(0 12px 8px rgba(0, 0, 0, .45));
            animation: cook-sway 2.8s ease-in-out infinite;
        }

        @keyframes flame-dance {
            to {
                transform: translateX(-50%) scale(.82, 1.15) rotate(3deg);
            }
        }

        @keyframes steam-rise {
            0% {
                opacity: 0;
                transform: translate(-50%, 18px) scale(.7) rotate(-8deg);
            }

            35% {
                opacity: .7;
            }

            100% {
                opacity: 0;
                transform: translate(-30%, -76px) scale(1.5) rotate(14deg);
            }
        }

        @keyframes stir-pot {

            0%,
            100% {
                transform: rotate(18deg);
            }

            50% {
                transform: rotate(-24deg);
            }
        }

        @keyframes cook-sway {

            0%,
            100% {
                transform: rotate(-2deg);
            }

            50% {
                transform: rotate(3deg) translateY(-3px);
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .login-cooking-scene *,
            .smoke-particle {
                animation: none;
            }
        }
    </style>
</head>

<body
    class="min-h-screen bg-neutral-950 text-neutral-200 antialiased relative overflow-hidden flex items-center justify-center selection:bg-red-500 selection:text-white">

    <!-- Background Fire & Smoke Effects -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-red-900/40 to-transparent"></div>
        <div class="smoke-particle smoke-1"></div>
        <div class="smoke-particle smoke-2"></div>
        <div class="smoke-particle smoke-3"></div>
        <div class="login-cooking-scene" aria-hidden="true">
            <div class="login-cook">🧑‍🍳</div>
            <div class="login-flame"></div>
            <div class="login-pot"></div>
            <div class="login-steam"></div>
            <div class="login-spoon"></div>
        </div>
    </div>

    <div class="relative z-10 w-full max-w-md px-6">
        <div class="fire-glow bg-neutral-900/80 backdrop-blur-xl rounded-3xl p-8 sm:p-10">

            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-red-500 to-orange-600 mb-4 shadow-[0_0_20px_rgba(239,68,68,0.6)]">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-black text-white tracking-tight">Mess Manager</h1>
                <p class="mt-2 text-sm text-neutral-400">আপনার সুস্বাদু রান্নার হিসেব রাখুন</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-bold text-neutral-300 mb-2">ইমেইল অ্যাড্রেস</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-neutral-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            autofocus
                            class="input-fire w-full pl-11 pr-4 py-3 bg-neutral-950/50 border border-neutral-800 rounded-xl text-white placeholder-neutral-500 focus:outline-none transition-all duration-300"
                            placeholder="আপনার ইমেইল লিখুন">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-500 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-neutral-300 mb-2">পাসওয়ার্ড</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-neutral-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="input-fire w-full pl-11 pr-4 py-3 bg-neutral-950/50 border border-neutral-800 rounded-xl text-white placeholder-neutral-500 focus:outline-none transition-all duration-300"
                            placeholder="আপনার পাসওয়ার্ড লিখুন">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-orange-500 hover:from-red-500 hover:to-orange-400 text-white font-bold py-3.5 px-6 rounded-xl shadow-[0_4px_15px_rgba(239,68,68,0.4)] hover:shadow-[0_6px_20px_rgba(239,68,68,0.6)] transition-all duration-300 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        লগিন করুন
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-neutral-600 mt-6 font-medium">
            &copy; {{ date('Y') }} Mess Manager. All rights reserved.
        </p>
    </div>
</body>

</html>
