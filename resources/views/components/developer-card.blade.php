@php
    $brand = \App\Models\SiteSetting::getCurrent();
@endphp

<footer class="mt-8 mb-4 w-full px-4 font-sans">
    <div
        class="group relative mx-auto max-w-4xl overflow-hidden rounded-2xl border border-slate-200/80 bg-[#edf4f2] p-3 shadow-[0_10px_30px_rgba(15,23,42,0.08)] transition-all duration-300 hover:-translate-y-0.5 sm:px-4 sm:py-3">
        <div class="relative z-10 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="relative shrink-0">
                    <div
                        class="h-12 w-12 overflow-hidden rounded-xl border-2 border-white bg-white shadow-sm sm:h-14 sm:w-14">
                        <img src="{{ $brand->brand_logo_path ? asset('storage/' . $brand->brand_logo_path) : ($brand->brand_logo_url ?: asset('images/default-brand-logo.png')) }}"
                            alt="{{ $brand->brand_name ?: 'Mess Manager' }}" class="h-full w-full object-cover"
                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">

                        <div
                            class="hidden flex h-full w-full items-center justify-center bg-slate-900 text-xs font-black text-white">
                            {{ strtoupper(substr($brand->brand_name ?? 'MM', 0, 2)) }}
                        </div>
                    </div>
                </div>

                <div class="relative shrink-0">
                    <div
                        class="h-12 w-12 overflow-hidden rounded-xl border-2 border-white bg-white shadow-sm sm:h-14 sm:w-14">
                        <img src="{{ $brand->avatar_path ? asset('storage/' . $brand->avatar_path) : ($brand->avatar_url ?: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80') }}"
                            alt="{{ $brand->developer_name }}" class="h-full w-full object-cover"
                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">

                        <div
                            class="hidden flex h-full w-full items-center justify-center text-sm font-black text-slate-700">
                            {{ strtoupper(substr($brand->developer_name ?? 'MR', 0, 2)) }}
                        </div>
                    </div>

                    <span
                        class="absolute -bottom-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-500 text-[8px] font-black text-white ring-2 ring-[#edf4f2]"
                        title="Active Developer">
                        ✓
                    </span>
                </div>

                <div class="min-w-0">
                    <div
                        class="mb-1 inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-[#dfeee9] px-2.5 py-1 text-[9px] font-bold tracking-wide text-slate-700 sm:text-[10px]">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ $brand->developer_title ?: 'Sponsored & Developed By' }}
                    </div>

                    <h3 class="text-base font-black tracking-tight text-slate-800 sm:text-lg">
                        {{ $brand->developer_name ?: 'Md. Mithu Rahman' }}
                    </h3>

                    <p class="text-[11px] font-medium text-slate-600 sm:text-xs">
                        {{ $brand->developer_tagline ?: 'Junior Software Developer & IT Support Expert' }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if (!empty($brand->phone))
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $brand->phone) }}"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-2.5 py-1.5 text-[10px] font-bold text-slate-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md">
                        <span
                            class="flex h-6 w-6 items-center justify-center rounded-lg bg-[#eafaf4] text-[11px] text-slate-700">📞</span>
                        <span>{{ $brand->phone }}</span>
                    </a>
                @endif

                @if (!empty($brand->whatsapp_url))
                    <a href="{{ $brand->whatsapp_url }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-[#6adbb5] px-2.5 py-1.5 text-[10px] font-bold text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <span
                            class="flex h-6 w-6 items-center justify-center rounded-lg bg-white/15 text-[11px] text-white">💬</span>
                        <span>WhatsApp</span>
                    </a>
                @endif

                @if (!empty($brand->facebook_url))
                    <a href="{{ $brand->facebook_url }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-sky-200 bg-[#eaf5ff] px-2.5 py-1.5 text-[10px] font-bold text-slate-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <span
                            class="flex h-6 w-6 items-center justify-center rounded-lg bg-[#1d9bf0] text-[11px] font-black text-white">f</span>
                        <span>Facebook</span>
                    </a>
                @endif
            </div>
        </div>

        <div
            class="mt-3 flex items-center justify-between border-t border-slate-300/90 pt-2 text-[10px] font-medium text-slate-600 sm:text-[11px]">
            <p>&copy; {{ date('Y') }} <span
                    class="font-bold text-slate-700">{{ $brand->brand_name ?: 'Mess Manager' }}</span>. All rights
                reserved.</p>
            <p class="font-bold text-[#60c9a9]">Crafted with care</p>
        </div>
    </div>
</footer>
