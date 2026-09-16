<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Member;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::query()->orderByDesc('date')->orderByDesc('id')->paginate(20);

        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $members = Member::orderBy('name')->get();

        return view('expenses.create', compact('members'));
    }

    public function edit(Expense $expense)
    {
        $members = Member::orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'members'));
    }

    private function ensureExpenseColumns(): void
    {
        if (! Schema::hasTable('expenses')) {
            return;
        }

        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'type')) {
                $table->string('type')->default('meal')->after('note');
            }
            if (! Schema::hasColumn('expenses', 'is_fixed')) {
                $table->boolean('is_fixed')->default(false)->after('type');
            }
            if (! Schema::hasColumn('expenses', 'member_ids')) {
                $table->json('member_ids')->nullable()->after('is_fixed');
            }
            if (! Schema::hasColumn('expenses', 'member_adjustments')) {
                $table->json('member_adjustments')->nullable()->after('member_ids');
            }
            if (! Schema::hasColumn('expenses', 'member_shares')) {
                $table->json('member_shares')->nullable()->after('member_adjustments');
            }
            if (! Schema::hasColumn('expenses', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    private function normalizeExpensePayload(Request $request): array
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
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'member_ids' => ['Shared Cost-এর জন্য অন্তত একজন সদস্য select করুন।'],
                ]);
            }

            if (abs(array_sum($memberShares) - (float) $validated['amount']) > 0.01) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'member_shares' => ['সব selected member amount-এর মোট অবশ্যই Total Amount-এর সমান হতে হবে।'],
                ]);
            }
        }

        return [
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
        ];
    }

    public function store(Request $request)
    {
        $this->ensureExpenseColumns();

        try {
            $payload = $this->normalizeExpensePayload($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        $expense = Expense::create($payload);

        return redirect()->route('dashboard', ['month' => date('Y-m', strtotime($expense->date))])
            ->with('success', 'খরচ সফলভাবে সেভ হয়েছে।');
    }

    public function update(Request $request, Expense $expense)
    {
        $this->ensureExpenseColumns();

        try {
            $payload = $this->normalizeExpensePayload($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        $expense->update($payload);

        return $this->expenseRedirect($request, 'খরচ সফলভাবে আপডেট হয়েছে।');
    }

    public function destroy(Request $request, Expense $expense)
    {
        $expense->delete();

        return $this->expenseRedirect($request, 'খরচটি ডিলিট হয়েছে।');
    }

    private function expenseRedirect(Request $request, string $message)
    {
        $returnTo = $request->input('return_to');

        if ($returnTo && str_starts_with($returnTo, url('/'))) {
            return redirect()->to($returnTo)->with('success', $message);
        }

        return redirect()->route('expenses.index')->with('success', $message);
    }

    public function restore(int $expenseId)
    {
        $expense = Expense::withTrashed()->withoutGlobalScopes()->findOrFail($expenseId);
        $expense->restore();

        return redirect()->route('expenses.index')->with('success', 'খরচটি পুনরুদ্ধার করা হয়েছে।');
    }
}
