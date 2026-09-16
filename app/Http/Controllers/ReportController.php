<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Meal;
use App\Models\Member;
use App\Models\Payment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureExpenseColumns();

        $type = $request->input('type', 'monthly');
        $memberId = $request->input('member_id');
        [$from, $to] = $this->period($request, $type);

        $memberOptions = Member::orderBy('name')->get();
        $selectedMember = $memberId ? $memberOptions->firstWhere('id', (int) $memberId) : null;
        $reportMembers = $selectedMember ? collect([$selectedMember]) : $memberOptions;

        $mealsQuery = Meal::with('member')->whereBetween('date', [$from, $to]);
        $expensesQuery = Expense::whereBetween('date', [$from, $to]);
        $paymentsQuery = Payment::with('member')->whereBetween('date', [$from, $to]);

        if ($memberId) {
            $mealsQuery->where('member_id', (int) $memberId);
            $expensesQuery->where(function ($query) use ($memberId) {
                $query->whereJsonContains('member_ids', (int) $memberId)
                    ->orWhere('type', 'meal');
            });
            $paymentsQuery->where('member_id', (int) $memberId);
        }

        $meals = $mealsQuery->get();
        $expenses = $expensesQuery->get();
        $payments = $paymentsQuery->get();

        $mealCount = (float) $meals->sum('meal_count');
        $mealExpense = (float) $expenses->where('type', 'meal')->sum('amount');
        $sharedExpense = (float) $expenses->where('type', 'fixed')->sum('amount');
        $totalPayments = (float) $payments->sum('amount');
        $mealRate = $mealCount > 0 ? round($mealExpense / $mealCount, 2) : 0;
        $dates = collect(CarbonPeriod::create($from, $to))->map(fn (Carbon $date) => $date->toDateString());
        $sharedCostEntries = $expenses->where('type', 'fixed')->map(function (Expense $expense) use ($reportMembers) {
            $memberIds = $expense->member_ids ?? $reportMembers->pluck('id')->all();
            $allocations = collect($memberIds)->mapWithKeys(function ($memberId) use ($expense, $memberIds, $reportMembers) {
                $memberShare = $expense->member_shares[$memberId] ?? null;
                $amount = $memberShare !== null
                    ? (float) $memberShare
                    : Expense::memberShareForExpense((float) $expense->amount, $memberIds, $expense->member_adjustments ?? [], (int) $memberId);
                $member = $reportMembers->firstWhere('id', (int) $memberId);

                return [$member?->name ?? 'Member #' . $memberId => $amount];
            });

            return [
                'date' => $expense->date,
                'category' => $expense->category,
                'vendor' => $expense->vendor,
                'amount' => (float) $expense->amount,
                'member_count' => count($memberIds),
                'allocations' => $allocations,
            ];
        })->values();
        $sharedCostByCategory = $sharedCostEntries->groupBy('category')->map(fn ($entries) => (float) $entries->sum('amount'));

        $members = $reportMembers->map(function (Member $member) use ($reportMembers, $meals, $payments, $expenses, $mealRate) {
            $memberMeals = (float) $meals->where('member_id', $member->id)->sum('meal_count');
            $memberPayments = (float) $payments->where('member_id', $member->id)->sum('amount');
            $shared = 0.0;

            foreach ($expenses->where('is_fixed', true) as $expense) {
                $memberIds = $expense->member_ids ?? $reportMembers->pluck('id')->all();
                if (! in_array($member->id, $memberIds, true)) {
                    continue;
                }

                $memberShare = $expense->member_shares[$member->id] ?? null;
                $shared += $memberShare !== null
                    ? (float) $memberShare
                    : \App\Models\Expense::memberShareForExpense((float) $expense->amount, $memberIds, $expense->member_adjustments ?? [], $member->id);
            }

            $mealCost = $memberMeals * $mealRate;

            return [
                'name' => $member->name,
                'status' => $member->status,
                'meals' => $memberMeals,
                'daily_meals' => $meals->where('member_id', $member->id)->groupBy('date')->map(fn ($entries) => (int) $entries->sum('meal_count')),
                'meal_cost' => $mealCost,
                'shared_cost' => $shared,
                'payments' => $memberPayments,
                'deposit_balance' => $memberPayments,
                'balance' => $memberPayments - $mealCost - $shared,
            ];
        });

        $paymentEntries = $payments->sortBy('date')->map(function (Payment $payment) {
            return [
                'date' => $payment->date,
                'member_name' => $payment->member?->name ?? 'Unknown Member',
                'payment_type' => $payment->payment_type,
                'amount' => (float) $payment->amount,
                'note' => $payment->note,
            ];
        })->values();

        $expenseEntries = $expenses->sortByDesc('date')->values()->map(function (Expense $expense) {
            return [
                'id' => $expense->id,
                'date' => $expense->date,
                'category' => $expense->category,
                'type' => $expense->type ?? 'meal',
                'vendor' => $expense->vendor,
                'note' => $expense->note,
                'amount' => (float) $expense->amount,
                'member_count' => count($expense->member_ids ?? []),
            ];
        });

        return view('reports.index', compact(
            'type', 'from', 'to', 'mealCount', 'mealExpense', 'sharedExpense',
            'totalPayments', 'mealRate', 'members', 'dates', 'sharedCostEntries', 'sharedCostByCategory',
            'memberOptions', 'selectedMember', 'paymentEntries', 'expenseEntries', 'memberId'
        ));
    }

    public function expenses(Request $request)
    {
        $this->ensureExpenseColumns();

        $type = $request->input('type', 'monthly');
        [$from, $to] = $this->period($request, $type);
        $memberId = $request->input('member_id');
        $expenseType = $request->input('expense_type', 'all');

        $memberOptions = Member::orderBy('name')->get();
        $query = Expense::query()->whereBetween('date', [$from, $to]);

        if ($memberId) {
            $query->where(function ($builder) use ($memberId) {
                $builder->whereJsonContains('member_ids', (int) $memberId)
                    ->orWhere('type', 'meal');
            });
        }

        if (in_array($expenseType, ['meal', 'fixed'], true)) {
            $query->where('type', $expenseType);
        }

        $expenses = $query->orderByDesc('date')->orderByDesc('id')->get();
        $totalAmount = (float) $expenses->sum('amount');
        $mealAmount = (float) $expenses->where('type', 'meal')->sum('amount');
        $sharedAmount = (float) $expenses->where('type', 'fixed')->sum('amount');
        $returnUrl = route('reports.expenses', $request->query());

        return view('reports.expenses', compact(
            'type', 'from', 'to', 'memberId', 'memberOptions', 'expenseType',
            'expenses', 'totalAmount', 'mealAmount', 'sharedAmount', 'returnUrl'
        ));
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

    private function period(Request $request, string $type): array
    {
        if ($type === 'daily') {
            $day = Carbon::parse($request->input('date', now()->toDateString()));
            return [$day->toDateString(), $day->toDateString()];
        }

        if ($type === 'weekly') {
            $start = Carbon::parse($request->input('week_start', now()->startOfWeek()->toDateString()))->startOfWeek();
            return [$start->toDateString(), $start->copy()->endOfWeek()->toDateString()];
        }

        $month = Carbon::createFromFormat('Y-m', $request->input('month', now()->format('Y-m')));
        return [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()];
    }
}
