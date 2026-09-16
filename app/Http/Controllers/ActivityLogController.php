<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\Mess;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user.mess')
            ->when($request->filled('mess_id'), fn ($q) => $q->where('mess_id', $request->integer('mess_id')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->string('action')))
            ->when($request->filled('model'), fn ($q) => $q->where('model_type', 'like', '%' . $request->string('model') . '%'))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->string('search') . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('details', 'like', $term)
                        ->orWhere('ip_address', 'like', $term)
                        ->orWhere('user_agent', 'like', $term);
                });
            });

        $logs = $query
            ->latest()
            ->paginate(25)
            ->appends($request->query());
        $messes = Mess::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $actions = ActivityLog::query()->distinct()->orderBy('action')->pluck('action');

        return view('activity_logs.index', compact('logs', 'messes', 'users', 'actions'));
    }

    public function restore(ActivityLog $activityLog)
    {
        if ($activityLog->model_type !== Expense::class || ! $activityLog->model_id) {
            return back()->with('error', 'এই ধরনের activity-র জন্য restore support নেই।');
        }

        $expense = Expense::withTrashed()->withoutGlobalScopes()->find($activityLog->model_id);

        if ($expense) {
            $expense->restore();
        } elseif (! empty($activityLog->old_values)) {
            $payload = $activityLog->old_values;
            $payload['mess_id'] = $payload['mess_id'] ?? $activityLog->mess_id ?? auth()->user()?->mess_id;

            Expense::withoutGlobalScopes()->create($payload);
        } else {
            return back()->with('error', 'Restore করার জন্য কোনো historical data নেই।');
        }

        return redirect()->route('superadmin.activity.logs')->with('success', 'Expense record successfully restored from activity log.');
    }
}
