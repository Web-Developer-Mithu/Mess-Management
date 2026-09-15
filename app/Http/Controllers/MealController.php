<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Member;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function create()
    {
        $members = Member::orderBy('name')->get();

        return view('meals.create', compact('members'));
    }

    public function createBulk()
    {
        $members = Member::where('status', 'active')->orderBy('name')->get();

        return view('meals.bulk-create', compact('members'));
    }

    public function storeBulk(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'meal_counts' => ['required', 'array'],
            'meal_counts.*' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $members = Member::where('status', 'active')
            ->whereIn('id', array_keys($validated['meal_counts']))
            ->get();

        foreach ($members as $member) {
            Meal::updateOrCreate(
                [
                    'member_id' => $member->id,
                    'date' => $validated['date'],
                ],
                [
                    'meal_count' => $validated['meal_counts'][$member->id],
                    'note' => $validated['note'] ?? null,
                ]
            );
        }

        return redirect()->route('dashboard', ['month' => date('Y-m', strtotime($validated['date']))])
            ->with('success', $members->count() . ' জন active সদস্যের meal একসাথে সংরক্ষণ হয়েছে।');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'date' => ['required', 'date'],
            'meal_count' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        Meal::create($validated);

        return redirect()->route('dashboard', ['month' => date('Y-m', strtotime($validated['date']))])
            ->with('success', 'Meal entry saved successfully.');
    }
}
