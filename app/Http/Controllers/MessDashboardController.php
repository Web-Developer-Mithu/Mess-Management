<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Meal;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;

class MessDashboardController extends Controller
{
    public function index()
    {
        $selectedMonth = request('month', now()->format('Y-m'));

        $members = Member::where('status', 'active')->with(['meals' => function ($query) use ($selectedMonth) {
            $query->whereMonth('date', date('m', strtotime($selectedMonth)))
                ->whereYear('date', date('Y', strtotime($selectedMonth)));
        }, 'payments' => function ($query) use ($selectedMonth) {
            $query->whereMonth('date', date('m', strtotime($selectedMonth)))
                ->whereYear('date', date('Y', strtotime($selectedMonth)));
        }])->get();
        $inactiveMembers = Member::where('status', 'inactive')->orderBy('name')->get();

        $totalMeals = Meal::whereHas('member', function ($query) {
                $query->where('status', 'active');
            })
            ->whereMonth('date', date('m', strtotime($selectedMonth)))
            ->whereYear('date', date('Y', strtotime($selectedMonth)))
            ->sum('meal_count');

        $allExpenses = Expense::whereMonth('date', date('m', strtotime($selectedMonth)))
            ->whereYear('date', date('Y', strtotime($selectedMonth)))
            ->get();

        $totalExpenses = $allExpenses->sum('amount');

        $totalPayments = Payment::whereHas('member', function ($query) {
                $query->where('status', 'active');
            })
            ->whereMonth('date', date('m', strtotime($selectedMonth)))
            ->whereYear('date', date('Y', strtotime($selectedMonth)))
            ->sum('amount');

        $mealExpenses = $allExpenses->where('type', 'meal')->sum('amount');
        $sharedExpenses = $allExpenses->where('type', 'fixed')->sum('amount');
        $mealRate = $totalMeals > 0 ? round($mealExpenses / $totalMeals, 2) : 0;

        $fixedMemberShares = [];
        $activeMemberIds = $members->pluck('id')->all();
        foreach ($allExpenses->where('is_fixed', true) as $expense) {
            $memberIds = $expense->member_ids ?? $activeMemberIds;
            $memberIds = array_values(array_intersect($memberIds, $activeMemberIds));

            foreach ($memberIds as $memberId) {
                $memberShare = $expense->member_shares[$memberId] ?? null;
                $share = $memberShare !== null
                    ? (float) $memberShare
                    : Expense::memberShareForExpense((float) $expense->amount, $memberIds, $expense->member_adjustments ?? [], $memberId);
                $fixedMemberShares[$memberId] = ($fixedMemberShares[$memberId] ?? 0) + $share;
            }
        }

        $openingBalances = $this->openingBalances($selectedMonth, $activeMemberIds);

        $rows = [];
        foreach ($members as $member) {
            $memberMeals = (float) $member->meals->sum('meal_count');
            $memberMealCost = $memberMeals * $mealRate;
            $memberSharedCost = $fixedMemberShares[$member->id] ?? 0;
            $memberTotalCost = $memberMealCost + $memberSharedCost;
            $memberPayments = (float) $member->payments->sum('amount');
            $openingBalance = $openingBalances[$member->id] ?? 0;
            $balance = $openingBalance + $memberPayments - $memberTotalCost;

            $rows[] = [
                'id' => $member->id,
                'name' => $member->name,
                'meals' => $memberMeals,
                'meal_cost' => $memberMealCost,
                'shared_cost' => $memberSharedCost,
                'payment' => $memberPayments,
                'deposit_balance' => $openingBalance + $memberPayments,
                'opening_balance' => $openingBalance,
                'balance' => $balance,
            ];
        }

        $mess = auth()->user()->mess;
        $totalNetBalance = collect($rows)->sum('balance');
        $balanceAlertMembers = collect($rows)->filter(function (array $row) use ($mess) {
            return $mess?->balance_alert_threshold !== null
                && $row['balance'] < (float) $mess->balance_alert_threshold;
        });

        $messId = auth()->user()->mess_id;

        $settingsRows = Setting::withoutGlobalScopes()
            ->where('mess_id', $messId)
            ->where('month', $selectedMonth)
            ->orderBy('id')
            ->get();

        if ($settingsRows->count() > 1) {
            $duplicateIds = $settingsRows->skip(1)->pluck('id')->all();

            if (! empty($duplicateIds)) {
                Setting::withoutGlobalScopes()->whereIn('id', $duplicateIds)->delete();
            }
        }

        $settings = Setting::withoutGlobalScopes()->updateOrCreate(
            [
                'mess_id' => $messId,
                'month' => $selectedMonth,
            ],
            [
                'meal_rate' => $mealRate > 0 ? $mealRate : 0,
            ]
        );

        return view('dashboard', [
            'selectedMonth' => $selectedMonth,
            'members' => $members,
            'inactiveMembers' => $inactiveMembers,
            'rows' => $rows,
            'totalMeals' => $totalMeals,
            'totalExpenses' => $totalExpenses,
            'mealExpenses' => $mealExpenses,
            'sharedExpenses' => $sharedExpenses,
            'totalPayments' => $totalPayments,
            'mealRate' => $mealRate,
            'settings' => $settings,
            'balanceAlertMembers' => $balanceAlertMembers,
            'balanceAlertComment' => $mess?->balance_alert_comment,
            'totalNetBalance' => $totalNetBalance,
            'totalBalanceWarningThreshold' => $mess?->total_balance_warning_threshold,
            'totalBalanceWarningMessage' => $mess?->total_balance_warning_message,
        ]);
    }

