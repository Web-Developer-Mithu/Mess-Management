<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Meal;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\WeeklyMenu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MessDashboardController extends Controller
{
    public function settings()
    {
        $mess = auth()->user()->mess;

        return view('mess.settings', compact('mess'));
    }

    public function updateSettings(Request $request)
    {
        $mess = auth()->user()->mess;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($mess?->logo) {
                Storage::disk('public')->delete($mess->logo);
            }

            $validated['logo'] = $request->file('logo')->store('mess-logos', 'public');
        }

        $mess->update($validated);

        return redirect()->route('dashboard')->with('success', 'Mess badge update successful.');
    }

    public function index()
    {
        if (! auth()->check()) {
            return view('welcome');
        }

        $this->ensureExpenseColumns();

        $selectedMonth = request('month', now()->format('Y-m'));
        $today = Carbon::today();
        $tomorrow = $today->copy()->addDay();
        $weeklyMenus = WeeklyMenu::whereIn('weekday', [$today->dayOfWeek, $tomorrow->dayOfWeek])
            ->get()
            ->keyBy('weekday');
        $menuToday = $weeklyMenus->get($today->dayOfWeek);
        $menuTomorrow = $weeklyMenus->get($tomorrow->dayOfWeek);
        $menuOverviewVisible = collect([$menuToday, $menuTomorrow])->contains(function ($menu) {
            return $menu && ($menu->menu || $menu->market_person || $menu->market_condition);
        });

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

        // Previous Month Comparison Analysis
        $prevMonthDate = Carbon::createFromFormat('Y-m', $selectedMonth)->subMonth();
        $prevMonthStr = $prevMonthDate->format('Y-m');
        $prevMonthName = $prevMonthDate->translatedFormat('F Y') ?: $prevMonthDate->format('F Y');
        $currentMonthName = Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') ?: Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y');

        $prevTotalMeals = (float) Meal::whereHas('member', function ($query) {
                $query->where('status', 'active');
            })
            ->whereMonth('date', $prevMonthDate->month)
            ->whereYear('date', $prevMonthDate->year)
            ->sum('meal_count');

        $prevAllExpenses = Expense::whereMonth('date', $prevMonthDate->month)
            ->whereYear('date', $prevMonthDate->year)
            ->get();

        $prevMealExpenses = (float) $prevAllExpenses->where('type', 'meal')->sum('amount');
        $prevMealRate = $prevTotalMeals > 0 ? round($prevMealExpenses / $prevTotalMeals, 2) : 0;

        $mealExpenseDiff = (float) ($mealExpenses - $prevMealExpenses);
        $mealExpensePct = $prevMealExpenses > 0 ? round(($mealExpenseDiff / $prevMealExpenses) * 100, 1) : null;

        $mealRateDiff = round((float) ($mealRate - $prevMealRate), 2);
        $mealRatePct = $prevMealRate > 0 ? round(($mealRateDiff / $prevMealRate) * 100, 1) : null;

        $totalMealsDiff = (float) ($totalMeals - $prevTotalMeals);

        $mealComparison = [
            'currentMonthName' => $currentMonthName,
            'prevMonthName' => $prevMonthName,
            'prevMonthStr' => $prevMonthStr,
            'currentMealExpense' => (float) $mealExpenses,
            'prevMealExpense' => (float) $prevMealExpenses,
            'mealExpenseDiff' => $mealExpenseDiff,
            'mealExpensePct' => $mealExpensePct,
            'currentMealRate' => (float) $mealRate,
            'prevMealRate' => (float) $prevMealRate,
            'mealRateDiff' => $mealRateDiff,
            'mealRatePct' => $mealRatePct,
            'currentTotalMeals' => (float) $totalMeals,
            'prevTotalMeals' => (float) $prevTotalMeals,
            'totalMealsDiff' => $totalMealsDiff,
            'hasPrevData' => ($prevTotalMeals > 0 || $prevMealExpenses > 0),
        ];

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
            'mess' => $mess,
            'totalNetBalance' => $totalNetBalance,
            'totalBalanceWarningThreshold' => $mess?->total_balance_warning_threshold,
            'totalBalanceWarningMessage' => $mess?->total_balance_warning_message,
            'mealComparison' => $mealComparison,
            'menuToday' => $menuToday,
            'menuTomorrow' => $menuTomorrow,
            'menuTodayDate' => $today,
            'menuTomorrowDate' => $tomorrow,
            'menuOverviewVisible' => $menuOverviewVisible,
        ]);
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
