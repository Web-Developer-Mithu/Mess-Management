<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Member;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function create()
    {
        $members = Member::orderBy('name')->get();

        return view('expenses.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'type' => ['required', 'in:meal,fixed'],
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['integer', 'exists:members,id'],
            'member_adjustments' => ['nullable', 'array'],
            'member_adjustments.*' => ['nullable', 'numeric'],
            'member_shares' => ['nullable', 'array'],
            'member_shares.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $memberIds = $validated['member_ids'] ?? [];
        $adjustments = [];

        foreach ($request->input('member_adjustments', []) as $memberId => $value) {
            $memberId = (int) $memberId;
            if (in_array($memberId, $memberIds, true)) {
                $adjustments[$memberId] = (float) $value;
            }
        }

        $memberShares = [];
        foreach ($request->input('member_shares', []) as $memberId => $value) {
            $memberId = (int) $memberId;
            if (in_array($memberId, $memberIds, true)) {
                $memberShares[$memberId] = round((float) $value, 2);
            }
        }

        if ($validated['type'] === 'fixed') {
            if (empty($memberIds)) {
                return back()->withErrors(['member_ids' => 'Shared Cost-এর জন্য অন্তত একজন সদস্য select করুন।'])->withInput();
            }

            if (abs(array_sum($memberShares) - (float) $validated['amount']) > 0.01) {
                return back()->withErrors(['member_shares' => 'সব selected member amount-এর মোট অবশ্যই Total Amount-এর সমান হতে হবে।'])->withInput();
            }
        }

        $expense = Expense::create([
            'date' => $validated['date'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'vendor' => $validated['vendor'] ?? null,
            'note' => $validated['note'] ?? null,
            'type' => $validated['type'],
            'is_fixed' => $validated['type'] === 'fixed',
            'member_ids' => $memberIds,
            'member_adjustments' => $adjustments,
            'member_shares' => $validated['type'] === 'fixed' ? $memberShares : null,
        ]);

        return redirect()->route('dashboard', ['month' => date('Y-m', strtotime($expense->date))])
            ->with('success', 'Expense saved successfully.');
    }
}