    private function openingBalances(string $selectedMonth, array $activeMemberIds): array
    {
        if (empty($activeMemberIds)) {
            return [];
        }

        $monthStart = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $priorMeals = Meal::whereHas('member', function ($query) {
                $query->where('status', 'active');
            })
            ->where('date', '<', $monthStart->toDateString())
            ->get(['member_id', 'date', 'meal_count']);
        $priorExpenses = Expense::where('date', '<', $monthStart->toDateString())->get();
        $priorPayments = Payment::whereHas('member', function ($query) {
                $query->where('status', 'active');
            })
            ->where('date', '<', $monthStart->toDateString())
            ->get(['member_id', 'amount'])
            ->groupBy('member_id')
            ->map(fn ($payments) => (float) $payments->sum('amount'));

        $mealCountsByMonth = $priorMeals->groupBy(
            fn ($meal) => Carbon::parse($meal->date)->format('Y-m')
        )->map(fn ($meals) => (float) $meals->sum('meal_count'));
        $mealExpensesByMonth = $priorExpenses->where('type', 'meal')->groupBy(
            fn ($expense) => Carbon::parse($expense->date)->format('Y-m')
        )->map(fn ($expenses) => (float) $expenses->sum('amount'));

        $openingBalances = [];
        foreach ($activeMemberIds as $memberId) {
            $openingBalances[$memberId] = $priorPayments[$memberId] ?? 0;
        }

        foreach ($priorMeals as $meal) {
            $month = Carbon::parse($meal->date)->format('Y-m');
            $monthTotalMeals = $mealCountsByMonth[$month] ?? 0;
            $monthRate = $monthTotalMeals > 0
                ? round(($mealExpensesByMonth[$month] ?? 0) / $monthTotalMeals, 2)
                : 0;
            $openingBalances[$meal->member_id] = ($openingBalances[$meal->member_id] ?? 0)
                - ((int) $meal->meal_count * $monthRate);
        }

        foreach ($priorExpenses->where('is_fixed', true) as $expense) {
            $memberIds = $expense->member_ids ?? $activeMemberIds;
            $memberIds = array_values(array_intersect($memberIds, $activeMemberIds));

            foreach ($memberIds as $memberId) {
                $memberShare = $expense->member_shares[$memberId] ?? null;
                $share = $memberShare !== null
                    ? (float) $memberShare
                    : Expense::memberShareForExpense((float) $expense->amount, $memberIds, $expense->member_adjustments ?? [], $memberId);
                $openingBalances[$memberId] = ($openingBalances[$memberId] ?? 0) - $share;
            }
        }

        return $openingBalances;
    }
}
