<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-3xl px-4 py-10">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-emerald-600">Super Admin</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Site Branding Settings</h1>
            </div>
            <a href="{{ route('superadmin.dashboard') }}"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back</a>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('superadmin.site.settings.update') }}" enctype="multipart/form-data"
            class="space-y-5 rounded-2xl bg-white p-6 shadow">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold">Brand Name</label>
                    <input type="text" name="brand_name" value="{{ old('brand_name', $settings->brand_name) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Brand Logo</label>
                    <input type="file" name="brand_logo" accept="image/jpeg,image/png,image/webp"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <p class="mt-1 text-xs text-slate-500">Upload a brand logo. JPG, PNG, or WebP up to 2MB.</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Developer Title</label>
                    <input type="text" name="developer_title"
                        value="{{ old('developer_title', $settings->developer_title) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Developer Name</label>
                    <input type="text" name="developer_name"
                        value="{{ old('developer_name', $settings->developer_name) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Developer Tagline</label>
                    <input type="text" name="developer_tagline"
                        value="{{ old('developer_tagline', $settings->developer_tagline) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $settings->phone) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Developer Photo</label>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <p class="mt-1 text-xs text-slate-500">Upload a real profile photo. JPG, PNG, or WebP up to 2MB.</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Avatar URL</label>
                    <input type="url" name="avatar_url" value="{{ old('avatar_url', $settings->avatar_url) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-semibold">WhatsApp URL</label>
                    <input type="url" name="whatsapp_url" value="{{ old('whatsapp_url', $settings->whatsapp_url) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-semibold">Facebook URL</label>
                    <input type="url" name="facebook_url"
                        value="{{ old('facebook_url', $settings->facebook_url) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-3 font-semibold text-white">Save
                    Settings</button>
                <a href="{{ route('superadmin.dashboard') }}"
                    class="rounded-lg border border-slate-300 px-5 py-3 font-semibold text-slate-700">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>
