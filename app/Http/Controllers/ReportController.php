<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Meal;
use App\Models\Member;
use App\Models\Payment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'monthly');
        [$from, $to] = $this->period($request, $type);

        $meals = Meal::with('member')->whereBetween('date', [$from, $to])->get();
        $expenses = Expense::whereBetween('date', [$from, $to])->get();
        $payments = Payment::with('member')->whereBetween('date', [$from, $to])->get();
        $reportMembers = Member::orderBy('name')->get();

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

        return view('reports.index', compact(
            'type', 'from', 'to', 'mealCount', 'mealExpense', 'sharedExpense',
            'totalPayments', 'mealRate', 'members', 'dates', 'sharedCostEntries', 'sharedCostByCategory'
        ));
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
