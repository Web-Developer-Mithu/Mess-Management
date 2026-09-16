<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mess</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-2xl px-4 py-10">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-indigo-600">Super Admin</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Edit Mess</h1>
            </div>
            <a href="{{ route('superadmin.dashboard') }}"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back</a>
        </div>

        <x-toast />


        <form method="POST" action="{{ route('superadmin.messes.update', $mess) }}" enctype="multipart/form-data"
            class="space-y-5 rounded-2xl bg-white p-6 shadow">
            @csrf
            @method('PUT')

            @if ($mess->logo)
                <div>
                    <p class="mb-2 text-sm font-semibold">Current Logo</p>
                    <img src="{{ asset('storage/' . $mess->logo) }}" alt="{{ $mess->name }} logo"
                        class="h-20 w-20 rounded-xl object-cover ring-1 ring-slate-200">
                </div>
            @endif

            <div>
                <label class="mb-1 block text-sm font-semibold">Mess Name</label>
                <input type="text" name="name" value="{{ old('name', $mess->name) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Address</label>
                <input type="text" name="address" value="{{ old('address', $mess->address) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Mess Status</label>
                <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="active" @selected(old('status', $mess->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $mess->status) === 'inactive')>Inactive</option>
                </select>
                <p class="mt-1 text-xs text-slate-500">Inactive করলে data থাকবে, শুধু manager login ও কাজ বন্ধ থাকবে।
                </p>
                <label class="mb-1 mt-4 block text-sm font-semibold">Inactive Warning Message</label>
                <textarea name="inactive_message" rows="2" maxlength="1000"
                    placeholder="যেমন: Mess সাময়িকভাবে বন্ধ আছে। Super Admin activate করার পর কাজ শুরু হবে।"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('inactive_message', $mess->inactive_message) }}</textarea>
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <h2 class="font-bold text-amber-900">Warning Settings</h2>
                <p class="mt-1 text-xs text-amber-800">যে সদস্যের balance এই অঙ্কের নিচে নামবে, Mess dashboard-এ
                    comment-টি সবাই দেখবে।</p>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Alert Threshold</label>
                        <input type="number" name="balance_alert_threshold" min="0" step="0.01"
                            value="{{ old('balance_alert_threshold', $mess->balance_alert_threshold) }}"
                            placeholder="যেমন 500" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Total Mess Balance
                            Threshold</label>
                        <input type="number" name="total_balance_warning_threshold" step="0.01"
                            value="{{ old('total_balance_warning_threshold', $mess->total_balance_warning_threshold) }}"
                            placeholder="যেমন 1000" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Total Balance Warning
                            Message</label>
                        <textarea name="total_balance_warning_message" rows="3" maxlength="1000"
                            placeholder="যেমন: Mess-এর মোট balance কমে গেছে।" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('total_balance_warning_message', $mess->total_balance_warning_message) }}</textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Alert Comment</label>
                        <textarea name="balance_alert_comment" rows="3" maxlength="1000"
                            placeholder="যেমন: অনুগ্রহ করে এই মাসের বকেয়া টাকা পরিশোধ করুন।"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('balance_alert_comment', $mess->balance_alert_comment) }}</textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Live Dining Scene Message</label>
                        <textarea name="dining_scene_message" rows="2" maxlength="255" placeholder="যেমন: খাবার দাও, খিদে লেগেছে!"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('dining_scene_message', $mess->dining_scene_message) }}</textarea>
                        <p class="mt-1 text-xs text-slate-500">Manager dashboard-এর animated dining scene-এ এই message
                            দেখাবে।</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">New Logo</label>
                <input type="file" name="logo" accept="image/jpeg,image/png,image/webp"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <p class="mt-1 text-xs text-slate-500">JPG, PNG বা WebP, সর্বোচ্চ 2MB</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white">Save
                    Changes</button>
                <a href="{{ route('superadmin.dashboard') }}"
                    class="rounded-lg border border-slate-300 px-5 py-3 font-semibold text-slate-700">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>
