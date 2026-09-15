<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-4xl px-4 py-10">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-emerald-600">Admin Panel</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Add Expense</h1>
            </div>
            <a href="{{ route('dashboard') }}"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back to Dashboard</a>
        </div>

        <form method="POST" action="{{ route('expenses.store') }}" id="expense-form"
            class="space-y-6 rounded-2xl bg-white p-6 shadow">
            @csrf

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold">Date</label>
                    <input type="date" name="date" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold">Expense Type</label>
                    <select name="type" id="expense-type"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                        <option value="meal">Meal Cost (affects meal rate)</option>
                        <option value="fixed">Shared Cost (deducts balance only)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Category</label>
                <input type="text" name="category" required placeholder="Rice, Fish, Oil, Milk, Rent, Utility..."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Total Amount</label>
                <input type="number" step="0.01" name="amount" id="amount" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="mb-3 text-sm font-semibold text-slate-700">Shared Cost Distribution</p>
                <div class="mb-3 flex items-center justify-between gap-3">
                    <label class="text-sm font-semibold">Select Members to Share</label>
                    <span class="text-xs font-medium text-slate-500">Shared Cost-এ প্রত্যেকের amount edit করুন</span>
                </div>

                <div class="grid gap-3 md:grid-cols-2" id="member-share-list">
                    @foreach ($members as $member)
                        <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-3">
                            <input type="checkbox" name="member_ids[]" value="{{ $member->id }}" checked
                                data-member-checkbox
                                class="member-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="min-w-0 flex-1 truncate font-medium text-slate-700">{{ $member->name }}</span>
                            <input type="number" name="member_shares[{{ $member->id }}]" min="0"
                                step="0.01" value="0" data-member-share
                                class="w-28 rounded-lg border border-slate-300 px-2 py-1.5 text-right text-sm">
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 rounded-lg bg-white p-3 text-sm">
                    <div class="flex flex-wrap justify-between gap-2 font-semibold">
                        <span>Allocated Total</span>
                        <span id="allocated-total">৳0.00 / ৳0.00</span>
                    </div>
                    <p id="allocation-message" class="mt-1 text-xs text-slate-500">Total Amount লিখলে selected
                        members-এর amount সমানভাবে বসবে।</p>
                </div>

            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Vendor</label>
                <input type="text" name="vendor" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Note</label>
                <textarea name="note" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2"></textarea>
            </div>

            <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-3 font-semibold text-white">Save
                Expense</button>
        </form>
    </div>

    <script>
        const amountInput = document.getElementById('amount');
        const memberCheckboxes = document.querySelectorAll('.member-checkbox');
        const memberShares = document.querySelectorAll('[data-member-share]');
        const expenseType = document.getElementById('expense-type');
        const allocatedTotal = document.getElementById('allocated-total');
        const allocationMessage = document.getElementById('allocation-message');

        function distributeAmount() {
            const amount = parseFloat(amountInput.value || 0);
            const selectedShares = [...memberShares].filter(input => input.closest('div').querySelector(
                '[data-member-checkbox]').checked);
            const share = selectedShares.length > 0 ? (amount / selectedShares.length).toFixed(2) : '0.00';
            memberShares.forEach(input => {
                const checkbox = input.closest('div').querySelector('[data-member-checkbox]');
                input.value = checkbox.checked ? share : '0.00';
                input.disabled = !checkbox.checked;
            });
            updateAllocation();
        }

        function updateAllocation() {
            const total = [...memberShares].reduce((sum, input) => sum + (input.disabled ? 0 : parseFloat(input.value ||
                0)), 0);
            const amount = parseFloat(amountInput.value || 0);
            allocatedTotal.textContent = `৳${total.toFixed(2)} / ৳${amount.toFixed(2)}`;
            const matches = Math.abs(total - amount) <= 0.01;
            allocationMessage.textContent = matches ? 'Amount allocation ঠিক আছে।' :
                'Selected member amounts-এর মোট Total Amount-এর সমান করুন।';
            allocationMessage.className = `mt-1 text-xs ${matches ? 'text-emerald-600' : 'text-red-600'}`;
        }

        amountInput.addEventListener('input', distributeAmount);
        memberCheckboxes.forEach(cb => cb.addEventListener('change', distributeAmount));
        memberShares.forEach(input => input.addEventListener('input', updateAllocation));
        document.getElementById('expense-form').addEventListener('submit', event => {
            if (expenseType.value === 'fixed') {
                const total = [...memberShares].reduce((sum, input) => sum + (input.disabled ? 0 : parseFloat(input
                    .value || 0)), 0);
                const amount = parseFloat(amountInput.value || 0);
                if (Math.abs(total - amount) > 0.01) {
                    event.preventDefault();
                    updateAllocation();
                    alert('সব selected member amount-এর মোট অবশ্যই Total Amount-এর সমান হতে হবে।');
                }
            }
        });
        distributeAmount();
    </script>
</body>

</html>
