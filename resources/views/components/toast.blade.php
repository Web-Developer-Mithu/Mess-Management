{{-- Toast Container & Core Notification Component --}}
<div id="toast-container"
    class="fixed top-5 right-5 z-[99999] flex flex-col gap-3 pointer-events-none max-w-sm w-full px-4 sm:px-0"
    aria-live="polite" aria-atomic="true"></div>

<style>
    @keyframes toastSlideIn {
        0% {
            opacity: 0;
            transform: translateX(100%) scale(0.95);
        }

        70% {
            transform: translateX(-4px) scale(1.01);
        }

        100% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    @keyframes toastSlideOut {
        0% {
            opacity: 1;
            transform: translateX(0) scale(1);
            max-height: 160px;
            margin-bottom: 0.75rem;
        }

        100% {
            opacity: 0;
            transform: translateX(110%) scale(0.9);
            max-height: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
            overflow: hidden;
        }
    }

    .toast-item {
        pointer-events: auto;
        animation: toastSlideIn 0.38s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        will-change: transform, opacity;
        transition: all 0.25s ease;
    }

    .toast-item.toast-hiding {
        animation: toastSlideOut 0.32s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
    }

    .toast-progress-bar {
        transition: width linear;
    }

    .toast-item:hover .toast-progress-bar {
        animation-play-state: paused !important;
    }
</style>

<script>
    (function() {
        if (window.toast) return;

        const ICONS = {
            success: `
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            `,
            error: `
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            `,
            warning: `
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            `,
            info: `
                <svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            `
        };

        const THEMES = {
            success: {
                badgeBg: 'bg-emerald-100 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60',
                border: 'border-emerald-500/30',
                titleColor: 'text-emerald-950 dark:text-emerald-100',
                barColor: 'bg-emerald-500',
                defaultTitle: 'সফল হয়েছে'
            },
            error: {
                badgeBg: 'bg-rose-100 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/60',
                border: 'border-rose-500/30',
                titleColor: 'text-rose-950 dark:text-rose-100',
                barColor: 'bg-rose-500',
                defaultTitle: 'সমস্যা হয়েছে'
            },
            warning: {
                badgeBg: 'bg-amber-100 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/60',
                border: 'border-amber-500/30',
                titleColor: 'text-amber-950 dark:text-amber-100',
                barColor: 'bg-amber-500',
                defaultTitle: 'সতর্কতা'
            },
            info: {
                badgeBg: 'bg-sky-100 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-800/60',
                border: 'border-sky-500/30',
                titleColor: 'text-sky-950 dark:text-sky-100',
                barColor: 'bg-sky-500',
                defaultTitle: 'তথ্য'
            }
        };

        function getContainer() {
            let c = document.getElementById('toast-container');
            if (!c) {
                c = document.createElement('div');
                c.id = 'toast-container';
                c.className =
                    'fixed top-5 right-5 z-[99999] flex flex-col gap-3 pointer-events-none max-w-sm w-full px-4 sm:px-0';
                document.body.appendChild(c);
            }
            return c;
        }

        function createToast({
            type = 'info',
            title,
            message,
            duration = 4500
        }) {
            const container = getContainer();
            const config = THEMES[type] || THEMES.info;
            const iconSvg = ICONS[type] || ICONS.info;
            const finalTitle = title || config.defaultTitle;

            const el = document.createElement('div');
            el.className =
                `toast-item relative overflow-hidden rounded-2xl border ${config.border} bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl p-4 shadow-xl shadow-slate-900/10 ring-1 ring-black/5 dark:ring-white/10`;
            el.setAttribute('role', 'alert');

            el.innerHTML = `
                <div class="flex items-start gap-3.5">
                    <div class="flex-shrink-0 mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl ${config.badgeBg}">
                        ${iconSvg}
                    </div>
                    <div class="flex-1 min-w-0 pr-6">
                        <h4 class="text-sm font-bold ${config.titleColor} tracking-tight">${finalTitle}</h4>
                        <div class="mt-0.5 text-xs font-medium text-slate-600 dark:text-slate-300 leading-relaxed break-words whitespace-pre-line">${message}</div>
                    </div>
                    <button type="button" aria-label="Close notification" class="toast-close-btn absolute top-3.5 right-3.5 p-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-100 dark:bg-slate-800/80 overflow-hidden rounded-b-2xl">
                    <div class="toast-progress-bar h-full ${config.barColor}" style="width: 100%;"></div>
                </div>
            `;

            container.appendChild(el);

            const progressBar = el.querySelector('.toast-progress-bar');
            let remainingTime = duration;
            let startTime = Date.now();
            let timerId = null;
            let isPaused = false;

            function dismiss() {
                if (el.classList.contains('toast-hiding')) return;
                el.classList.add('toast-hiding');
                setTimeout(() => {
                    el.remove();
                }, 350);
            }

            el.querySelector('.toast-close-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                if (timerId) clearTimeout(timerId);
                dismiss();
            });

            if (duration > 0) {
                progressBar.style.transition = `width ${duration}ms linear`;
                requestAnimationFrame(() => {
                    progressBar.style.width = '0%';
                });

                timerId = setTimeout(dismiss, duration);

                // Pause on hover
                el.addEventListener('mouseenter', () => {
                    if (isPaused) return;
                    isPaused = true;
                    clearTimeout(timerId);
                    const elapsed = Date.now() - startTime;
                    remainingTime = Math.max(0, remainingTime - elapsed);
                    const computedWidth = window.getComputedStyle(progressBar).width;
                    progressBar.style.transition = 'none';
                    progressBar.style.width = computedWidth;
                });

                el.addEventListener('mouseleave', () => {
                    if (!isPaused) return;
                    isPaused = false;
                    startTime = Date.now();
                    progressBar.style.transition = `width ${remainingTime}ms linear`;
                    requestAnimationFrame(() => {
                        progressBar.style.width = '0%';
                    });
                    timerId = setTimeout(dismiss, remainingTime);
                });
            }

            return {
                element: el,
                close: dismiss
            };
        }

        window.toast = {
            show: createToast,
            success: (msg, title = 'সফল হয়েছে', duration = 4500) => createToast({
                type: 'success',
                title,
                message: msg,
                duration
            }),
            error: (msg, title = 'ত্রুটি হয়েছে', duration = 5500) => createToast({
                type: 'error',
                title,
                message: msg,
                duration
            }),
            warning: (msg, title = 'সতর্কতা', duration = 5000) => createToast({
                type: 'warning',
                title,
                message: msg,
                duration
            }),
            info: (msg, title = 'তথ্য', duration = 4500) => createToast({
                type: 'info',
                title,
                message: msg,
                duration
            })
        };
    })();
</script>

{{-- Auto-fire server flashed messages --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            window.toast.success(@json(session('success')));
        @endif

        @if (session('error'))
            window.toast.error(@json(session('error')));
        @endif

        @if (session('warning'))
            window.toast.warning(@json(session('warning')));
        @endif

        @if (session('info'))
            window.toast.info(@json(session('info')));
        @endif

        @if (session('status'))
            window.toast.info(@json(session('status')));
        @endif

        @if (isset($errors) && $errors->any())
            @if ($errors->count() === 1)
                window.toast.error(@json($errors->first()), 'ফর্ম ইনপুট সঠিক নয়');
            @else
                window.toast.error(@json(implode("\n• ", array_merge(['অনুগ্রহ করে নিচের বিষয়গুলো সংশোধন করুন:'], $errors->all()))), 'ফর্ম ভ্যালিডেশন ত্রুটি');
            @endif
        @endif
    });
</script>
